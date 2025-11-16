<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'product_id',
        'status',
        'last_message',
        'last_message_at',
        'last_message_by',
        'buyer_unread_count',
        'seller_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessageUser()
    {
        return $this->belongsTo(User::class, 'last_message_by');
    }

    public function bargainHistory()
    {
        return $this->hasMany(BargainHistory::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Methods
    public function getOtherUser($userId)
    {
        // 🔥 修復：確保總是返回單個 User 模型而不是 Collection
        if ($this->buyer_id === $userId) {
            // 如果關聯已載入，直接返回
            if ($this->relationLoaded('seller')) {
                return $this->seller;
            }
            // 否則重新查詢
            return $this->seller()->first();
        } else {
            // 如果關聯已載入，直接返回
            if ($this->relationLoaded('buyer')) {
                return $this->buyer;
            }
            // 否則重新查詢
            return $this->buyer()->first();
        }
    }

    public function getUnreadCount($userId)
    {
        return $this->buyer_id === $userId
            ? $this->buyer_unread_count
            : $this->seller_unread_count;
    }

    public function markAsRead($userId)
    {
        if ($this->buyer_id === $userId) {
            $this->update(['buyer_unread_count' => 0]);
        } else {
            $this->update(['seller_unread_count' => 0]);
        }

        // 標記所有未讀訊息為已讀
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function incrementUnreadCount($userId)
    {
        if ($this->buyer_id === $userId) {
            $this->increment('buyer_unread_count');
        } else {
            $this->increment('seller_unread_count');
        }
    }

    public function updateLastMessage($message, $senderId)
    {
        // 🔥 修復：使用 update 方法，不使用 array_merge
        $this->update([
            'last_message' => $message,
            'last_message_at' => now(),
            'last_message_by' => $senderId,
        ]);

        // 🔥 重要：刷新模型以避免後續操作出錯
        $this->refresh();
    }
}
