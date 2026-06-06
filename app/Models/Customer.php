<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'birthdate', 'gender',
        'points', 'member_code', 'member_level', 'total_spent', 
        'visit_count', 'last_visit'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'last_visit' => 'date',
        'total_spent' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateMemberCode()
    {
        do {
            $code = 'MBR' . Str::random(6);
        } while (self::where('member_code', $code)->exists());
        
        return $code;
    }

    public function updateMemberLevel()
    {
        if ($this->total_spent >= 5000000) {
            $this->member_level = 'platinum';
        } elseif ($this->total_spent >= 2000000) {
            $this->member_level = 'gold';
        } elseif ($this->total_spent >= 500000) {
            $this->member_level = 'silver';
        } else {
            $this->member_level = 'regular';
        }
        $this->save();
    }

    public function getMemberDiscountAttribute()
    {
        return match($this->member_level) {
            'silver' => 5,
            'gold' => 10,
            'platinum' => 15,
            default => 0,
        };
    }
}