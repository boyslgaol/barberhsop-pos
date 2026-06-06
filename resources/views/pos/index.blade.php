@extends('layouts.app')

@section('title', 'Point of Sale - Kasir')

@push('styles')
<style>
    /* POS Specific Styles */
    .pos-container {
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Service Card */
    .service-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(212, 175, 55, 0.15);
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.02) 100%);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-gold), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .service-card:hover::before {
        opacity: 1;
    }
    
    .service-card:hover {
        transform: translateY(-5px);
        border-color: var(--accent-gold);
        box-shadow: 0 10px 30px rgba(212, 175, 55, 0.15);
    }
    
    .service-card .service-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(212, 175, 55, 0.1);
        border-radius: 12px;
        transition: all 0.3s;
    }
    
    .service-card:hover .service-icon {
        background: var(--accent-gold);
        transform: scale(1.05);
    }
    
    .service-card:hover .service-icon i {
        color: var(--primary-black) !important;
    }
    
    /* Cart Section */
    .cart-card {
        position: sticky;
        top: 20px;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .cart-header {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
    }
    
    .cart-body {
        max-height: calc(100vh - 300px);
        overflow-y: auto;
        padding: 1rem;
    }
    
    /* Custom Scrollbar for Cart */
    .cart-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .cart-body::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }
    
    .cart-body::-webkit-scrollbar-thumb {
        background: var(--accent-gold);
        border-radius: 3px;
    }
    
    /* Cart Item */
    .cart-item {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        margin-bottom: 8px;
        padding: 10px;
        transition: all 0.3s;
    }
    
    .cart-item:hover {
        background: rgba(212, 175, 55, 0.08);
        transform: translateX(3px);
    }
    
    /* Payment Method Buttons */
    .payment-method {
        padding: 10px;
        border-radius: 12px;
        border: 1px solid rgba(212, 175, 55, 0.2);
        background: transparent;
        transition: all 0.3s;
        font-weight: 500;
    }
    
    .payment-method:hover {
        border-color: var(--accent-gold);
        background: rgba(212, 175, 55, 0.1);
    }
    
    .payment-method.active {
        background: var(--accent-gold);
        color: var(--primary-black);
        border-color: var(--accent-gold);
    }
    
    /* Category Badge */
    .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: rgba(212, 175, 55, 0.1);
        border-radius: 20px;
        margin-bottom: 15px;
    }
    
    /* Input Styling */
    .form-control-custom {
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 12px;
        padding: 10px 15px;
        color: var(--text-light);
        transition: all 0.3s;
    }
    
    .form-control-custom:focus {
        background: rgba(0, 0, 0, 0.6);
        border-color: var(--accent-gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        outline: none;
    }
    
    /* Button Primary */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
        border: none;
        padding: 12px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
    }
    
    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.show {
        display: flex;
    }
    
    .spinner-custom {
        width: 50px;
        height: 50px;
        border: 3px solid rgba(212, 175, 55, 0.3);
        border-top-color: var(--accent-gold);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Empty State */
    .empty-cart {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-gray);
    }
    
    .empty-cart i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    /* Queue Badge Animation */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .queue-badge {
        animation: slideInRight 0.3s ease;
    }
    
    /* Tax Info */
    .tax-info {
        font-size: 0.75rem;
        color: var(--text-gray);
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="row g-3">
        <!-- Left Panel - Services -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-cut me-2" style="color: var(--accent-gold);"></i>
                            Pilih Layanan
                        </h5>
                        <small class="text-muted">Klik layanan untuk menambah ke keranjang</small>
                    </div>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text" style="background: rgba(212, 175, 55, 0.1); border-color: rgba(212, 175, 55, 0.2);">
                            <i class="fas fa-search" style="color: var(--accent-gold);"></i>
                        </span>
                        <input type="text" id="searchService" class="form-control" placeholder="Cari layanan...">
                        <button class="btn btn-outline-primary" id="clearSearch" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="services-list" id="servicesList">
                        @foreach($categories as $category)
                            <div class="mb-4">
                                <div class="category-badge">
                                    <i class="fas fa-tag me-1"></i>
                                    <span class="text-uppercase small fw-bold" style="color: var(--accent-gold);">{{ $category->name }}</span>
                                    <span class="text-muted ms-2">({{ $category->services->count() }})</span>
                                </div>
                                <div class="row g-2">
                                    @foreach($category->services as $service)
                                        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-4 col-6">
                                            <div class="card service-card h-100" 
                                                 data-id="{{ $service->id }}"
                                                 data-name="{{ $service->name }}"
                                                 data-price="{{ $service->price }}"
                                                 data-duration="{{ $service->duration }}">
                                                <div class="card-body text-center p-3">
                                                    <div class="service-icon mb-2">
                                                        <i class="fas fa-cut fa-2x" style="color: var(--accent-gold);"></i>
                                                    </div>
                                                    <h6 class="mb-1 text-light small">{{ str($service->name)->limit(20) }}</h6>
                                                    <p class="mb-0 service-price" style="color: var(--accent-gold); font-weight: 600; font-size: 0.9rem;">
                                                        <span data-currency-value="{{ $service->price }}">{{ number_format($service->price, 0, ',', '.') }}</span>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $service->duration }} menit
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Cart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card cart-card">
                <div class="cart-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2" style="color: var(--accent-gold);"></i>
                                Keranjang
                            </h5>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" id="clearCartBtn" title="Kosongkan Keranjang">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Total item: <strong id="cartItemCount">0</strong></small>
                    </div>
                </div>
                
                <div class="cart-body">
                    <!-- Customer Selection -->
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold" style="color: var(--accent-gold);">
                            <i class="fas fa-user me-1"></i> Pilih Pelanggan
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: rgba(212, 175, 55, 0.1);">
                                <i class="fas fa-search" style="color: var(--accent-gold);"></i>
                            </span>
                            <input type="text" id="customerSearch" class="form-control" placeholder="Cari nama atau no HP...">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <input type="hidden" id="customer_id">
                        <div id="customerInfo" class="mt-2" style="display: none;"></div>
                    </div>
                    
                    <!-- Cart Items -->
                    <div id="cart" class="mb-3">
                        <div id="cartItems"></div>
                        <div id="cartFooter" style="display: none;">
                            <div class="mt-3 pt-2 border-top border-secondary">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal</span>
                                    <strong id="subtotal">Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Pajak <span id="taxRateDisplay"></span></span>
                                    <span id="tax">Rp 0</span>
                                </div>
                                <div id="serviceChargeRow" class="d-flex justify-content-between mb-1" style="display: none;">
                                    <span>Service Charge</span>
                                    <span id="serviceCharge">Rp 0</span>
                                </div>
                                <div id="memberDiscountRow" class="d-flex justify-content-between mb-1 text-success" style="display: none;">
                                    <span>Diskon Member <span id="memberDiscountPercent"></span></span>
                                    <span id="memberDiscount">Rp 0</span>
                                </div>
                                <div id="pointsDiscountRow" class="d-flex justify-content-between mb-1 text-success" style="display: none;">
                                    <span>Diskon Poin</span>
                                    <span id="pointsDiscount">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2 pt-2 border-top border-secondary">
                                    <span class="fw-bold">Total</span>
                                    <strong id="total" style="color: var(--accent-gold); font-size: 1.3rem;">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Points Section -->
                    <div id="pointsSection" style="display: none;">
                        <div class="mb-3 p-2 rounded" style="background: rgba(212, 175, 55, 0.05);">
                            <label class="form-label small mb-1">
                                <i class="fas fa-gem me-1" style="color: var(--accent-gold);"></i>
                                Poin tersedia: <strong id="availablePoints" style="color: var(--accent-gold);">0</strong>
                            </label>
                            <div class="input-group">
                                <input type="number" id="pointsUsed" class="form-control" min="0" step="10" value="0">
                                <span class="input-group-text">poin</span>
                                <button class="btn btn-outline-primary" type="button" id="maxPointsBtn">Max</button>
                            </div>
                            <small class="text-muted" id="pointsConversionInfo">10 poin = Rp 1.000 diskon</small>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold" style="color: var(--accent-gold);">
                            <i class="fas fa-credit-card me-1"></i> Metode Pembayaran
                        </label>
                        <div class="d-flex gap-2">
                            <button type="button" class="payment-method flex-fill active" data-method="cash">
                                <i class="fas fa-money-bill-wave me-1"></i> Tunai
                            </button>
                            <button type="button" class="payment-method flex-fill" data-method="qris">
                                <i class="fas fa-qrcode me-1"></i> QRIS
                            </button>
                            <button type="button" class="payment-method flex-fill" data-method="debit">
                                <i class="fas fa-credit-card me-1"></i> Debit
                            </button>
                        </div>
                        <input type="hidden" id="paymentMethod" value="cash">
                    </div>
                    
                    <!-- Cash Payment -->
                    <div id="cashPayment">
                        <div class="mb-3">
                            <label class="form-label small">Jumlah Bayar</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold" id="currencySymbol">Rp</span>
                                <input type="text" id="paidAmount" class="form-control" placeholder="0">
                            </div>
                            <div id="changeInfo" class="alert alert-success mt-2 d-none">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Kembalian: <strong id="changeAmount">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Process Button -->
                    <button id="processPayment" class="btn btn-primary-custom w-100 mt-2" disabled>
                        <i class="fas fa-check-circle me-2"></i> Proses Pembayaran
                    </button>
                    
                    <!-- Back Button -->
                    @if(request()->has('queue_id'))
                        <a href="{{ route('queue.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Antrian
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="newCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2" style="color: var(--accent-gold);"></i>
                    Tambah Pelanggan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newCustomerForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor HP *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2" style="color: var(--accent-gold);"></i>
                    Struk Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body receipt-content" id="receiptContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-custom"></div>
</div>

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
// =====================================================
// GLOBAL VARIABLES
// =====================================================
let cart = [];
let subtotal = 0;
let total = 0;
let currentCustomer = null;

// Settings from global appSettings
let appSettings = null;
let taxRate = 11;
let serviceCharge = 0;
let enablePoints = true;
let pointValue = 100;
let pointsConversionRate = 1000; // 10 poin = 1000 rupiah
let currencySymbol = 'Rp';

// =====================================================
// LOAD SETTINGS FROM GLOBAL
// =====================================================
function loadSettings() {
    if (window.appSettings) {
        appSettings = window.appSettings;
        taxRate = parseFloat(appSettings.get('taxRate')) || 11;
        serviceCharge = parseFloat(appSettings.get('serviceCharge')) || 0;
        enablePoints = appSettings.get('enablePoints') || true;
        pointValue = parseFloat(appSettings.get('pointValue')) || 100;
        
        // Update UI with settings
        $('#taxRateDisplay').text(`(${taxRate}%)`);
        $('.tax-info').text(`Pajak ${taxRate}%`);
        
        // Update currency symbol
        currencySymbol = appSettings.get('currency') === 'IDR' ? 'Rp' : '$';
        $('#currencySymbol').text(currencySymbol);
        
        // Update points conversion info
        const pointsPerRp = 10; // 10 poin
        const rpValue = pointsPerRp * pointValue;
        $('#pointsConversionInfo').text(`${pointsPerRp} poin = ${currencySymbol} ${formatNumber(rpValue)} diskon`);
        
        // Show/hide service charge row
        if (serviceCharge > 0) {
            $('#serviceChargeRow').show();
        } else {
            $('#serviceChargeRow').hide();
        }
        
        // Apply currency to all service prices
        applyCurrencyToServicePrices();
    } else {
        // Fallback: wait for appSettings
        setTimeout(loadSettings, 100);
    }
}

function applyCurrencyToServicePrices() {
    if (!window.appSettings) return;
    
    $('.service-price span[data-currency-value]').each(function() {
        const value = parseFloat($(this).attr('data-currency-value'));
        if (!isNaN(value)) {
            $(this).text(window.appSettings.formatCurrency(value));
        }
    });
}

// =====================================================
// HELPER FUNCTIONS
// =====================================================

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(Math.round(number));
}

function formatCurrency(amount) {
    if (window.appSettings) {
        return window.appSettings.formatCurrency(amount);
    }
    return `${currencySymbol} ${formatNumber(amount)}`;
}

function getMemberDiscount(level) {
    if (window.appSettings) {
        return window.appSettings.getMemberDiscount(level);
    }
    const discounts = { silver: 5, gold: 10, platinum: 15 };
    return discounts[level] || 0;
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function showToast(type, message) {
    const toastHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mb-2" role="alert" style="animation: slideInRight 0.3s ease; position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('.toast-notification-global').remove();
    $('body').append('<div class="toast-notification-global"></div>');
    $('.toast-notification-global').append(toastHtml);
    setTimeout(() => $('.toast-notification-global .alert').fadeOut('slow', function() { $(this).parent().remove(); }), 3000);
}

function updateCart() {
    let html = '';
    subtotal = 0;
    
    if (cart.length === 0) {
        html = `<div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p class="mb-0">Keranjang masih kosong</p>
                    <small>Klik layanan untuk menambah</small>
                </div>`;
        $('#cartFooter').hide();
    } else {
        cart.forEach((item, index) => {
            subtotal += item.price;
            html += `<div class="cart-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong class="text-light">${escapeHtml(item.name)}</strong>
                                ${item.from_queue ? '<br><small class="text-muted"><i class="fas fa-ticket-alt me-1"></i>Dari Antrian</small>' : ''}
                            </div>
                            <div class="text-end">
                                <div>${formatCurrency(item.price)}</div>
                                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${index})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
        });
        $('#cartFooter').show();
    }
    
    $('#cartItems').html(html);
    $('#cartItemCount').text(cart.length);
    calculateTotal();
}

function calculateTotal() {
    let tax = subtotal * (taxRate / 100);
    let memberDiscount = 0;
    let pointsDiscount = 0;
    let memberPercent = 0;
    
    if (currentCustomer) {
        memberPercent = getMemberDiscount(currentCustomer.member_level);
        memberDiscount = subtotal * (memberPercent / 100);
        
        let pointsUsed = parseInt($('#pointsUsed').val()) || 0;
        // Points conversion: points * (pointValue / 10) for discount in rupiah
        pointsDiscount = Math.floor(pointsUsed / 10) * pointValue;
    }
    
    let discount = memberDiscount + pointsDiscount;
    total = subtotal + tax + serviceCharge - discount;
    
    // Ensure total is not negative
    if (total < 0) total = 0;
    
    // Update displays
    $('#subtotal').text(formatCurrency(subtotal));
    $('#tax').text(formatCurrency(tax));
    
    if (serviceCharge > 0) {
        $('#serviceCharge').text(formatCurrency(serviceCharge));
        $('#serviceChargeRow').show();
    } else {
        $('#serviceChargeRow').hide();
    }
    
    if (memberDiscount > 0) {
        $('#memberDiscount').text(formatCurrency(memberDiscount));
        $('#memberDiscountPercent').text(`(${memberPercent}%)`);
        $('#memberDiscountRow').show();
    } else {
        $('#memberDiscountRow').hide();
    }
    
    if (pointsDiscount > 0) {
        $('#pointsDiscount').text(formatCurrency(pointsDiscount));
        $('#pointsDiscountRow').show();
    } else {
        $('#pointsDiscountRow').hide();
    }
    
    $('#total').text(formatCurrency(total));
    checkPayment();
}

function checkPayment() {
    let paid = parseFloat($('#paidAmount').val().replace(/[^0-9]/g, '')) || 0;
    let paymentMethod = $('#paymentMethod').val();
    
    if (paymentMethod !== 'cash') {
        paid = Math.ceil(total / 1000) * 1000;
        $('#paidAmount').val(formatNumber(paid));
    }
    
    if (paid >= total && total > 0 && cart.length > 0) {
        $('#processPayment').prop('disabled', false);
        let change = paid - total;
        $('#changeAmount').text(formatCurrency(change));
        $('#changeInfo').removeClass('d-none');
    } else {
        $('#processPayment').prop('disabled', true);
        $('#changeInfo').addClass('d-none');
    }
}

function removeFromCart(index) {
    let removed = cart[index];
    cart.splice(index, 1);
    updateCart();
    showToast('info', `${removed.name} dihapus dari keranjang`);
}

// =====================================================
// LOAD QUEUE FROM URL
// =====================================================
(function loadQueueFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('service_id');
    const serviceName = urlParams.get('service_name');
    const servicePrice = urlParams.get('service_price');
    const customerName = urlParams.get('customer_name');
    const customerPhone = urlParams.get('customer_phone');
    const queueNumber = urlParams.get('queue_number');
    
    if (serviceId && serviceName && servicePrice && serviceId !== 'null') {
        cart.push({
            id: parseInt(serviceId),
            name: decodeURIComponent(serviceName),
            price: parseFloat(servicePrice),
            from_queue: true,
            queue_id: urlParams.get('queue_id')
        });
        
        if (queueNumber && queueNumber !== 'null') {
            $('.cart-header .d-flex').append(`
                <span class="badge ms-2" style="background: var(--accent-gold); color: var(--primary-black);">
                    <i class="fas fa-people-arrows me-1"></i> ${decodeURIComponent(queueNumber)}
                </span>
            `);
        }
        
        if (customerName && customerName !== 'null') {
            const decodedName = decodeURIComponent(customerName);
            const decodedPhone = customerPhone && customerPhone !== 'null' ? decodeURIComponent(customerPhone) : '';
            $('#customerSearch').val(decodedName);
            $('#customerInfo').html(`
                <div class="alert alert-info queue-badge">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pelanggan dari Antrian:</strong><br>
                    ${decodedName}
                    ${decodedPhone ? '<br><small>' + decodedPhone + '</small>' : ''}
                </div>
            `);
            
            $.ajax({
                url: '{{ route("pos.search-customer") }}',
                data: {search: decodedName},
                success: function(data) {
                    let matched = data.find(c => c.name === decodedName || c.phone === decodedPhone);
                    if (matched) {
                        $('#customer_id').val(matched.id);
                        currentCustomer = matched;
                        if (enablePoints) {
                            $('#pointsSection').show();
                            $('#availablePoints').text(matched.points);
                        }
                        calculateTotal();
                    }
                }
            });
        }
        
        updateCart();
        showToast('success', `Antrian ${decodeURIComponent(queueNumber || '')} dimuat`);
    }
})();

// =====================================================
// DOCUMENT READY
// =====================================================
$(document).ready(function() {
    // Load settings
    loadSettings();
    
    // Search service
    $('#searchService').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('.service-card').each(function() {
            let name = $(this).data('name').toLowerCase();
            $(this).closest('.col-xl-3, .col-lg-4, .col-md-3, .col-sm-4, .col-6').toggle(name.indexOf(value) > -1);
        });
    });
    
    $('#clearSearch').click(function() {
        $('#searchService').val('').trigger('keyup');
    });
    
    // Add to cart
    $('.service-card').click(function() {
        let service = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            price: parseFloat($(this).data('price')),
            from_queue: false
        };
        cart.push(service);
        updateCart();
        $(this).addClass('border-success');
        setTimeout(() => $(this).removeClass('border-success'), 300);
    });
    
    // Customer autocomplete
    $('#customerSearch').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route("pos.search-customer") }}',
                data: {search: request.term},
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: `${item.name} - ${item.phone}`,
                            value: item.name,
                            id: item.id,
                            points: item.points,
                            member_level: item.member_level,
                            phone: item.phone
                        };
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {
            currentCustomer = ui.item;
            $('#customer_id').val(ui.item.id);
            $('#customerInfo').html(`
                <div class="p-2 rounded" style="background: rgba(212, 175, 55, 0.1);">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${ui.item.label}</strong>
                            <br><small class="text-muted">${ui.item.phone}</small>
                        </div>
                        <span class="badge" style="background: var(--accent-gold);">${ui.item.member_level.toUpperCase()}</span>
                    </div>
                </div>
            `);
            if (enablePoints) {
                $('#availablePoints').text(ui.item.points);
                $('#pointsSection').show();
                $('#pointsUsed').attr('max', ui.item.points).val(0);
            }
            calculateTotal();
        }
    });
    
    $('#maxPointsBtn').click(function() {
        let maxPoints = parseInt($('#pointsUsed').attr('max')) || 0;
        $('#pointsUsed').val(maxPoints);
        calculateTotal();
    });
    
    $('#pointsUsed').on('change keyup', function() {
        let maxPoints = parseInt($('#pointsUsed').attr('max')) || 0;
        let value = parseInt($(this).val()) || 0;
        if (value > maxPoints) $(this).val(maxPoints);
        if (value < 0) $(this).val(0);
        calculateTotal();
    });
    
    $('.payment-method').click(function() {
        $('.payment-method').removeClass('active');
        $(this).addClass('active');
        let method = $(this).data('method');
        $('#paymentMethod').val(method);
        if (method === 'cash') {
            $('#cashPayment').show();
            $('#paidAmount').val('');
        } else {
            $('#cashPayment').hide();
            let paidAmount = Math.ceil(total / 1000) * 1000;
            $('#paidAmount').val(formatNumber(paidAmount));
            checkPayment();
        }
    });
    
    $('#paidAmount').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(value);
        checkPayment();
    });
    
    $('#processPayment').click(function() {
        if (cart.length === 0) {
            showToast('error', 'Keranjang masih kosong!');
            return;
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        const queueId = urlParams.get('queue_id');
        let paidAmount = parseFloat($('#paidAmount').val().replace(/[^0-9]/g, '')) || 0;
        
        if ($('#paymentMethod').val() !== 'cash' && paidAmount === 0) {
            paidAmount = Math.ceil(total / 1000) * 1000;
        }
        
        let button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');
        $('#loadingOverlay').addClass('show');
        
        let postData = {
            _token: '{{ csrf_token() }}',
            customer_id: $('#customer_id').val(),
            services: cart.map(item => ({ id: item.id, barber_id: null })),
            payment_method: $('#paymentMethod').val(),
            paid_amount: paidAmount,
            points_used: $('#pointsUsed').val() || 0,
            tax_rate: taxRate,
            service_charge: serviceCharge
        };
        
        if (queueId) postData.queue_id = queueId;
        
        $.ajax({
            url: '{{ route("pos.process") }}',
            method: 'POST',
            data: postData,
            success: function(response) {
                if (response.success) {
                    showToast('success', 'Transaksi berhasil! Invoice: ' + response.invoice_number);
                    if (response.receipt_html) {
                        $('#receiptContent').html(response.receipt_html);
                        $('#receiptModal').modal('show');
                    }
                    // Check if auto print is enabled
                    if (window.appSettings && window.appSettings.get('autoPrint')) {
                        setTimeout(() => window.print(), 500);
                    }
                    setTimeout(() => {
                        if (queueId) {
                            window.location.href = '{{ route("queue.index") }}';
                        } else {
                            location.reload();
                        }
                    }, 2000);
                } else {
                    showToast('error', response.message || 'Terjadi kesalahan');
                    button.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Proses Pembayaran');
                    $('#loadingOverlay').removeClass('show');
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses transaksi';
                showToast('error', message);
                button.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Proses Pembayaran');
                $('#loadingOverlay').removeClass('show');
            }
        });
    });
    
    $('#clearCartBtn').click(function() {
        if (cart.length > 0 && confirm('Yakin ingin mengosongkan keranjang?')) {
            cart = [];
            updateCart();
            showToast('info', 'Keranjang telah dikosongkan');
        }
    });
    
    $('#newCustomerForm').submit(function(e) {
        e.preventDefault();
        let button = $(this).find('button[type="submit"]');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
        
        $.ajax({
            url: '{{ route("customers.store") }}',
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                $('#newCustomerModal').modal('hide');
                showToast('success', 'Pelanggan berhasil ditambahkan!');
                if (response.id && response.name) {
                    $('#customerSearch').val(response.name);
                    $('#customer_id').val(response.id);
                    currentCustomer = response;
                    if (enablePoints) {
                        $('#pointsSection').show();
                        $('#availablePoints').text(response.points || 0);
                    }
                    calculateTotal();
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal menambahkan pelanggan';
                showToast('error', message);
                button.prop('disabled', false).html('Simpan');
            },
            complete: function() {
                button.prop('disabled', false).html('Simpan');
            }
        });
    });
    
    // Listen for settings changes
    if (window.appSettings) {
        window.appSettings.onChange(function(settings) {
            taxRate = parseFloat(settings.taxRate) || 11;
            serviceCharge = parseFloat(settings.serviceCharge) || 0;
            enablePoints = settings.enablePoints || true;
            pointValue = parseFloat(settings.pointValue) || 100;
            currencySymbol = settings.currency === 'IDR' ? 'Rp' : '$';
            
            $('#taxRateDisplay').text(`(${taxRate}%)`);
            if (serviceCharge > 0) {
                $('#serviceChargeRow').show();
            } else {
                $('#serviceChargeRow').hide();
            }
            
            if (!enablePoints) {
                $('#pointsSection').hide();
                $('#pointsUsed').val(0);
            }
            
            applyCurrencyToServicePrices();
            calculateTotal();
        });
    }
});
</script>
@endpush
@endsection