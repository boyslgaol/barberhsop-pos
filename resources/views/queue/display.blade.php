<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Display Antrian - Barbershop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: white;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .display-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .current-calling {
            background: linear-gradient(135deg, #d4af37 0%, #b8960c 100%);
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            margin-bottom: 40px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(212, 175, 55, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 0 50px rgba(212, 175, 55, 0.5); }
        }
        
        .current-number {
            font-size: 120px;
            font-weight: 800;
            letter-spacing: 5px;
            color: #000;
            line-height: 1;
        }
        
        .current-name {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .current-service {
            font-size: 24px;
            color: #2d2d2d;
        }
        
        .waiting-time {
            font-size: 18px;
            color: #333;
            margin-top: 15px;
        }
        
        .queue-card {
            background: rgba(26, 26, 26, 0.95);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        
        .queue-number {
            font-size: 36px;
            font-weight: 700;
            color: #d4af37;
            font-family: monospace;
        }
        
        .in-service-item {
            background: rgba(16, 185, 129, 0.1);
            border-left: 3px solid #10b981;
            margin-bottom: 12px;
            padding: 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .in-service-item:hover {
            background: rgba(16, 185, 129, 0.2);
            transform: translateX(5px);
        }
        
        .waiting-item {
            background: rgba(245, 158, 11, 0.1);
            border-left: 3px solid #f59e0b;
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .title {
            color: #d4af37;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .badge-custom {
            background: #d4af37;
            color: #000;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .current-number { font-size: 60px; }
            .current-name { font-size: 20px; }
            .current-service { font-size: 16px; }
            .queue-number { font-size: 24px; }
            .title { font-size: 18px; }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .queue-item {
            animation: slideIn 0.3s ease;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="display-container">
        <!-- Sekarang Dipanggil -->
        <div class="current-calling">
            <div class="badge-custom d-inline-block mb-3">SEKARANG DIPANGGIL</div>
            <div class="current-number">
                {{ $nowCalling->queue_number ?? '–' }}
            </div>
            <div class="current-name">
                {{ $nowCalling->customer_name ?? 'Belum ada antrian' }}
            </div>
            <div class="current-service">
                {{ $nowCalling->service->name ?? '-' }}
            </div>
            @if($nowCalling)
                <div class="waiting-time">
                    <i class="fas fa-clock me-1"></i>
                    Waktu tunggu: {{ $nowCalling->getWaitingTimeAttribute() }} menit
                </div>
            @endif
        </div>
        
        <div class="row">
            <!-- Antrian Menunggu -->
            <div class="col-lg-6 mb-4">
                <div class="queue-card h-100">
                    <h3 class="title">
                        <i class="fas fa-clock me-2"></i>
                        ANTRIAN MENUNGGU
                    </h3>
                    <div class="waiting-list">
                        @forelse($waitingList as $queue)
                            <div class="waiting-item queue-item">
                                <div>
                                    <span class="queue-number">{{ $queue->queue_number }}</span>
                                    <div class="text-muted small">{{ $queue->customer_name }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark">Pos #{{ $queue->position }}</span>
                                    <div class="small text-muted">{{ $queue->service->name }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-smile fa-2x mb-2 d-block"></i>
                                Tidak ada antrian menunggu
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Sedang Dilayani -->
            <div class="col-lg-6 mb-4">
                <div class="queue-card h-100">
                    <h3 class="title">
                        <i class="fas fa-cut me-2"></i>
                        SEDANG DILAYANI
                    </h3>
                    <div class="in-service-list">
                        @forelse($inService as $service)
                            <div class="in-service-item queue-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="queue-number" style="font-size: 24px;">{{ $service->queue_number }}</div>
                                        <div class="fw-bold">{{ $service->customer_name }}</div>
                                        <small class="text-muted">{{ $service->service->name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">Sedang Berlangsung</span>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-user me-1"></i>{{ $service->barber->name ?? 'Barber' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-cut fa-2x mb-2 d-block"></i>
                                Tidak ada layanan sedang berlangsung
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <i class="fas fa-clock me-1"></i>
            Terakhir diperbarui: {{ now('Asia/Jakarta')->format('H:i:s') }}
            &nbsp;|&nbsp;
            <i class="fas fa-sync-alt me-1"></i>
            Auto refresh setiap 30 detik
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh setiap 10 detik
        setTimeout(function() {
            location.reload();
        }, 10000);
        
        // Play sound when new queue is called
        @if($nowCalling)
            let audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log('Audio not supported'));
        @endif
        
        // Add animation to new items
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.classList && node.classList.contains('queue-item')) {
                            node.style.opacity = '0';
                            setTimeout(() => {
                                node.style.transition = 'opacity 0.5s';
                                node.style.opacity = '1';
                            }, 10);
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
</body>
</html>