<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user')->orderBy('expense_date', 'desc');
        
        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }
        
        $expenses = $query->paginate(15);
        
        // Get summary
        $totalExpenses = $query->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $monthExpenses = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        
        // Get categories for filter
        $categories = Expense::select('category')->distinct()->pluck('category');
        
        return view('expenses.index', compact('expenses', 'totalExpenses', 'todayExpenses', 'monthExpenses', 'categories'));
    }
    
    public function create()
    {
        $categories = [
            'Sewa Tempat',
            'Listrik & Air',
            'Gaji Karyawan',
            'Peralatan',
            'Produk Perawatan',
            'Marketing',
            'Perawatan & Servis',
            'Lainnya'
        ];
        
        return view('expenses.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->all();
        $data['user_id'] = auth()->id();
        
        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $filename = time() . '_' . $receipt->getClientOriginalName();
            $path = $receipt->storeAs('receipts', $filename, 'public');
            $data['receipt'] = $path;
        }
        
        Expense::create($data);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }
    
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }
    
    public function edit(Expense $expense)
    {
        $categories = [
            'Sewa Tempat',
            'Listrik & Air',
            'Gaji Karyawan',
            'Peralatan',
            'Produk Perawatan',
            'Marketing',
            'Perawatan & Servis',
            'Lainnya'
        ];
        
        return view('expenses.edit', compact('expense', 'categories'));
    }
    
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->all();
        
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $filename = time() . '_' . $receipt->getClientOriginalName();
            $path = $receipt->storeAs('receipts', $filename, 'public');
            $data['receipt'] = $path;
        }
        
        $expense->update($data);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }
    
    public function destroy(Expense $expense)
    {
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
    
    public function byCategory($category)
    {
        $expenses = Expense::where('category', $category)
            ->with('user')
            ->orderBy('expense_date', 'desc')
            ->paginate(15);
        
        $total = $expenses->sum('amount');
        
        return view('expenses.by-category', compact('expenses', 'category', 'total'));
    }
    
    public function exportPdf(Request $request)
    {
        $query = Expense::with('user');
        
        if ($request->date_from) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        $expenses = $query->get();
        $total = $expenses->sum('amount');
        
        $pdf = Pdf::loadView('expenses.pdf', compact('expenses', 'total'));
        return $pdf->download('laporan-pengeluaran.pdf');
    }
    
    public function exportExcel(Request $request)
    {
        return Excel::download(new ExpensesExport($request), 'pengeluaran.xlsx');
    }
}