<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%");
            });
        }
        
        // Member level filter
        if ($request->has('member_level') && $request->member_level != '') {
            $query->where('member_level', $request->member_level);
        }
        
        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics
        $totalCustomers = Customer::count();
        $totalSpent = Customer::sum('total_spent');
        $totalPoints = Customer::sum('points');
        $memberCounts = [
            'regular' => Customer::where('member_level', 'regular')->count(),
            'silver' => Customer::where('member_level', 'silver')->count(),
            'gold' => Customer::where('member_level', 'gold')->count(),
            'platinum' => Customer::where('member_level', 'platinum')->count(),
        ];
        
        return view('customers.index', compact('customers', 'totalCustomers', 'totalSpent', 'totalPoints', 'memberCounts'));
    }
    
    /**
     * Show form for creating new customer
     */
    public function create()
    {
        return view('customers.create');
    }
    
    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'points' => 'nullable|integer|min:0'
        ]);
        
        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'points' => $request->points ?? 0,
            'member_code' => Customer::generateMemberCode(),
            'member_level' => 'regular',
            'total_spent' => 0,
            'visit_count' => 0
        ]);
        
        return redirect()->route('customers.show', $customer)
            ->with('success', 'Pelanggan "' . $customer->name . '" berhasil ditambahkan');
    }
    
    /**
     * Display specified customer
     */
    public function show(Customer $customer)
    {
        $transactions = $customer->transactions()
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
        
        return view('customers.show', compact('customer', 'transactions'));
    }
    
    /**
     * Show form for editing customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }
    
    /**
     * Update specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'points' => 'nullable|integer|min:0'
        ]);
        
        $customer->update($request->all());
        
        // Update member level based on total spent
        $customer->updateMemberLevel();
        
        return redirect()->route('customers.show', $customer)
            ->with('success', 'Pelanggan "' . $customer->name . '" berhasil diperbarui');
    }
    
    /**
     * Remove specified customer
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has transactions
        if ($customer->transactions()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Pelanggan tidak dapat dihapus karena sudah memiliki transaksi');
        }
        
        $customerName = $customer->name;
        $customer->delete();
        
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan "' . $customerName . '" berhasil dihapus');
    }
    
    /**
     * Add points to customer
     */
    public function addPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:1'
        ]);
        
        $customer->points += $request->points;
        $customer->save();
        
        return response()->json([
            'success' => true,
            'message' => $request->points . ' poin berhasil ditambahkan',
            'new_points' => $customer->points
        ]);
    }
    
    /**
     * Get customer by phone (for POS)
     */
    public function getByPhone($phone)
    {
        $customer = Customer::where('phone', $phone)->first();
        
        if ($customer) {
            return response()->json($customer);
        }
        
        return response()->json(null, 404);
    }
}