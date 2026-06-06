<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
            }
            return $next($request);
        });
    }

    /**
     * Display settings page
     */
    public function index()
    {
        // Get current settings from session (already saved from frontend modal)
        $settings = [
            'general' => [
                'shop_name' => session('settings.shop_name', 'BARBERSHOP'),
                'shop_address' => session('settings.shop_address', 'Jl. Contoh No. 123, Jakarta'),
                'shop_phone' => session('settings.shop_phone', '+62 812-3456-7890'),
                'shop_email' => session('settings.shop_email', 'info@barbershop.com'),
                'timezone' => config('app.timezone', 'Asia/Jakarta'),
                'date_format' => session('settings.date_format', 'd/m/Y'),
                'currency' => session('settings.currency', 'IDR'),
                'currency_symbol' => session('settings.currency', 'IDR') === 'IDR' ? 'Rp' : '$',
                'tax_rate' => session('settings.tax_rate', 11),
            ],
            'business' => [
                'open_time' => session('settings.open_time', '09:00'),
                'close_time' => session('settings.close_time', '21:00'),
                'days_open' => session('settings.days_open', 'monday,tuesday,wednesday,thursday,friday,saturday'),
                'max_queue' => session('settings.max_queue', 30),
                'auto_call' => session('settings.auto_call', false),
                'sound_enabled' => session('settings.sound_enabled', true),
            ],
            'membership' => [
                'points_rate' => session('settings.point_value', 100),
                'points_redemption' => session('settings.min_points_redeem', 10),
                'silver_threshold' => session('settings.silver_threshold', 500000),
                'gold_threshold' => session('settings.gold_threshold', 2000000),
                'platinum_threshold' => session('settings.platinum_threshold', 5000000),
                'silver_discount' => session('settings.silver_discount', 5),
                'gold_discount' => session('settings.gold_discount', 10),
                'platinum_discount' => session('settings.platinum_discount', 15),
            ],
            'print' => [
                'auto_print' => session('settings.auto_print', true),
                'paper_size' => session('settings.paper_size', '80'),
                'print_copies' => session('settings.print_copies', 1),
                'footer_text' => session('settings.footer_text', 'Terima kasih atas kunjungan Anda'),
            ],
            'notification' => [
                'email_notifications' => session('settings.email_notifications', true),
                'whatsapp_notifications' => session('settings.whatsapp_notifications', false),
                'whatsapp_number' => session('settings.whatsapp_number', ''),
                'admin_email' => session('settings.admin_email', 'admin@barbershop.com'),
                'admin_phone' => session('settings.admin_phone', ''),
            ],
            'backup' => [
                'auto_backup' => session('settings.auto_backup', false),
                'backup_frequency' => session('settings.backup_frequency', 'daily'),
                'backup_location' => session('settings.backup_location', 'storage/backups'),
                'last_backup' => session('settings.last_backup', null),
            ],
            'system' => [
                'app_version' => '2.0.0',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
            ]
        ];
        
        return view('settings.index', compact('settings'));
    }
    
    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string',
            'shop_phone' => 'nullable|string|max:20',
            'shop_email' => 'nullable|email',
            'date_format' => 'required|string',
            'currency' => 'required|string',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);
        
        // Update session settings
        session([
            'settings.shop_name' => $request->shop_name,
            'settings.shop_address' => $request->shop_address,
            'settings.shop_phone' => $request->shop_phone,
            'settings.shop_email' => $request->shop_email,
            'settings.date_format' => $request->date_format,
            'settings.currency' => $request->currency,
            'settings.tax_rate' => $request->tax_rate,
        ]);
        
        // Also update in-appSettings localStorage via response
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan umum berhasil disimpan');
    }
    
    /**
     * Update business settings
     */
    public function updateBusiness(Request $request)
    {
        $request->validate([
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'max_queue' => 'required|integer|min:1|max:100',
            'auto_call' => 'boolean',
            'sound_enabled' => 'boolean',
            'days_open' => 'nullable|array',
        ]);
        
        $daysOpen = $request->days_open ? implode(',', $request->days_open) : '';
        
        session([
            'settings.open_time' => $request->open_time,
            'settings.close_time' => $request->close_time,
            'settings.max_queue' => $request->max_queue,
            'settings.auto_call' => $request->auto_call ? true : false,
            'settings.sound_enabled' => $request->sound_enabled ? true : false,
            'settings.days_open' => $daysOpen,
        ]);
        
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan bisnis berhasil disimpan');
    }
    
    /**
     * Update membership settings
     */
    public function updateMembership(Request $request)
    {
        $request->validate([
            'points_rate' => 'required|integer|min:1',
            'points_redemption' => 'required|integer|min:1',
            'silver_threshold' => 'required|numeric|min:0',
            'gold_threshold' => 'required|numeric|min:0',
            'platinum_threshold' => 'required|numeric|min:0',
            'silver_discount' => 'required|numeric|min:0|max:100',
            'gold_discount' => 'required|numeric|min:0|max:100',
            'platinum_discount' => 'required|numeric|min:0|max:100',
        ]);
        
        session([
            'settings.point_value' => $request->points_rate,
            'settings.min_points_redeem' => $request->points_redemption,
            'settings.silver_threshold' => $request->silver_threshold,
            'settings.gold_threshold' => $request->gold_threshold,
            'settings.platinum_threshold' => $request->platinum_threshold,
            'settings.silver_discount' => $request->silver_discount,
            'settings.gold_discount' => $request->gold_discount,
            'settings.platinum_discount' => $request->platinum_discount,
        ]);
        
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan member berhasil disimpan');
    }
    
    /**
     * Update print settings
     */
    public function updatePrint(Request $request)
    {
        $request->validate([
            'auto_print' => 'boolean',
            'paper_size' => 'required|in:58,80',
            'print_copies' => 'required|integer|min:1|max:3',
            'footer_text' => 'nullable|string|max:255',
        ]);
        
        session([
            'settings.auto_print' => $request->auto_print ? true : false,
            'settings.paper_size' => $request->paper_size,
            'settings.print_copies' => $request->print_copies,
            'settings.footer_text' => $request->footer_text,
        ]);
        
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan cetak berhasil disimpan');
    }
    
    /**
     * Update notification settings
     */
    public function updateNotification(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'whatsapp_number' => 'nullable|string|max:20',
            'admin_email' => 'nullable|email',
            'admin_phone' => 'nullable|string|max:20',
        ]);
        
        session([
            'settings.email_notifications' => $request->email_notifications ? true : false,
            'settings.whatsapp_notifications' => $request->whatsapp_notifications ? true : false,
            'settings.whatsapp_number' => $request->whatsapp_number,
            'settings.admin_email' => $request->admin_email,
            'settings.admin_phone' => $request->admin_phone,
        ]);
        
        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan notifikasi berhasil disimpan');
    }
    
    /**
     * Clear all cache
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        Cache::flush();
        
        return redirect()->back()->with('success', 'Semua cache berhasil dibersihkan');
    }
    
    /**
     * Create database backup - simplified version without mysqldump
     */
    public function createBackup()
    {
        try {
            $backupDir = storage_path('backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . '/' . $filename;
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = "Tables_in_{$databaseName}";
            
            $sql = "-- Barbershop POS Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get create table syntax
                $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
                $sql .= "-- Table: {$tableName}\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Get data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $columns = array_keys((array)$row);
                        $values = array_map(function($value) {
                            return is_null($value) ? 'NULL' : DB::connection()->getPdo()->quote($value);
                        }, array_values((array)$row));
                        
                        $sql .= "INSERT INTO {$tableName} (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }
            
            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
            
            // Write to file
            file_put_contents($filepath, $sql);
            
            if (file_exists($filepath) && filesize($filepath) > 0) {
                session(['settings.last_backup' => now()]);
                
                return response()->download($filepath)->deleteFileAfterSend(true);
            }
            
            return redirect()->back()->with('error', 'Gagal membuat backup database');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Backup database (alias for createBackup)
     */
    public function backupDatabase()
    {
        return $this->createBackup();
    }
    
    /**
     * Get system info
     */
    public function systemInfo()
    {
        $info = [
            'app_name' => session('settings.shop_name', config('app.name')),
            'app_version' => '2.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => DB::connection()->getDatabaseName(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'On' : 'Off',
            'last_backup' => session('settings.last_backup'),
        ];
        
        return response()->json($info);
    }
    
    /**
     * Reset all settings to default
     */
    public function resetSettings()
    {
        $defaults = [
            'shop_name' => 'BARBERSHOP',
            'shop_address' => 'Jl. Contoh No. 123, Jakarta',
            'shop_phone' => '+62 812-3456-7890',
            'shop_email' => 'info@barbershop.com',
            'date_format' => 'd/m/Y',
            'currency' => 'IDR',
            'tax_rate' => 11,
            'open_time' => '09:00',
            'close_time' => '21:00',
            'max_queue' => 30,
            'point_value' => 100,
            'min_points_redeem' => 10,
            'silver_discount' => 5,
            'gold_discount' => 10,
            'platinum_discount' => 15,
            'auto_print' => true,
            'paper_size' => '80',
            'print_copies' => 1,
        ];
        
        foreach ($defaults as $key => $value) {
            session(['settings.' . $key => $value]);
        }
        
        return redirect()->route('settings.index')
            ->with('success', 'Semua pengaturan telah direset ke default');
    }
}