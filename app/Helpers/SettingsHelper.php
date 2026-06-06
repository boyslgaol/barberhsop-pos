<?php

namespace App\Helpers;

class SettingsHelper
{
    protected static $settings = null;
    
    public static function get($key, $default = null)
    {
        // Bisa diambil dari database atau session
        return session("settings.$key", $default);
    }
    
    public static function formatCurrency($amount)
    {
        $currency = self::get('currency', 'IDR');
        if ($currency === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
        return '$ ' . number_format($amount, 2, '.', ',');
    }
    
    public static function calculateTax($amount)
    {
        $taxRate = self::get('taxRate', 11);
        return $amount * ($taxRate / 100);
    }
}