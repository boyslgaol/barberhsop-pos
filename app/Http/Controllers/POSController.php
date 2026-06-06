<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
class POSController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'cashier'])) {
            abort(403, 'Hanya admin dan kasir yang dapat mengakses halaman ini.');
        }
        return $next($request);
    });
}
    /**
     * Display POS page
     */
    public function index(Request $request)
    {
        // Check if there's queue_id in URL parameter
        if ($request->has('queue_id')) {
            $queue = Queue::find($request->queue_id);
            if ($queue && $queue->status === 'completed' && !$queue->transaction_id) {
                // Store queue data in session
                session([
                    'queue_cart' => [
                        [
                            'id' => $queue->service_id,
                            'name' => $queue->service->name,
                            'price' => (float) $queue->service->price,
                            'from_queue' => true,
                            'queue_id' => $queue->id,
                            'barber_id' => $queue->barber_id
                        ]
                    ],
                    'queue_customer' => [
                        'name' => $queue->customer_name,
                        'phone' => $queue->customer_phone ?? ''
                    ],
                    'queue_id' => $queue->id,
                    'queue_number' => $queue->queue_number,
                    'queue_loaded' => true
                ]);
                
                Log::info('Queue data loaded from URL parameter', [
                    'queue_id' => $queue->id,
                    'queue_number' => $queue->queue_number
                ]);
            }
        }
        
        $services = Service::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();
        
        $categories = \App\Models\Category::where('is_active', true)
            ->with(['services' => function($query) {
                $query->where('is_active', true); // <-- Tambahkan ini untuk filter aktif
            }])
            ->orderBy('sort_order')
            ->get();
        
        $customers = Customer::orderBy('name')->get();
        $barbers = User::where('role', 'barber')->where('is_active', true)->get();
        
        return view('pos.index', compact('services', 'categories', 'customers', 'barbers'));
    }
    public function toggleStatus(Service $service)
    {
        // Toggle status
        $service->is_active = !$service->is_active;
        $service->save();
         // Clear view cache
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        
        // Clear config cache
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        
        $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', 'Layanan "' . $service->name . '" berhasil ' . $status);
        $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        // Clear cache jika ada
        cache()->forget('services_active');
        
        // Log perubahan
        \Log::info('Service status changed', [
            'service_id' => $service->id,
            'service_name' => $service->name,
            'new_status' => $service->is_active
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan "' . $service->name . '" berhasil ' . $status,
                'is_active' => $service->is_active
            ]);
        }
        
        return redirect()->route('services.index')
            ->with('success', 'Layanan "' . $service->name . '" berhasil ' . $status);
    }
    public function getServices()
    {
        $services = Service::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'duration' => $service->duration,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category->name
                ];
            });
        
        return response()->json(['services' => $services]);
    }
    /**
     * Search customer by name or phone
     */
    public function searchCustomer(Request $request)
    {
        $customers = Customer::where('name', 'like', "%{$request->search}%")
            ->orWhere('phone', 'like', "%{$request->search}%")
            ->limit(10)
            ->get();
        
        return response()->json($customers);
    }

    /**
     * Get customer details by ID
     */
    public function getCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    /**
     * Load queue data to POS (via route)
     */
    public function fromQueue(Queue $queue)
    {
        // Check if queue already has transaction
        if ($queue->transaction_id) {
            return redirect()->route('queue.index')
                ->with('error', 'Antrian ini sudah memiliki transaksi.');
        }
        
        // Check queue status
        if ($queue->status !== 'completed') {
            return redirect()->route('queue.index')
                ->with('error', 'Antrian belum selesai, silakan selesaikan layanan terlebih dahulu.');
        }
        
        // Build URL with parameters
        $params = [
            'queue_id' => $queue->id,
            'service_id' => $queue->service_id,
            'service_name' => $queue->service->name,
            'service_price' => $queue->service->price,
            'customer_name' => $queue->customer_name,
            'customer_phone' => $queue->customer_phone ?? '',
            'queue_number' => $queue->queue_number
        ];
        
        $redirectUrl = route('pos.index') . '?' . http_build_query($params);
        
        Log::info('Redirecting to POS with params', $params);
        
        return redirect()->to($redirectUrl)
            ->with('success', 'Antrian ' . $queue->queue_number . ' (' . $queue->service->name . ') siap diproses pembayaran.');
    }

    /**
     * Process transaction from POS
     */
    public function processTransaction(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'services' => 'required|array',
            'services.*.id' => 'exists:services,id',
            'payment_method' => 'required|in:cash,qris,debit,credit',
            'paid_amount' => 'required|numeric|min:0',
            'points_used' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $servicesData = collect($request->services);
            $serviceIds = $servicesData->pluck('id');
            $services = Service::whereIn('id', $serviceIds)->get();
            
            $subtotal = $services->sum('price');
            $tax = $subtotal * 0.11;
            $discount = 0;
            $pointsDiscount = 0;
            $pointsEarned = 0;
            $pointsUsed = 0;
            
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                
                if ($customer) {
                    // Member discount
                    $memberDiscountPercent = $customer->member_discount;
                    $memberDiscount = $subtotal * ($memberDiscountPercent / 100);
                    $discount += $memberDiscount;
                    
                    // Points usage
                    $pointsUsed = min($request->points_used ?? 0, $customer->points);
                    $pointsDiscountValue = floor($pointsUsed / 10) * 1000;
                    $discount += $pointsDiscountValue;
                    $pointsDiscount = $pointsDiscountValue;
                    
                    // Points earned
                    $pointsEarned = floor(($subtotal - $discount) * 0.01);
                }
            }
            
            $total = $subtotal + $tax - $discount;
            $change = $request->paid_amount - $total;
            
            if ($change < 0) {
                throw new \Exception('Pembayaran kurang Rp ' . number_format(abs($change), 0, ',', '.'));
            }
            
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Get queue ID from request or session
            $queueId = $request->queue_id ?? session('queue_id') ?? null;
            $queueNumber = session('queue_number') ?? null;
            
            Log::info('Processing transaction', [
                'queue_id_from_request' => $request->queue_id,
                'queue_id_from_session' => session('queue_id'),
                'final_queue_id' => $queueId,
                'queue_number' => $queueNumber
            ]);
            
            // Prepare notes
            $notes = $request->notes;
            if ($queueNumber) {
                $notes = ($notes ? $notes . ' | ' : '') . 'Dari antrian: ' . $queueNumber;
            }
            
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'transaction_date' => Carbon::now('Asia/Jakarta'),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'service_fee' => 0,
                'total' => $total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $change,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'points_earned' => $pointsEarned,
                'points_used' => $pointsUsed,
                'notes' => $notes
            ]);
            
            // Save transaction details
            foreach ($servicesData as $item) {
                $service = $services->firstWhere('id', $item['id']);
                if ($service) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'service_id' => $service->id,
                        'barber_id' => $item['barber_id'] ?? null,
                        'price' => $service->price,
                        'discount' => 0,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }
            
            // *** UPDATE QUEUE TRANSACTION_ID ***
            $queueUpdated = false;
            
            // Try to get queue from various sources
            if ($queueId) {
                $queue = Queue::find($queueId);
                if ($queue && !$queue->transaction_id) {
                    $queue->update(['transaction_id' => $transaction->id]);
                    $queueUpdated = true;
                    Log::info('Queue updated from queue_id variable', [
                        'queue_id' => $queueId,
                        'transaction_id' => $transaction->id,
                        'queue_number' => $queue->queue_number
                    ]);
                }
            }
            
            // Also check from session directly
            if (!$queueUpdated && session('queue_id')) {
                $queueFromSession = Queue::find(session('queue_id'));
                if ($queueFromSession && !$queueFromSession->transaction_id) {
                    $queueFromSession->update(['transaction_id' => $transaction->id]);
                    $queueUpdated = true;
                    Log::info('Queue updated from session', [
                        'queue_id' => session('queue_id'),
                        'transaction_id' => $transaction->id
                    ]);
                }
            }
            
            // Check if queue is in the cart data
            if (!$queueUpdated && $servicesData->contains('from_queue', true)) {
                $queueItem = $servicesData->firstWhere('from_queue', true);
                if ($queueItem && isset($queueItem['queue_id'])) {
                    $queueFromCart = Queue::find($queueItem['queue_id']);
                    if ($queueFromCart && !$queueFromCart->transaction_id) {
                        $queueFromCart->update(['transaction_id' => $transaction->id]);
                        $queueUpdated = true;
                        Log::info('Queue updated from cart data', [
                            'queue_id' => $queueItem['queue_id'],
                            'transaction_id' => $transaction->id
                        ]);
                    }
                }
            }
            
            if (!$queueUpdated) {
                Log::warning('No queue was updated for transaction', [
                    'transaction_id' => $transaction->id,
                    'queue_id_from_request' => $request->queue_id,
                    'queue_id_from_session' => session('queue_id')
                ]);
            }
            
            // Update customer data
            if ($customer) {
                $customer->points = $customer->points - $pointsUsed + $pointsEarned;
                $customer->total_spent += $total;
                $customer->visit_count += 1;
                $customer->last_visit = Carbon::now('Asia/Jakarta');
                $customer->save();
                $customer->updateMemberLevel();
            }
            
            DB::commit();
            
            // Clear queue session
            session()->forget(['queue_cart', 'queue_customer', 'queue_id', 'queue_number', 'queue_loaded']);
            
            // Generate receipt HTML
            try {
                $receiptHtml = view('receipt.print', compact('transaction'))->render();
            } catch (\Exception $e) {
                $receiptHtml = '<div class="alert alert-warning">Struk tidak dapat ditampilkan, tetapi transaksi berhasil.</div>';
                Log::warning('Receipt view error: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'change' => $change,
                'receipt_url' => route('receipt.print', $transaction->id),
                'receipt_html' => $receiptHtml
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transaction processing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $date = Carbon::now('Asia/Jakarta');
        $prefix = 'INV/' . $date->format('Ymd') . '/';
        
        $lastTransaction = Transaction::whereDate('transaction_date', $date->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->invoice_number, -4));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Clear queue session
     */
    public function clearQueueSession()
    {
        session()->forget(['queue_cart', 'queue_customer', 'queue_id', 'queue_number', 'queue_loaded']);
        return response()->json(['success' => true]);
    }
    
    /**
     * Check queue session (for debugging)
     */
    public function checkQueueSession()
    {
        return response()->json([
            'has_queue' => session()->has('queue_cart'),
            'queue_cart' => session('queue_cart'),
            'queue_customer' => session('queue_customer'),
            'queue_id' => session('queue_id'),
            'queue_number' => session('queue_number'),
            'queue_loaded' => session('queue_loaded')
        ]);
    }
}