<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueSetting extends Model
{
    protected $fillable = ['key', 'value'];
    
    public static function getSettings()
    {
        $settings = self::all()->pluck('value', 'key')->toArray();
        
        return [
            'display_message' => $settings['display_message'] ?? 'Selamat Datang di Barbershop',
            'sound_enabled' => $settings['sound_enabled'] ?? true,
            'auto_call' => $settings['auto_call'] ?? false,
            'max_waiting' => $settings['max_waiting'] ?? 10,
            'display_bg_color' => $settings['display_bg_color'] ?? '#000000',
            'display_text_color' => $settings['display_text_color'] ?? '#d4af37'
        ];
    }
}