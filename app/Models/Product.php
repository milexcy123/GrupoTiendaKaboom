<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'price',
        'brand',
        'stock',
        'image',
        'category_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    // Oferta vigente con mayor descuento (o null si no hay ninguna)
    public function activeOffer(): ?Offer
    {
        return $this->offers()
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->orderByDesc('discount')
            ->first();
    }

    // Precio final: aplica la oferta vigente si existe
    public function finalPrice(): float
    {
        $precio = (float) $this->price;
        $oferta = $this->activeOffer();

        if ($oferta) {
            $precio = $precio * (1 - ((float) $oferta->discount) / 100);
        }

        return round($precio, 2);
    }
}