<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'category',
        'game_type',
        'game_server',
        'game_region',
        'rarity',
        'description',
        'specifications',
        'price',
        'original_price',
        'min_price',
        'max_price',
        'is_negotiable',
        'stock',
        'status',
        'is_published',
        'is_featured',
        'delivery_method',
        'delivery_instructions',
        'delivery_time',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'published_at',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(6);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('stock', '>', 0)->orWhere('stock', 0);
        });
    }

    // Accessors
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0 || $this->stock === 0; // 0 表示無限庫存
    }

    public function getHasUnlimitedStockAttribute()
    {
        return $this->stock === 0;
    }


}
