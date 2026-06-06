<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';
    
    protected $fillable = ['key', 'value'];
    
    protected $casts = [
        'value' => 'json'
    ];
    
    public static function getValue($key, $default = null)
    {
        $settings = Cache::remember('settings', 3600, function() {
            return self::pluck('value', 'key')->toArray();
        });
        
        return $settings[$key] ?? $default;
    }
    
    public static function setValue($key, $value)
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        Cache::forget('settings');
    }
    
    public static function getAll()
    {
        return Cache::remember('settings', 3600, function() {
            return self::pluck('value', 'key')->toArray();
        });
    }
}