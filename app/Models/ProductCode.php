<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'status',
        'order_id',
        'buyer_id',
        'sold_at',
        'reserved_at',
        'notes',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'reserved_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Methods
    public function markAsSold($orderId, $buyerId)
    {
        $this->update([
            'status' => 'sold',
            'order_id' => $orderId,
            'buyer_id' => $buyerId,
            'sold_at' => now(),
        ]);
    }

    public function reserve($buyerId)
    {
        $this->update([
            'status' => 'reserved',
            'buyer_id' => $buyerId,
            'reserved_at' => now(),
        ]);
    }

    public function unreserve()
    {
        $this->update([
            'status' => 'available',
            'buyer_id' => null,
            'reserved_at' => null,
        ]);
    }
}
