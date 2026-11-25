<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'delivery_type',
        'payment_method',
        'total',
        'status',
        'payment_status',
    ];

    protected $appends = ['order_number'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    // Número de pedido que ve el cliente, por ejemplo KE-000012
    public function getOrderNumberAttribute(): string
    {
        return 'KE-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}