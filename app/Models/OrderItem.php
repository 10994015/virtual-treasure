<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_name',
        'product_category',
        'game_type',
        'game_server',
        'game_region',
        'rarity',
        'product_description',
        'product_specifications',
        'product_image',
        'original_price',
        'price',
        'quantity',
        'subtotal',
        'delivery_method',
        'delivery_instructions',
        'delivery_status',
        'delivery_code',
        'delivery_info',
        'delivered_at',
        'rating',
        'review',
        'reviewed_at',
    ];

    protected $casts = [
        'product_specifications' => 'array',
        'delivery_info' => 'array',
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'delivered_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Scopes
    public function scopeDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    public function scopePending($query)
    {
        return $query->where('delivery_status', 'pending');
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeReviewed($query)
    {
        return $query->whereNotNull('rating');
    }

    // Accessors
    public function getIsDeliveredAttribute()
    {
        return $this->delivery_status === 'delivered';
    }

    public function getIsReviewedAttribute()
    {
        return $this->rating !== null;
    }

    // Methods
    public function markAsDelivered($deliveryCode = null, $deliveryInfo = null)
    {
        $this->update([
            'delivery_status' => 'delivered',
            'delivery_code' => $deliveryCode,
            'delivery_info' => $deliveryInfo,
            'delivered_at' => now(),
        ]);
    }

    public function addReview($rating, $review)
    {
        $this->update([
            'rating' => $rating,
            'review' => $review,
            'reviewed_at' => now(),
        ]);
    }

    public function productCodes()
    {
        return $this->hasMany(ProductCode::class, 'order_id', 'order_id')
                    ->where('product_id', $this->product_id);
    }

}
