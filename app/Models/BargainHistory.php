<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BargainHistory extends Model
{
    use HasFactory;

    protected $table = 'bargain_history';

    protected $fillable = [
        'product_id',
        'conversation_id',
        'buyer_id',
        'seller_id',
        'original_price',
        'buyer_offer',
        'seller_offer',
        'final_price',
        'status',
        'round',
        'offered_at',
        'responded_at',
        'expired_at',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'buyer_offer' => 'decimal:2',
        'seller_offer' => 'decimal:2',
        'final_price' => 'decimal:2',
        'offered_at' => 'datetime',
        'responded_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isExpired()
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function accept($finalPrice = null)
    {
        $this->update([
            'status' => 'accepted',
            'final_price' => $finalPrice ?? $this->buyer_offer ?? $this->seller_offer,
            'responded_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
    }

    public function counter($price)
    {
        $this->update([
            'status' => 'countered',
            'seller_offer' => $price,
            'responded_at' => now(),
        ]);
    }

    public function deal($finalPrice)
    {
        $this->update([
            'status' => 'deal',
            'final_price' => $finalPrice,
            'responded_at' => now(),
        ]);
    }
}
