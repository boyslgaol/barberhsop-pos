<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id', 'service_id', 'barber_id', 'price', 'discount', 'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function barber()
    {
        return $this->belongsTo(User::class, 'barber_id');
    }
}