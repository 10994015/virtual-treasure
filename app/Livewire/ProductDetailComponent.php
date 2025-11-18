<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductDetailComponent extends Component
{
    public Product $product;
    public $quantity = 1;
    public $selectedImage = null;

    // 稀有度配色
    public $rarityColors = [
        'common' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => '普通'],
        'uncommon' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => '非凡'],
        'rare' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => '稀有'],
        'epic' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => '史詩'],
        'legendary' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => '傳說'],
        'mythic' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => '神話'],
    ];

    // 交付方式
    public $deliveryMethods = [
        'instant' => ['icon' => 'bolt', 'label' => '自動發貨', 'desc' => '付款後立即取得'],
        'manual' => ['icon' => 'handshake', 'label' => '手動交付', 'desc' => '賣家手動交付'],
        'both' => ['icon' => 'layer-group', 'label' => '混合交付', 'desc' => '自動+手動'],
    ];

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->where('status', 'active')
            ->with(['user', 'images'])
            ->firstOrFail();

        // 設定預設圖片
        if ($this->product->images->isNotEmpty()) {
            $primaryImage = $this->product->images->where('is_primary', true)->first();
            $this->selectedImage = $primaryImage ? $primaryImage->image_path : $this->product->images->first()->image_path;
        }

        // 增加瀏覽次數（可選）
        // $this->product->increment('views');
    }

    public function increaseQuantity()
    {
        // 🔥 檢查庫存上限
        if ($this->quantity >= $this->product->stock) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '已達庫存上限'
            ]);
            return;
        }

        $this->quantity++;
    }


    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        // 🔥 檢查庫存（庫存 0 = 已售完）
        if ($this->product->stock === 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '商品已售完'
            ]);
            return;
        }

        if ($this->quantity > $this->product->stock) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '商品庫存不足'
            ]);
            return;
        }

        // 從 Cookie 取得購物車
        $cart = [];
        $cartCookie = request()->cookie('shopping_cart');
        if ($cartCookie) {
            $cart = json_decode($cartCookie, true) ?? [];
        }

        // 🔥 只檢查「原價商品」是否已在購物車（不檢查議價商品）
        $existingIndex = null;
        foreach ($cart as $index => $item) {
            // 🔥 關鍵：只檢查 ID 相同且沒有 conversation_id 的商品
            if ($item['id'] == $this->product->id && !isset($item['conversation_id'])) {
                $existingIndex = $index;
                break;
            }
        }

        // 取得主要圖片
        $image = null;
        if ($this->product->images->isNotEmpty()) {
            $primaryImage = $this->product->images->where('is_primary', true)->first();
            $image = $primaryImage ? $primaryImage->image_path : $this->product->images->first()->image_path;
        }

        if ($existingIndex !== null) {
            // 🔥 更新「原價商品」的數量
            $newQuantity = $cart[$existingIndex]['quantity'] + $this->quantity;

            // 檢查庫存
            if ($this->product->stock > 0 && $newQuantity > $this->product->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => '超過庫存數量'
                ]);
                return;
            }

            $cart[$existingIndex]['quantity'] = $newQuantity;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已更新購物車數量'
            ]);
        } else {
            // 🔥 新增「原價商品」（不綁定 conversation_id）
            $cart[] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'quantity' => $this->quantity,
                'image' => $image,
                'stock' => $this->product->stock,
                'game_type' => $this->product->game_type,
                'category' => $this->product->category,
                // 🔥 關鍵：原價商品不設置 conversation_id、is_bargain、bargain_id
            ];

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已加入購物車'
            ]);
        }

        // 儲存到 Cookie
        cookie()->queue('shopping_cart', json_encode($cart), 43200); // 30 天

        $this->dispatch('cart-updated', ['count' => count($cart)]);

        // 重置數量
        $this->quantity = 1;
    }


    public function buyNow()
    {
        $this->addToCart();
        return redirect()->route('checkout');
    }

    public function selectImage($imagePath)
    {
        $this->selectedImage = $imagePath;
    }

    #[Title('商品詳情')]
    #[Layout('livewire.layouts.app')]
    public function render()
    {
        // 取得相關商品（同分類、同遊戲）
        $relatedProducts = Product::where('is_published', true)
            ->where('status', 'active')
            ->where('id', '!=', $this->product->id)
            ->where(function($query) {
                $query->where('category', $this->product->category)
                      ->orWhere('game_type', $this->product->game_type);
            })
            ->with(['images', 'user'])
            ->take(4)
            ->get();

        return view('livewire.product-detail-component', [
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
