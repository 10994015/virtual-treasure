<?php

namespace App\Livewire;

use App\Models\BargainHistory;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
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
                $hasChanges = true;
                continue;
            }

            // 🔥 議價商品不更新價格
            $isBargainItem = isset($item['is_bargain']) && $item['is_bargain'] === true;

            // 🔥 一般商品更新價格
            if (!$isBargainItem && $item['price'] != $product->price) {
                $item['price'] = $product->price;
                $hasChanges = true;
            }

            // 🔥 議價商品數量已鎖定，不檢查庫存
            $isLocked = isset($item['locked_quantity']) && $item['locked_quantity'] === true;

            if (!$isLocked && $product->stock > 0 && $item['quantity'] > $product->stock) {
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

        // 🔥 檢查是否為鎖定數量的商品
        if (isset($this->cart[$index]['locked_quantity']) && $this->cart[$index]['locked_quantity']) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '議價商品數量已鎖定，無法修改'
            ]);
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

        // 🔥 檢查是否為鎖定數量的商品
        if (isset($this->cart[$index]['locked_quantity']) && $this->cart[$index]['locked_quantity']) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '議價商品數量已鎖定，無法修改'
            ]);
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

        // 🔥 檢查是否為鎖定數量的商品
        if (isset($this->cart[$index]['locked_quantity']) && $this->cart[$index]['locked_quantity']) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '議價商品數量已鎖定，無法修改'
            ]);
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
            // 🔥 如果是議價商品，清除加入購物車標記
            if (isset($this->cart[$index]['bargain_id'])) {
                try {
                    $bargain = BargainHistory::find($this->cart[$index]['bargain_id']);
                    if ($bargain) {
                        $bargain->update(['added_to_cart_at' => null]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clear bargain cart status: ' . $e->getMessage());
                }
            }

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
        // 🔥 清除所有議價商品的購物車標記
        foreach ($this->cart as $item) {
            if (isset($item['bargain_id'])) {
                try {
                    $bargain = BargainHistory::find($item['bargain_id']);
                    if ($bargain) {
                        $bargain->update(['added_to_cart_at' => null]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clear bargain cart status: ' . $e->getMessage());
                }
            }
        }

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
        cookie()->queue('shopping_cart', json_encode($this->cart), 43200);
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
        return $this->subtotal;
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.cart-component');
    }
}
