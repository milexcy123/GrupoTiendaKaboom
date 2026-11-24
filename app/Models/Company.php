<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'primary_color',
        'secondary_color',
        'phone',
        'email',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}