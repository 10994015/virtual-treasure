<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CartComponent extends Component
{
    public $cart = [];
    public $cartCount = 0;

    public function mount()
    {
        $this->loadCartFromCookie();
    }

    protected function loadCartFromCookie()
    {
        $cartCookie = request()->cookie('shopping_cart');
        if ($cartCookie) {
            $this->cart = json_decode($cartCookie, true) ?? [];
            $this->cartCount = count($this->cart);

            // 驗證商品是否仍然可購買
            $this->validateCart();
        }
    }

    protected function validateCart()
    {
        $productIds = array_column($this->cart, 'id');
        $products = Product::whereIn('id', $productIds)
            ->where('is_published', true)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $updatedCart = [];
        $hasChanges = false;

        foreach ($this->cart as $item) {
            $product = $products->get($item['id']);

            if (!$product) {
                // 商品不存在或已下架
                $hasChanges = true;
                continue;
            }

            // 更新價格（如果商品價格有變動）
            if ($item['price'] != $product->price) {
                $item['price'] = $product->price;
                $hasChanges = true;
            }

            // 檢查庫存
            if ($product->stock > 0 && $item['quantity'] > $product->stock) {
                $item['quantity'] = $product->stock;
                $hasChanges = true;
            }

            $item['stock'] = $product->stock;
            $updatedCart[] = $item;
        }

        if ($hasChanges) {
            $this->cart = $updatedCart;
            $this->cartCount = count($this->cart);
            $this->saveCartToCookie();

            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '購物車已更新，部分商品價格或庫存有變動'
            ]);
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $quantity = (int) $quantity;
        $stock = $this->cart[$index]['stock'];

        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($stock > 0 && $quantity > $stock) {
            $quantity = $stock;
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '數量已調整為庫存上限'
            ]);
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->saveCartToCookie();
    }

    public function increaseQuantity($index)
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $stock = $this->cart[$index]['stock'];
        $currentQuantity = $this->cart[$index]['quantity'];

        if ($stock > 0 && $currentQuantity >= $stock) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '已達庫存上限'
            ]);
            return;
        }

        $this->cart[$index]['quantity']++;
        $this->saveCartToCookie();
    }

    public function decreaseQuantity($index)
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
            $this->saveCartToCookie();
        }
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            $this->cartCount = count($this->cart);

            $this->saveCartToCookie();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '商品已從購物車移除'
            ]);

            $this->dispatch('cart-updated', ['count' => $this->cartCount]);
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->cartCount = 0;
        $this->saveCartToCookie();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '購物車已清空'
        ]);

        $this->dispatch('cart-updated', ['count' => $this->cartCount]);
    }

    protected function saveCartToCookie()
    {
        cookie()->queue('shopping_cart', json_encode($this->cart), 43200); // 30 天
    }

    public function getSubtotalProperty()
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    public function getTotalProperty()
    {
        return $this->subtotal; // 虛寶商品無運費，總計等於小計
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.cart-component');
    }
}
