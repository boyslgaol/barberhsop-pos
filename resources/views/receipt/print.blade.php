<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaction->invoice_number }}</title>
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
            padding: 15px;
            width: 320px;
            margin: 0 auto;
        }
        
        .receipt {
            max-width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .divider-dotted {
            border-top: 1px dotted #000;
            margin: 8px 0;
        }
        
        .divider-double {
            border-top: 2px solid #000;
            margin: 8px 0;
        }
        
        .items-table {
            width: 100%;
            margin: 8px 0;
        }
        
        .items-table th, 
        .items-table td {
            text-align: left;
            padding: 4px 0;
        }
        
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 11px;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }
        
        .grand-total .total-row {
            font-size: 14px;
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
        
        .text-left {
            text-align: left;
        }
        
        .thankyou {
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        
        .qr-code {
            text-align: center;
            margin: 10px 0;
            font-family: monospace;
            font-size: 8px;
        }
        
        .watermark {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #ccc;
            z-index: -1;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 8px;
            }
            .no-print {
                display: none;
            }
            .watermark {
                display: none;
            }
        }
        
        .btn-print {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            margin: 10px 5px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .btn-close {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            margin: 10px 5px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .btn-print:hover, .btn-close:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h2>BARBERSHOP</h2>
            <p>Professional Grooming</p>
            <p>Jl. Contoh No. 123, Jakarta</p>
            <p>Telp: 0812-3456-7890</p>
            <p>Email: info@barbershop.com</p>
        </div>
        
        <!-- Transaction Info -->
        <div class="divider"></div>
        
        <div class="info-row">
            <span>Invoice:</span>
            <strong>{{ $transaction->invoice_number }}</strong>
        </div>
        <div class="info-row">
            <span>Tanggal:</span>
            <span>{{ $transaction->transaction_date ? $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') : '-' }}</span>
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
        
        <div class="divider-dotted"></div>
        
        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
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
        
        @if($transaction->service_fee > 0)
        <div class="total-row">
            <span>Service Fee:</span>
            <span>Rp {{ number_format($transaction->service_fee, 0, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="divider-double"></div>
        
        <div class="grand-total">
            <div class="total-row">
                <span><strong>TOTAL</strong></span>
                <span><strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong></span>
            </div>
        </div>
        
        <div class="divider-dotted"></div>
        
        <!-- Payment Info -->
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
            <span>{{ number_format($transaction->points_used) }}</span>
        </div>
        <div class="info-row">
            <span>Poin Didapat:</span>
            <span>{{ number_format($transaction->points_earned) }}</span>
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
            <p><strong>Terima kasih atas kunjungan Anda</strong></p>
            <p>Barbershop - Professional Grooming</p>
            <p>www.barbershop.com</p>
            <p>⭐ Follow IG: @barbershop</p>
        </div>
        
        <div class="thankyou">
            <p>*** SIMPAN STRUK INI ***</p>
        </div>
        
        <!-- Print Button -->
        <div class="text-center no-print" style="margin-top: 15px;">
            <button onclick="window.print(); return false;" class="btn-print">
                🖨️ CETAK STRUK
            </button>
            <button onclick="window.close();" class="btn-close">
                ✖ TUTUP
            </button>
        </div>
    </div>
    
    <div class="watermark">
        <p>Transaksi terekam & aman</p>
    </div>
    
    <script>
        // Optional: Auto print when page loads
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };
        
        // Keyboard shortcut: Ctrl+P to print
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>