<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class TransactionController extends Controller
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
    public function index(Request $request)
    {
        $query = Transaction::with('customer', 'user');
        
        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
        
        return view('transactions.index', compact('transactions'));
    }
    
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'user', 'details.service', 'details.barber');
        return view('transactions.show', compact('transaction'));
    }
    
    /**
     * Print receipt for transaction
     */
    public function printReceipt(Transaction $transaction)
    {
        $transaction->load('customer', 'user', 'details.service', 'details.barber');
        
        // Sanitize invoice number for filename (remove special characters)
        $safeInvoiceNumber = preg_replace('/[^a-zA-Z0-9_-]/', '_', $transaction->invoice_number);
        $filename = 'struk_' . $safeInvoiceNumber . '.pdf';
        
        // Generate PDF
        $pdf = Pdf::loadView('receipt.print', compact('transaction'));
        $pdf->setPaper('a6', 'portrait');
        
        return $pdf->download($filename);
    }
    
    /**
     * Print receipt as HTML (without PDF)
     */
    public function printReceiptHtml(Transaction $transaction)
    {
        $transaction->load('customer', 'user', 'details.service', 'details.barber');
        return view('receipt.print', compact('transaction'));
    }
    
    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'completed') {
            $transaction->status = 'cancelled';
            $transaction->save();
            
            if ($transaction->customer && $transaction->points_used > 0) {
                $customer = $transaction->customer;
                $customer->points += $transaction->points_used;
                $customer->save();
            }
            
            return redirect()->back()->with('success', 'Transaksi dibatalkan');
        }
        
        return redirect()->back()->with('error', 'Tidak dapat membatalkan transaksi');
    }
}