// ========== SISTEM PENGATURAN GLOBAL ==========

// Class Pengaturan Global
class AppSettings {
    constructor() {
        this.settings = this.loadSettings();
        this.listeners = [];
    }
    
    // Load settings dari localStorage
    loadSettings() {
        const defaultSettings = {
            language: 'id',
            dateFormat: 'd/m/Y',
            currency: 'IDR',
            darkMode: true,
            defaultCollapse: false,
            shopName: 'BARBERSHOP',
            shopAddress: 'Jl. Contoh No. 123, Jakarta',
            shopPhone: '+62 812-3456-7890',
            shopEmail: 'info@barbershop.com',
            openTime: '09:00',
            closeTime: '21:00',
            openEveryday: true,
            taxRate: 11,
            serviceCharge: 0,
            enablePoints: true,
            pointValue: 100,
            minPointsRedeem: 100,
            silverDiscount: 5,
            goldDiscount: 10,
            platinumDiscount: 15,
            transactionNotification: true,
            stockNotification: true,
            memberNotification: false,
            autoPrint: true,
            paperSize: '80',
            printCopies: 1
        };
        
        const saved = localStorage.getItem('appSettings');
        if (saved) {
            try {
                return { ...defaultSettings, ...JSON.parse(saved) };
            } catch(e) {
                return defaultSettings;
            }
        }
        return defaultSettings;
    }
    
    // Save settings
    saveSettings(newSettings) {
        this.settings = { ...this.settings, ...newSettings };
        localStorage.setItem('appSettings', JSON.stringify(this.settings));
        
        // Terapkan pengaturan ke seluruh halaman
        this.applySettings();
        
        // Trigger event untuk komponen yang mendengarkan
        this.triggerChange();
        
        return this.settings;
    }
    
    // Get setting
    get(key) {
        return this.settings[key];
    }
    
    // Terapkan pengaturan ke seluruh elemen
    applySettings() {
        // 1. Dark Mode
        this.applyDarkMode();
        
        // 2. Nama Barbershop di semua tempat
        this.applyShopName();
        
        // 3. Format mata uang
        this.applyCurrencyFormat();
        
        // 4. Sidebar collapse
        this.applySidebarCollapse();
        
        // 5. Update semua elemen yang depend on settings
        this.updateAllDependentElements();
    }
    
    applyDarkMode() {
        if (this.settings.darkMode) {
            document.body.classList.remove('light-mode');
        } else {
            document.body.classList.add('light-mode');
        }
    }
    
    applyShopName() {
        const elements = document.querySelectorAll('[data-shop-name]');
        elements.forEach(el => {
            el.textContent = this.settings.shopName;
        });
        // Update title page
        if (this.settings.shopName) {
            document.title = `${this.settings.shopName} POS - ${document.title.split(' - ')[1] || ''}`;
        }
    }
    
    applyCurrencyFormat() {
        const elements = document.querySelectorAll('[data-currency]');
        elements.forEach(el => {
            const value = el.getAttribute('data-currency-value');
            if (value) {
                el.textContent = this.formatCurrency(parseFloat(value));
            }
        });
    }
    
    applySidebarCollapse() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (!sidebar) return;
        
        if (this.settings.defaultCollapse && !sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
        }
    }
    
    updateAllDependentElements() {
        // Update semua elemen yang menggunakan pengaturan
        document.querySelectorAll('[data-setting]').forEach(el => {
            const settingKey = el.getAttribute('data-setting');
            const settingValue = this.get(settingKey);
            if (settingValue !== undefined) {
                if (el.tagName === 'INPUT' && el.type === 'checkbox') {
                    el.checked = settingValue;
                } else if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                    el.value = settingValue;
                } else {
                    el.textContent = settingValue;
                }
            }
        });
    }
    
    // Format currency
    formatCurrency(amount) {
        const currency = this.settings.currency;
        if (currency === 'IDR') {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        } else {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(amount);
        }
    }
    
    // Format tanggal
    formatDate(date, format = null) {
        const fmt = format || this.settings.dateFormat;
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        return fmt
            .replace('d', day)
            .replace('m', month)
            .replace('Y', year)
            .replace('y', String(year).slice(-2));
    }
    
    // Hitung pajak
    calculateTax(amount) {
        return amount * (this.settings.taxRate / 100);
    }
    
    // Hitung total dengan pajak dan service charge
    calculateTotal(amount) {
        const tax = this.calculateTax(amount);
        const service = this.settings.serviceCharge;
        return amount + tax + service;
    }
    
    // Hitung diskon berdasarkan level member
    getMemberDiscount(level) {
        const discounts = {
            silver: this.settings.silverDiscount,
            gold: this.settings.goldDiscount,
            platinum: this.settings.platinumDiscount
        };
        return discounts[level.toLowerCase()] || 0;
    }
    
    // Hitung poin dari transaksi
    calculatePoints(amount) {
        if (!this.settings.enablePoints) return 0;
        return Math.floor(amount / this.settings.pointValue);
    }
    
    // Register listener untuk perubahan pengaturan
    onChange(callback) {
        this.listeners.push(callback);
    }
    
    triggerChange() {
        this.listeners.forEach(callback => callback(this.settings));
    }
}

// Inisialisasi instance global
window.appSettings = new AppSettings();

// Inisialisasi saat DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.appSettings.applySettings();
});

// Helper function untuk update harga di POS
window.updatePricesWithSettings = function(basePrice) {
    const tax = window.appSettings.calculateTax(basePrice);
    const service = window.appSettings.get('serviceCharge');
    const total = basePrice + tax + service;
    
    return {
        base: basePrice,
        tax: tax,
        service: service,
        total: total,
        formatted: window.appSettings.formatCurrency(total)
    };
};