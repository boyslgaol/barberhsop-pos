<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\QueueSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'cashier', 'barber'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        return $next($request);
    });
}
  public function index()
{
    $queues = Queue::with(['service', 'barber'])
        ->whereIn('status', ['waiting', 'calling', 'in_service'])
        ->orderBy('queue_time')
        ->get();
    
    // Pastikan mengambil data dengan transaction_id
    $completedQueues = Queue::with(['service', 'barber'])
        ->whereIn('status', ['completed', 'cancelled'])
        ->whereDate('queue_time', Carbon::today('Asia/Jakarta'))
        ->orderBy('queue_time', 'desc')
        ->limit(20)
        ->get();
    
    $services = Service::where('is_active', true)->get();
    $barbers = User::where('role', 'barber')->where('is_active', true)->get();
    
    $todayQueues = Queue::whereDate('queue_time', Carbon::today('Asia/Jakarta'))->count();
    $waitingQueues = Queue::whereDate('queue_time', Carbon::today('Asia/Jakarta'))->where('status', 'waiting')->count();
    $completedToday = Queue::whereDate('queue_time', Carbon::today('Asia/Jakarta'))->where('status', 'completed')->count();
    
    $avgResult = Queue::whereDate('queue_time', Carbon::today('Asia/Jakarta'))
        ->whereNotNull('call_time')
        ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, queue_time, call_time)) as avg_wait'))
        ->first();
    $avgWaitingTime = $avgResult->avg_wait ?? 0;
    
    return view('queue.index', compact(
        'queues', 'completedQueues', 'services', 'barbers',
        'todayQueues', 'waitingQueues', 'completedToday', 'avgWaitingTime'
    ));
}
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string'
        ]);
        
        $service = Service::findOrFail($request->service_id);
        
        $queue = Queue::create([
            'queue_number' => Queue::generateQueueNumber(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'service_id' => $request->service_id,
            'estimated_price' => $service->price,
            'estimated_duration' => $service->duration,
            'status' => 'waiting',
            'queue_time' => Carbon::now('Asia/Jakarta'),
            'notes' => $request->notes,
            'created_by' => auth()->id()
        ]);
        
        $this->updatePositions();
        
        return response()->json([
            'success' => true,
            'queue' => $queue,
            'message' => 'Antrian berhasil ditambahkan. No: ' . $queue->queue_number
        ]);
    }
    
    public function call($id)
    {
        $queue = Queue::findOrFail($id);
        
        if ($queue->status !== 'waiting') {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak dapat dipanggil'
            ], 400);
        }
        
        $queue->update([
            'status' => 'calling',
            'call_time' => Carbon::now('Asia/Jakarta')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pelanggan ' . $queue->customer_name . ' dipanggil'
        ]);
    }
    
    public function start(Request $request, $id)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id'
        ]);
        
        $queue = Queue::findOrFail($id);
        
        if ($queue->status !== 'calling') {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak dapat dimulai'
            ], 400);
        }
        
        $barber = User::find($request->barber_id);
        
        if (!$barber || $barber->role !== 'barber') {
            return response()->json([
                'success' => false,
                'message' => 'Barber tidak valid'
            ], 400);
        }
        
        if ($barber->is_busy || $barber->current_queue_id) {
            return response()->json([
                'success' => false,
                'message' => 'Barber ' . $barber->name . ' sedang sibuk melayani pelanggan lain!'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $queue->update([
                'status' => 'in_service',
                'start_time' => Carbon::now('Asia/Jakarta'),
                'barber_id' => $request->barber_id
            ]);
            
            $barber->update([
                'is_busy' => true,
                'current_queue_id' => $queue->id
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Layanan dimulai untuk ' . $queue->customer_name . ' oleh ' . $barber->name
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai layanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function complete($id)
    {
        $queue = Queue::findOrFail($id);
        
        if ($queue->status !== 'in_service') {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak dapat diselesaikan'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            if ($queue->barber_id) {
                User::where('id', $queue->barber_id)->update([
                    'is_busy' => false,
                    'current_queue_id' => null
                ]);
            }
            
            $queue->update([
                'status' => 'completed',
                'end_time' => Carbon::now('Asia/Jakarta')
            ]);
            
            $this->updatePositions();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Layanan selesai. Silakan proses pembayaran.',
                'redirect' => route('pos.from-queue', $queue->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan layanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function cancel(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        if (!in_array($queue->status, ['waiting', 'calling', 'in_service'])) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak dapat dibatalkan'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            if ($queue->barber_id && $queue->status === 'in_service') {
                User::where('id', $queue->barber_id)->update([
                    'is_busy' => false,
                    'current_queue_id' => null
                ]);
            }
            
            $queue->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->reason ?? 'Dibatalkan oleh operator',
                'end_time' => Carbon::now('Asia/Jakarta')
            ]);
            
            $this->updatePositions();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Antrian dibatalkan'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan antrian: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function assignBarber(Request $request, $id)
    {
        $request->validate([
            'barber_id' => 'required|exists:users,id'
        ]);
        
        $queue = Queue::findOrFail($id);
        $barber = User::find($request->barber_id);
        
        if (!$barber || $barber->role !== 'barber') {
            return response()->json([
                'success' => false,
                'message' => 'Barber tidak valid'
            ], 400);
        }
        
        if ($barber->is_busy || $barber->current_queue_id) {
            return response()->json([
                'success' => false,
                'message' => 'Barber ' . $barber->name . ' sedang sibuk melayani pelanggan lain!'
            ], 400);
        }
        
        $queue->update(['barber_id' => $request->barber_id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Barber ' . $barber->name . ' ditugaskan untuk antrian ini'
        ]);
    }
    
    public function updatePositions()
    {
        $queues = Queue::where('status', 'waiting')
            ->orderBy('queue_time')
            ->get();
        
        foreach ($queues as $index => $queue) {
            $queue->update(['position' => $index + 1]);
        }
    }
    
    public function getDisplay()
    {
        $queues = Queue::with(['service', 'barber'])
            ->whereIn('status', ['waiting', 'calling', 'in_service'])
            ->orderBy('queue_time')
            ->get();
        
        $nowCalling = Queue::with(['service', 'barber'])->where('status', 'calling')->first();
        $inService = Queue::with(['service', 'barber'])->where('status', 'in_service')->get();
        $waitingList = Queue::with(['service', 'barber'])
            ->where('status', 'waiting')
            ->orderBy('position')
            ->limit(10)
            ->get();
        
        return view('queue.display', compact('queues', 'nowCalling', 'inService', 'waitingList'));
    }
    
    public function getBarberStatus()
    {
        $barbers = User::where('role', 'barber')
            ->where('is_active', true)
            ->with('currentQueue')
            ->get()
            ->map(function($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'is_available' => !$barber->is_busy && !$barber->current_queue_id,
                    'current_queue' => $barber->currentQueue ? [
                        'number' => $barber->currentQueue->queue_number,
                        'customer' => $barber->currentQueue->customer_name
                    ] : null
                ];
            });
        
        return response()->json($barbers);
    }
    
    public function checkBarberAvailability($id)
    {
        $barber = User::find($id);
        
        if (!$barber || $barber->role !== 'barber') {
            return response()->json([
                'available' => false, 
                'name' => null,
                'message' => 'Barber tidak ditemukan'
            ]);
        }
        
        $isAvailable = !$barber->is_busy && !$barber->current_queue_id;
        
        return response()->json([
            'available' => $isAvailable,
            'name' => $barber->name,
            'message' => $isAvailable ? 'Barber tersedia' : 'Barber sedang sibuk'
        ]);
    }
    
    public function getAvailableBarbers()
    {
        $barbers = User::where('role', 'barber')
            ->where('is_active', true)
            ->where('is_busy', false)
            ->whereNull('current_queue_id')
            ->select('id', 'name')
            ->get();
        
        return response()->json($barbers);
    }
    
    public function resetDaily()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Tidak memiliki akses');
        }
        
        Queue::whereDate('queue_time', '<', Carbon::today('Asia/Jakarta'))->delete();
        
        return redirect()->back()->with('success', 'Antrian harian telah direset');
    }
    
    public function export()
    {
        $queues = Queue::with(['service', 'barber'])
            ->whereDate('queue_time', Carbon::today('Asia/Jakarta'))
            ->orderBy('queue_time')
            ->get();
        
        $filename = 'antrian_' . Carbon::now('Asia/Jakarta')->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($queues) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Antrian', 'Nama Pelanggan', 'No. HP', 'Layanan', 'Barber', 'Status', 'Waktu Antri', 'Waktu Panggil', 'Waktu Mulai', 'Waktu Selesai']);
            
            foreach ($queues as $queue) {
                fputcsv($file, [
                    $queue->queue_number,
                    $queue->customer_name,
                    $queue->customer_phone ?? '-',
                    $queue->service->name,
                    $queue->barber->name ?? '-',
                    $queue->status,
                    $queue->queue_time ? $queue->queue_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-',
                    $queue->call_time ? $queue->call_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-',
                    $queue->start_time ? $queue->start_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-',
                    $queue->end_time ? $queue->end_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Redirect to POS for payment from queue
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
    
    $service = $queue->service;
    if (!$service) {
        return redirect()->route('queue.index')
            ->with('error', 'Data layanan tidak ditemukan.');
    }
    
    // Debug log
    \Log::info('=== FROM QUEUE - PREPARING REDIRECT ===');
    \Log::info('Queue ID: ' . $queue->id);
    \Log::info('Service: ' . $service->name);
    \Log::info('Customer: ' . $queue->customer_name);
    
    // Build URL parameters
    $params = [
        'queue_id' => $queue->id,
        'service_id' => $service->id,
        'service_name' => $service->name,
        'service_price' => $service->price,
        'customer_name' => $queue->customer_name,
        'customer_phone' => $queue->customer_phone ?? '',
        'queue_number' => $queue->queue_number
    ];
    
    // Build full URL
    $redirectUrl = url('/pos') . '?' . http_build_query($params);
    
    \Log::info('Redirect URL: ' . $redirectUrl);
    
    // Clear any existing session data
    session()->forget(['queue_cart', 'queue_customer', 'queue_id', 'queue_number', 'queue_loaded']);
    
    return redirect()->to($redirectUrl)
        ->with('success', 'Antrian ' . $queue->queue_number . ' (' . $service->name . ') siap diproses pembayaran.');
}
public function checkQueueSession()
{
    $hasQueue = session()->has('queue_cart') && session('queue_loaded') === true;
    
    return response()->json([
        'has_queue' => $hasQueue,
        'queue_cart' => session('queue_cart'),
        'queue_customer' => session('queue_customer'),
        'queue_id' => session('queue_id'),
        'queue_number' => session('queue_number'),
        'queue_loaded' => session('queue_loaded')
    ]);
}
    
    /**
     * Clear queue session
     */
    public function clearQueueSession()
    {
        session()->forget(['queue_cart', 'queue_customer', 'queue_id', 'queue_number', 'queue_loaded']);
        return response()->json(['success' => true]);
    }
}