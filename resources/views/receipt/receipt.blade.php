<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 10px;
            width: 300px;
            margin: 0 auto;
        }
        
        .receipt {
            max-width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .header h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .divider-dotted {
            border-top: 1px dotted #000;
            margin: 10px 0;
        }
        
        .items-table {
            width: 100%;
            margin: 10px 0;
        }
        
        .items-table th, 
        .items-table td {
            text-align: left;
            padding: 3px 0;
        }
        
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-weight: bold;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .thankyou {
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h2>BARBERSHOP</h2>
            <p>Jl. Contoh No. 123, Jakarta</p>
            <p>Telp: 0812-3456-7890</p>
            <p>Email: info@barbershop.com</p>
        </div>
        
        <!-- Transaction Info -->
        <div class="info-row">
            <span>No. Invoice:</span>
            <strong>{{ $transaction->invoice_number }}</strong>
        </div>
        <div class="info-row">
            <span>Tanggal:</span>
            <span>{{ $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span>Kasir:</span>
            <span>{{ $transaction->user->name ?? 'Admin' }}</span>
        </div>
        <div class="info-row">
            <span>Pelanggan:</span>
            <span>{{ $transaction->customer->name ?? 'Guest' }}</span>
        </div>
        
        @if($transaction->customer && $transaction->customer->phone)
        <div class="info-row">
            <span>No. HP:</span>
            <span>{{ $transaction->customer->phone }}</span>
        </div>
        @endif
        
        <div class="divider"></div>
        
        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $item)
                <tr>
                    <td>{{ $item->service->name }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="divider-dotted"></div>
        
        <!-- Totals -->
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>PPN 11%:</span>
            <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
        </div>
        
        @if($transaction->discount > 0)
        <div class="total-row">
            <span>Diskon:</span>
            <span>Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="grand-total">
            <div class="total-row">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="divider-dotted"></div>
        
        <div class="info-row">
            <span>Metode Bayar:</span>
            <span>{{ strtoupper($transaction->payment_method) }}</span>
        </div>
        <div class="info-row">
            <span>Tunai:</span>
            <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
        
        <!-- Points Info -->
        @if($transaction->points_earned > 0 || $transaction->points_used > 0)
        <div class="divider-dotted"></div>
        <div class="info-row">
            <span>Poin Digunakan:</span>
            <span>{{ $transaction->points_used }}</span>
        </div>
        <div class="info-row">
            <span>Poin Didapat:</span>
            <span>{{ $transaction->points_earned }}</span>
        </div>
        @endif
        
        <!-- Notes -->
        @if($transaction->notes)
        <div class="divider-dotted"></div>
        <div class="info-row">
            <span>Catatan:</span>
            <span>{{ $transaction->notes }}</span>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kunjungan Anda</p>
            <p>Barbershop - Professional Grooming</p>
            <p>www.barbershop.com</p>
        </div>
        
        <div class="thankyou">
            <p>*** SIMPAN STRUK INI ***</p>
        </div>
        
        <!-- Print Button -->
        <div class="text-center no-print" style="margin-top: 15px;">
            <button onclick="window.print()" style="padding: 5px 10px; margin-right: 5px;">🖨️ Print</button>
            <button onclick="window.close()" style="padding: 5px 10px;">✖ Tutup</button>
        </div>
    </div>
    
    <script>
        // Auto print when modal opens (optional)
        // setTimeout(function() {
        //     window.print();
        // }, 500);
    </script>
</body>
</html>