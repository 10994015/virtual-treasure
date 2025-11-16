<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'type',
        'content',
        'image_path',
        'bargain_price',
        'related_message_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'bargain_price' => 'decimal:2',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function relatedMessage()
    {
        return $this->belongsTo(Message::class, 'related_message_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeBargainMessages($query)
    {
        return $query->whereIn('type', [
            'bargain',
            'bargain_counter',
            'bargain_accept',
            'bargain_reject',
            'bargain_deal'
        ]);
    }

    // Methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function isBargainMessage()
    {
        return in_array($this->type, [
            'bargain',
            'bargain_counter',
            'bargain_accept',
            'bargain_reject',
            'bargain_deal'
        ]);
    }

    public function getFormattedMessage()
    {
        switch ($this->type) {
            case 'bargain':
                return "議價：NT$ " . number_format($this->bargain_price);
            case 'bargain_counter':
                return "反議價：NT$ " . number_format($this->bargain_price);
            case 'bargain_accept':
                return "已接受議價";
            case 'bargain_reject':
                return "已拒絕議價";
            case 'bargain_deal':
                return "成交價：NT$ " . number_format($this->bargain_price);
            case 'system':
                return $this->content;
            case 'image':
                return "[圖片]";
            default:
                return $this->content;
        }
    }
}
