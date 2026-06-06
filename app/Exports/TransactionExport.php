// app/Exports/TransactionExport.php
<?php
namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Transaction::with('customer', 'user')->get()->map(function($transaction) {
            return [
                'Invoice' => $transaction->invoice_number,
                'Customer' => $transaction->customer->name ?? 'Guest',
                'Date' => $transaction->transaction_date,
                'Total' => $transaction->total,
                'Payment' => $transaction->payment_method,
                'Cashier' => $transaction->user->name
            ];
        });
    }
    
    public function headings(): array
    {
        return ['Invoice', 'Customer', 'Date', 'Total', 'Payment Method', 'Cashier'];
    }
}