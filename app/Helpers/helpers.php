<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $settings = App\Models\Setting::getValue($key);
        return $settings ?? $default;
    }
}