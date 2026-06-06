<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    protected $fillable = [
        'category_id', 'name', 'code', 'price', 'cost', 'duration', 
        'description', 'image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    // ==============================================
    // RELATIONSHIPS
    // ==============================================
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // ==============================================
    // ACCESSORS
    // ==============================================
    
    public function getProfitAttribute()
    {
        return $this->price - ($this->cost ?? 0);
    }

    public function getProfitMarginAttribute()
    {
        if ($this->price > 0) {
            return round(($this->profit / $this->price) * 100, 2);
        }
        return 0;
    }
    
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    public function getFormattedCostAttribute()
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }

    // ==============================================
    // CODE GENERATION
    // ==============================================
    
    /**
     * Generate unique service code
     * Fixed: Now checks for duplicates
     */
    public static function generateCode()
    {
        // Get the highest code number from database
        $lastCode = self::max('code');
        
        if ($lastCode) {
            // Extract number from code (e.g., SRV0001 -> 1)
            $lastNumber = (int) substr($lastCode, 3);
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        // Generate new code
        $newCode = 'SRV' . str_pad($number, 4, '0', STR_PAD_LEFT);
        
        // Check if code already exists (avoid duplicate)
        $attempts = 0;
        while (self::where('code', $newCode)->exists() && $attempts < 100) {
            $number++;
            $newCode = 'SRV' . str_pad($number, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $newCode;
    }
    
    /**
     * Generate random unique code (alternative method)
     */
    public static function generateRandomCode()
    {
        do {
            $code = 'SRV' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
    
    /**
     * Regenerate code for existing service (admin only)
     */
    public function regenerateCode()
    {
        $newCode = self::generateCode();
        $this->update(['code' => $newCode]);
        return $newCode;
    }

    // ==============================================
    // SCOPES
    // ==============================================
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%");
    }

    // ==============================================
    // HELPER METHODS
    // ==============================================
    
    /**
     * Check if service can be deleted
     */
    public function canDelete()
    {
        return $this->transactionDetails()->count() === 0;
    }
    
    /**
     * Get usage count
     */
    public function getUsageCountAttribute()
    {
        return $this->transactionDetails()->count();
    }
    
    /**
     * Toggle service status
     */
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
        return $this->is_active;
    }
}