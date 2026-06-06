<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Constructor - semua role bisa akses dashboard
     */
   public function __construct()
    {
        // Hanya pastikan user sudah login
        $this->middleware('auth');
    }

    /**
     * Display dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Get current user role
        $userRole = auth()->user()->role;
        
        // ==============================================================
        // STATISTICS (berdasarkan role)
        // ==============================================================
        
        // Total pendapatan hari ini
        $todayRevenue = Transaction::whereDate('transaction_date', $today)
            ->where('status', 'completed')
            ->sum('total');
        
        // Total transaksi hari ini
        $todayTransactionsCount = Transaction::whereDate('transaction_date', $today)
            ->where('status', 'completed')
            ->count();
        
        // Jumlah pelanggan unik hari ini
        $todayCustomers = Transaction::whereDate('transaction_date', $today)
            ->where('status', 'completed')
            ->distinct('customer_id')
            ->count('customer_id');
        
        // Pendapatan bulan ini
        $monthStart = Carbon::now()->startOfMonth();
        $monthRevenue = Transaction::whereBetween('transaction_date', [$monthStart, now()])
            ->where('status', 'completed')
            ->sum('total');
        
        // ==============================================================
        // TOP SERVICES (Layanan Terlaris)
        // ==============================================================
        
        // Cek apakah tabel transaction_details dan services ada
        $topServices = collect();
        try {
            $topServices = DB::table('transaction_details')
                ->join('services', 'transaction_details.service_id', '=', 'services.id')
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->whereMonth('transactions.transaction_date', $currentMonth)
                ->whereYear('transactions.transaction_date', $currentYear)
                ->where('transactions.status', 'completed')
                ->select('services.name', DB::raw('COUNT(*) as total'))
                ->groupBy('services.id', 'services.name')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Jika tabel belum ada, gunakan data dummy atau kosong
            \Log::warning('Error getting top services: ' . $e->getMessage());
        }
        
        // ==============================================================
        // RECENT TRANSACTIONS (berdasarkan role)
        // ==============================================================
        
        $recentTransactions = collect();
        try {
            $query = Transaction::with('customer')
                ->where('status', 'completed')
                ->orderBy('transaction_date', 'desc')
                ->limit(10);
            
            // Jika role barber, hanya lihat transaksi yang terkait dengan layanan barber
            if ($userRole === 'barber') {
                // Barber hanya melihat transaksi dari layanan yang mereka handle
                // Atau bisa dibatasi sesuai kebutuhan
                $recentTransactions = $query->get();
            } else {
                $recentTransactions = $query->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Error getting recent transactions: ' . $e->getMessage());
        }
        
        // ==============================================================
        // CHART DATA (7 hari terakhir)
        // ==============================================================
        
        $chartData = [
            'labels' => [],
            'revenue' => []
        ];
        
        try {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $revenue = Transaction::whereDate('transaction_date', $date)
                    ->where('status', 'completed')
                    ->sum('total');
                
                $chartData['labels'][] = $date->format('d/m');
                $chartData['revenue'][] = (float) $revenue;
            }
        } catch (\Exception $e) {
            \Log::warning('Error getting chart data: ' . $e->getMessage());
            // Default data jika error
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartData['labels'][] = $date->format('d/m');
                $chartData['revenue'][] = 0;
            }
        }
        
        // ==============================================================
        // BARBER PERFORMANCE (Khusus Admin)
        // ==============================================================
        
        $barberPerformance = collect();
        if ($userRole === 'admin') {
            try {
                $barberPerformance = DB::table('users')
                    ->leftJoin('queues', 'users.id', '=', 'queues.barber_id')
                    ->where('users.role', 'barber')
                    ->where('users.is_active', true)
                    ->select(
                        'users.id',
                        'users.name',
                        DB::raw('COUNT(CASE WHEN queues.status = "completed" THEN 1 END) as completed_services'),
                        DB::raw('COUNT(CASE WHEN queues.status = "pending" THEN 1 END) as pending_services')
                    )
                    ->groupBy('users.id', 'users.name')
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Error getting barber performance: ' . $e->getMessage());
            }
        }
        
        // ==============================================================
        // CASHIER PERFORMANCE (Khusus Admin)
        // ==============================================================
        
        $cashierPerformance = collect();
        if ($userRole === 'admin') {
            try {
                $cashierPerformance = DB::table('users')
                    ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
                    ->where('users.role', 'cashier')
                    ->where('users.is_active', true)
                    ->whereMonth('transactions.transaction_date', $currentMonth)
                    ->select(
                        'users.id',
                        'users.name',
                        DB::raw('COUNT(transactions.id) as total_transactions'),
                        DB::raw('COALESCE(SUM(transactions.total), 0) as total_revenue')
                    )
                    ->groupBy('users.id', 'users.name')
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Error getting cashier performance: ' . $e->getMessage());
            }
        }
        
        // ==============================================================
        // SHOP NAME (dari session atau default)
        // ==============================================================
        
        $shopName = session('settings.shopName', 'BARBERSHOP');
        
        // ==============================================================
        // RETURN VIEW
        // ==============================================================
        
        return view('dashboard', compact(
            'todayRevenue',
            'todayTransactionsCount',
            'todayCustomers',
            'monthRevenue',
            'topServices',
            'recentTransactions',
            'chartData',
            'shopName',
            'userRole',
            'barberPerformance',
            'cashierPerformance'
        ));
    }
    
    /**
     * Get chart data for AJAX requests
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 7);
        
        $chartData = [
            'labels' => [],
            'revenue' => []
        ];
        
        try {
            for ($i = $period - 1; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $revenue = Transaction::whereDate('transaction_date', $date)
                    ->where('status', 'completed')
                    ->sum('total');
                
                $chartData['labels'][] = $date->format('d/m');
                $chartData['revenue'][] = (float) $revenue;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
        return response()->json($chartData);
    }
    
    /**
     * Refresh data for AJAX requests
     */
    public function refreshData(Request $request)
    {
        try {
            $today = Carbon::today();
            
            $todayRevenue = Transaction::whereDate('transaction_date', $today)
                ->where('status', 'completed')
                ->sum('total');
            
            $todayTransactionsCount = Transaction::whereDate('transaction_date', $today)
                ->where('status', 'completed')
                ->count();
            
            $todayCustomers = Transaction::whereDate('transaction_date', $today)
                ->where('status', 'completed')
                ->distinct('customer_id')
                ->count('customer_id');
            
            $monthStart = Carbon::now()->startOfMonth();
            $monthRevenue = Transaction::whereBetween('transaction_date', [$monthStart, now()])
                ->where('status', 'completed')
                ->sum('total');
            
            return response()->json([
                'success' => true,
                'todayRevenue' => $todayRevenue,
                'todayTransactionsCount' => $todayTransactionsCount,
                'todayCustomers' => $todayCustomers,
                'monthRevenue' => $monthRevenue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get barber performance data
     */
    public function getBarberPerformance(Request $request)
    {
        $user = auth()->user();
        
        // Hanya admin yang bisa melihat performa barber
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $period = $request->get('period', 'month'); // week, month, year
        
        try {
            $query = DB::table('users')
                ->leftJoin('queues', 'users.id', '=', 'queues.barber_id')
                ->where('users.role', 'barber')
                ->where('users.is_active', true);
            
            if ($period === 'week') {
                $query->where('queues.created_at', '>=', Carbon::now()->startOfWeek());
            } elseif ($period === 'month') {
                $query->whereMonth('queues.created_at', Carbon::now()->month);
            } elseif ($period === 'year') {
                $query->whereYear('queues.created_at', Carbon::now()->year);
            }
            
            $performance = $query->select(
                    'users.id',
                    'users.name',
                    DB::raw('COUNT(CASE WHEN queues.status = "completed" THEN 1 END) as completed_services'),
                    DB::raw('COUNT(CASE WHEN queues.status = "pending" THEN 1 END) as pending_services'),
                    DB::raw('COUNT(CASE WHEN queues.status = "processing" THEN 1 END) as processing_services')
                )
                ->groupBy('users.id', 'users.name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $performance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}