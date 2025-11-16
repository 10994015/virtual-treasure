<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'total',
        'coupon_code',
        'coupon_discount',
        'status',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'paid_at',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'buyer_game_id',
        'buyer_note',
        'admin_note',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -8));
            }

            // 設定訂單過期時間（例如：未付款訂單3天後過期）
            if (empty($order->expires_at) && $order->payment_status === 'pending') {
                $order->expires_at = now()->addDays(3);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('payment_status', 'pending');
    }

    // Accessors
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->payment_status === 'pending';
    }

    public function getCanCancelAttribute()
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    public function getCanRefundAttribute()
    {
        return $this->payment_status === 'paid' && in_array($this->status, ['paid', 'processing']);
    }

    // Methods
    public function markAsPaid($transactionId = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
            'payment_transaction_id' => $transactionId,
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
            'cancelled_at' => now(),
        ]);
    }
}
