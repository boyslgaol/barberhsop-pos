<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getActiveServicesAttribute()
    {
        return $this->services()->where('is_active', true)->get();
    }
}