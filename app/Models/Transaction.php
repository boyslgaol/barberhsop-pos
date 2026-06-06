<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'user_id', 'transaction_date',
        'subtotal', 'tax', 'discount', 'service_fee', 'total', 'paid_amount',
        'change_amount', 'payment_method', 'payment_reference', 'status',
        'points_earned', 'points_used', 'notes'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function getTotalServicesAttribute()
    {
        return $this->details->count();
    }
}