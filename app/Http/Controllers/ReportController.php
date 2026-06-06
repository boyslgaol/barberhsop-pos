<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses laporan.');
        }
        return $next($request);
    });
}
    /**
     * Daily report view
     */
    public function daily(Request $request)
    {
        // Set timezone ke Asia/Jakarta
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now('Asia/Jakarta');
        
        // Debug: Log query date
        Log::info('Daily Report Query', [
            'date' => $date->format('Y-m-d'),
            'date_start' => $date->copy()->startOfDay()->setTimezone('UTC')->toDateTimeString(),
            'date_end' => $date->copy()->endOfDay()->setTimezone('UTC')->toDateTimeString()
        ]);
        
        // Get transactions for the selected date
        $transactions = Transaction::with(['customer', 'user'])
            ->whereDate('transaction_date', $date->format('Y-m-d'))
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        // Alternative query using whereBetween if needed
        if ($transactions->isEmpty()) {
            $startOfDay = $date->copy()->startOfDay()->setTimezone('UTC');
            $endOfDay = $date->copy()->endOfDay()->setTimezone('UTC');
            
            $transactions = Transaction::with(['customer', 'user'])
                ->whereBetween('transaction_date', [$startOfDay, $endOfDay])
                ->orderBy('transaction_date', 'desc')
                ->get();
            
            Log::info('Alternative query transactions: ' . $transactions->count());
        }
        
        // Summary statistics
        $totalRevenue = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $totalCustomers = $transactions->unique('customer_id')->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Payment methods breakdown
        $paymentMethods = [
            'cash' => $transactions->where('payment_method', 'cash')->sum('total'),
            'qris' => $transactions->where('payment_method', 'qris')->sum('total'),
            'debit' => $transactions->where('payment_method', 'debit')->sum('total')
        ];
        
        // Top services today
        $topServices = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('services', 'transaction_details.service_id', '=', 'services.id')
            ->whereDate('transactions.transaction_date', $date->format('Y-m-d'))
            ->select('services.name', DB::raw('COUNT(*) as total'), DB::raw('SUM(transaction_details.price) as revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Hourly breakdown
        $hourlyBreakdown = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyBreakdown[$i] = $transactions->filter(function($t) use ($i) {
                $transactionHour = $t->transaction_date->setTimezone('Asia/Jakarta')->hour;
                return $transactionHour == $i;
            })->sum('total');
        }
        
        // Get expenses for the date
        $expenses = Expense::whereDate('expense_date', $date->format('Y-m-d'))->sum('amount');
        
        // Net profit
        $netProfit = $totalRevenue - $expenses;
        
        return view('reports.daily', compact(
            'date', 'transactions', 'totalRevenue', 'totalTransactions', 
            'totalCustomers', 'averageTransaction', 'paymentMethods',
            'topServices', 'hourlyBreakdown', 'expenses', 'netProfit'
        ));
    }
    
    /**
     * Monthly report view
     */
    public function monthly(Request $request)
    {
        // Get selected month or current month
        $month = $request->month ? Carbon::parse($request->month . '-01') : Carbon::now('Asia/Jakarta')->startOfMonth();
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        
        // Get transactions for the month
        $transactions = Transaction::with(['customer', 'user'])
            ->whereBetween('transaction_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        // Summary statistics
        $totalRevenue = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $totalCustomers = $transactions->unique('customer_id')->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Daily breakdown for chart
        $dailyBreakdown = [];
        $daysInMonth = $endDate->day;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dailyBreakdown[$i] = $transactions->filter(function($t) use ($i) {
                return $t->transaction_date->setTimezone('Asia/Jakarta')->day == $i;
            })->sum('total');
        }
        
        // Payment methods breakdown
        $paymentMethods = [
            'cash' => $transactions->where('payment_method', 'cash')->sum('total'),
            'qris' => $transactions->where('payment_method', 'qris')->sum('total'),
            'debit' => $transactions->where('payment_method', 'debit')->sum('total')
        ];
        
        // Top services this month
        $topServices = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('services', 'transaction_details.service_id', '=', 'services.id')
            ->whereBetween('transactions.transaction_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->select('services.name', DB::raw('COUNT(*) as total'), DB::raw('SUM(transaction_details.price) as revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Weekly breakdown
        $weeklyBreakdown = [];
        $currentWeek = $startDate->copy();
        $weekNumber = 1;
        while ($currentWeek <= $endDate) {
            $weekEnd = $currentWeek->copy()->endOfWeek();
            $weeklyBreakdown[$weekNumber] = [
                'week' => 'Minggu ' . $weekNumber,
                'start' => $currentWeek->format('d/m'),
                'end' => min($weekEnd, $endDate)->format('d/m'),
                'revenue' => $transactions->filter(function($t) use ($currentWeek, $weekEnd) {
                    $date = $t->transaction_date->setTimezone('Asia/Jakarta');
                    return $date >= $currentWeek && $date <= $weekEnd;
                })->sum('total')
            ];
            $currentWeek = $weekEnd->copy()->addDay();
            $weekNumber++;
        }
        
        // Get expenses for the month
        $expenses = Expense::whereBetween('expense_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->sum('amount');
        
        // Net profit
        $netProfit = $totalRevenue - $expenses;
        
        // Growth percentage compared to previous month
        $previousMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $startDate->copy()->subMonth()->endOfMonth();
        $previousRevenue = Transaction::whereBetween('transaction_date', [$previousMonthStart, $previousMonthEnd])->sum('total');
        $growth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : ($totalRevenue > 0 ? 100 : 0);
        
        return view('reports.monthly', compact(
            'month', 'startDate', 'endDate', 'transactions', 'totalRevenue', 
            'totalTransactions', 'totalCustomers', 'averageTransaction',
            'dailyBreakdown', 'weeklyBreakdown', 'topServices', 'paymentMethods',
            'expenses', 'netProfit', 'growth', 'previousRevenue'
        ));
    }
    
    /**
     * Export transactions to Excel (CSV)
     */
    public function exportExcel(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now('Asia/Jakarta');
        
        $transactions = Transaction::with(['customer', 'user', 'details.service'])
            ->whereDate('transaction_date', $date->format('Y-m-d'))
            ->get();
        
        $filename = 'laporan_' . $date->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['No. Invoice', 'Tanggal', 'Pelanggan', 'Kasir', 'Total', 'Metode Bayar', 'Status', 'Detail Layanan']);
            
            // Data
            foreach ($transactions as $transaction) {
                $services = $transaction->details->pluck('service.name')->implode(', ');
                fputcsv($file, [
                    $transaction->invoice_number,
                    $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
                    $transaction->customer->name ?? 'Guest',
                    $transaction->user->name ?? '-',
                    number_format($transaction->total, 0, ',', '.'),
                    strtoupper($transaction->payment_method),
                    $transaction->status,
                    $services
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Expenses report view
     */
    public function expenses(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now('Asia/Jakarta')->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now('Asia/Jakarta')->endOfMonth();
        
        $expenses = Expense::with('user')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        
        $expensesByCategory = $expenses->groupBy('category')->map(function($item) {
            return $item->sum('amount');
        })->sortDesc();
        
        // Chart data
        $chartCategories = $expensesByCategory->keys();
        $chartAmounts = $expensesByCategory->values();
        
        return view('reports.expenses', compact(
            'expenses', 'totalExpenses', 'expensesByCategory', 
            'startDate', 'endDate', 'chartCategories', 'chartAmounts'
        ));
    }
    
    /**
     * Debug: Check transactions in database
     */
    public function debugTransactions(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now('Asia/Jakarta');
        
        $transactions = Transaction::all();
        
        $result = [
            'total_transactions_in_db' => Transaction::count(),
            'selected_date' => $date->format('Y-m-d'),
            'transactions_on_date' => Transaction::whereDate('transaction_date', $date->format('Y-m-d'))->count(),
            'all_transactions' => Transaction::select('id', 'invoice_number', 'transaction_date', 'total', 'status')->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'invoice' => $t->invoice_number,
                    'date' => $t->transaction_date->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    'total' => $t->total,
                    'status' => $t->status
                ];
            })
        ];
        
        return response()->json($result);
    }
    
    /**
     * Get report summary data for dashboard
     */
    public function summary(Request $request)
    {
        $today = Carbon::now('Asia/Jakarta');
        $startOfWeek = $today->copy()->startOfWeek();
        $startOfMonth = $today->copy()->startOfMonth();
        
        $todayRevenue = Transaction::whereDate('transaction_date', $today)->sum('total');
        $weekRevenue = Transaction::whereBetween('transaction_date', [$startOfWeek, $today])->sum('total');
        $monthRevenue = Transaction::whereBetween('transaction_date', [$startOfMonth, $today])->sum('total');
        
        $topServices = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('services', 'transaction_details.service_id', '=', 'services.id')
            ->whereMonth('transactions.transaction_date', $today->month)
            ->select('services.name', DB::raw('COUNT(*) as total'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'today_revenue' => $todayRevenue,
            'week_revenue' => $weekRevenue,
            'month_revenue' => $monthRevenue,
            'top_services' => $topServices
        ]);
    }
}