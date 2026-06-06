<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Queue extends Model
{
    protected $table = 'queues';
    
    protected $fillable = [
        'queue_number', 'customer_name', 'customer_phone', 'service_id', 'barber_id',
        'transaction_id', 'estimated_price', 'estimated_duration', 'position', 'status', 
        'queue_time', 'call_time', 'start_time', 'end_time', 'notes', 'cancellation_reason', 'created_by'
    ];
    
    protected $casts = [
        'queue_time' => 'datetime',
        'call_time' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'estimated_price' => 'decimal:2'
    ];
    
    // ==============================================
    // RELATIONSHIPS
    // ==============================================
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function barber()
    {
        return $this->belongsTo(User::class, 'barber_id');
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // ==============================================
    // STATIC METHODS
    // ==============================================
    
    public static function generateQueueNumber()
    {
        $today = Carbon::now('Asia/Jakarta')->format('Ymd');
        $last = self::whereDate('queue_time', Carbon::today('Asia/Jakarta'))->orderBy('id', 'desc')->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->queue_number, -4));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return 'Q' . $today . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    // ==============================================
    // ACCESSORS (Attribute Methods)
    // ==============================================
    
    /**
     * Get waiting time in minutes (since queue created)
     */
    public function getWaitingTimeAttribute()
    {
        if ($this->queue_time) {
            return $this->queue_time->diffInMinutes(Carbon::now('Asia/Jakarta'));
        }
        return 0;
    }
    
    /**
     * Get formatted waiting time
     */
    public function getFormattedWaitingTimeAttribute()
    {
        $minutes = $this->waiting_time;
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . ' jam ' . $mins . ' menit';
    }
    
    /**
     * Get service duration in minutes
     */
    public function getServiceTimeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInMinutes($this->end_time);
        }
        return null;
    }
    
    /**
     * Get formatted queue time
     */
    public function getFormattedQueueTimeAttribute()
    {
        return $this->queue_time ? $this->queue_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-';
    }
    
    /**
     * Get formatted call time
     */
    public function getFormattedCallTimeAttribute()
    {
        return $this->call_time ? $this->call_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-';
    }
    
    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-';
    }
    
    /**
     * Get formatted end time
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->setTimezone('Asia/Jakarta')->format('H:i:s') : '-';
    }
    
    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'waiting' => '<span class="badge" style="background: #f59e0b; color: black;"><i class="fas fa-clock me-1"></i> Menunggu</span>',
            'calling' => '<span class="badge" style="background: #3b82f6; color: white;"><i class="fas fa-bell me-1"></i> Dipanggil</span>',
            'in_service' => '<span class="badge" style="background: #10b981; color: white;"><i class="fas fa-cut me-1"></i> Dilayani</span>',
            'completed' => '<span class="badge" style="background: #8b5cf6; color: white;"><i class="fas fa-check-circle me-1"></i> Selesai</span>',
            'cancelled' => '<span class="badge" style="background: #ef4444; color: white;"><i class="fas fa-times-circle me-1"></i> Dibatalkan</span>'
        ];
        
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
    }
    
    /**
     * Get payment status badge HTML
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->transaction_id) {
            return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Lunas</span>';
        }
        return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Belum Dibayar</span>';
    }
    
    /**
     * Get simple payment status text
     */
    public function getPaymentStatusTextAttribute()
    {
        if ($this->transaction_id) {
            return 'Lunas';
        }
        return 'Belum Dibayar';
    }
    
    /**
     * Get status text (non-HTML)
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'waiting' => 'Menunggu',
            'calling' => 'Dipanggil',
            'in_service' => 'Dilayani',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
        
        return $statuses[$this->status] ?? ucfirst($this->status);
    }
    
    // ==============================================
    // HELPER METHODS
    // ==============================================
    
    /**
     * Check if queue is paid
     */
    public function isPaid()
    {
        return !is_null($this->transaction_id);
    }
    
    /**
     * Check if queue can be paid
     */
    public function canBePaid()
    {
        return $this->status === 'completed' && is_null($this->transaction_id);
    }
    
    /**
     * Get payment URL
     */
    public function getPaymentUrlAttribute()
    {
        if ($this->canBePaid()) {
            return route('pos.from-queue', $this->id);
        }
        return null;
    }
    
    /**
     * Check if queue is active (waiting, calling, or in_service)
     */
    public function isActive()
    {
        return in_array($this->status, ['waiting', 'calling', 'in_service']);
    }
    
    /**
     * Check if queue is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    
    /**
     * Check if queue is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
    
    /**
     * Get position in queue with ordinal suffix
     */
    public function getPositionTextAttribute()
    {
        if (!$this->position) {
            return '-';
        }
        
        $suffix = 'th';
        if ($this->position == 1) $suffix = 'st';
        elseif ($this->position == 2) $suffix = 'nd';
        elseif ($this->position == 3) $suffix = 'rd';
        
        return $this->position . $suffix;
    }
    
    // ==============================================
    // SCOPES
    // ==============================================
    
    /**
     * Scope a query to only include active queues
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'calling', 'in_service']);
    }
    
    /**
     * Scope a query to only include waiting queues
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }
    
    /**
     * Scope a query to only include completed queues
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope a query to only include unpaid queues
     */
    public function scopeUnpaid($query)
    {
        return $query->whereNull('transaction_id');
    }
    
    /**
     * Scope a query to only include paid queues
     */
    public function scopePaid($query)
    {
        return $query->whereNotNull('transaction_id');
    }
    
    /**
     * Scope a query for today's queues
     */
    public function scopeToday($query)
    {
        return $query->whereDate('queue_time', Carbon::today('Asia/Jakarta'));
    }
}