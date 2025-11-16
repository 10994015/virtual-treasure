<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CheckoutComponent extends Component
{
    public $cart = [];
    public $cartCount = 0;

    // 買家資訊
    public $buyer_name = '';
    public $buyer_email = '';
    public $buyer_phone = '';
    public $buyer_game_id = ''; // 遊戲ID（用於遊戲內交易）

    // 付款方式
    public $payment_method = 'credit_card';

    // 備註
    public $order_note = '';

    // 同意條款
    public $agreed_terms = false;

    protected $rules = [
        'buyer_name' => 'required|min:2',
        'buyer_email' => 'required|email',
        'buyer_phone' => 'nullable|regex:/^09[0-9]{8}$/',
        'buyer_game_id' => 'required|min:3',
        'payment_method' => 'required|in:credit_card,atm,convenience_store,wallet',
        'order_note' => 'nullable|max:500',
        'agreed_terms' => 'accepted',
    ];

    protected $messages = [
        'buyer_name.required' => '請輸入您的姓名',
        'buyer_name.min' => '姓名至少需要2個字元',
        'buyer_email.required' => '請輸入電子郵件',
        'buyer_email.email' => '電子郵件格式不正確',
        'buyer_phone.regex' => '手機號碼格式不正確',
        'buyer_game_id.required' => '請輸入遊戲ID/角色名稱',
        'buyer_game_id.min' => '遊戲ID至少需要3個字元',
        'payment_method.required' => '請選擇付款方式',
        'order_note.max' => '訂單備註不可超過500字',
        'agreed_terms.accepted' => '請同意服務條款',
    ];

    public $paymentMethods = [
        'credit_card' => ['name' => '信用卡/金融卡', 'icon' => 'credit-card', 'desc' => '即時付款，立即完成交易'],
        'atm' => ['name' => 'ATM轉帳', 'icon' => 'university', 'desc' => '取得轉帳帳號後3天內完成轉帳'],
        'convenience_store' => ['name' => '超商代碼繳費', 'icon' => 'store', 'desc' => '取得繳費代碼後3天內完成繳費'],
        'wallet' => ['name' => '電子錢包', 'icon' => 'wallet', 'desc' => '使用平台錢包餘額支付'],
    ];

    public function mount()
    {
        $this->loadCartFromCookie();

        if (empty($this->cart)) {
            session()->flash('error', '購物車是空的');
            return redirect()->route('cart');
        }

        // 如果已登入，自動填入使用者資料
        if (auth()->check()) {
            $user = auth()->user();
            $this->buyer_name = $user->name;
            $this->buyer_email = $user->email;
            $this->buyer_phone = $user->phone ?? '';
        }

        $this->validateCart();
    }

    protected function loadCartFromCookie()
    {
        $cartCookie = request()->cookie('shopping_cart');
        if ($cartCookie) {
            $this->cart = json_decode($cartCookie, true) ?? [];
            $this->cartCount = count($this->cart);
        }
    }

    protected function validateCart()
    {
        $productIds = array_column($this->cart, 'id');
        $products = Product::with('user')
            ->whereIn('id', $productIds)
            ->where('is_published', true)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $updatedCart = [];
        $hasChanges = false;
        $removedProducts = [];

        foreach ($this->cart as $item) {
            $product = $products->get($item['id']);

            if (!$product) {
                $hasChanges = true;
                $removedProducts[] = $item['name'];
                continue;
            }

            if ($item['price'] != $product->price) {
                $item['price'] = $product->price;
                $hasChanges = true;
            }

            if ($product->stock > 0 && $item['quantity'] > $product->stock) {
                $item['quantity'] = $product->stock;
                $hasChanges = true;
            }

            $item['stock'] = $product->stock;
            $item['trade_type'] = $product->trade_type;
            $item['game_server'] = $product->game_server;
            $item['game_region'] = $product->game_region;
            $item['seller_id'] = $product->user_id;
            $updatedCart[] = $item;
        }

        if ($hasChanges) {
            $this->cart = $updatedCart;
            $this->cartCount = count($this->cart);
            $this->saveCartToCookie();

            if (!empty($removedProducts)) {
                session()->flash('warning', '部分商品已下架：' . implode('、', $removedProducts));
            } else {
                session()->flash('warning', '購物車已更新，部分商品價格或庫存有變動');
            }
        }

        if (empty($this->cart)) {
            return redirect()->route('cart');
        }
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

    public function placeOrder()
    {
        $this->validate();

        // 再次驗證購物車
        $this->validateCart();

        if (empty($this->cart)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '購物車是空的，無法結帳'
            ]);
            return redirect()->route('cart');
        }

        try {
            DB::beginTransaction();

            // 建立訂單
            $order = Order::create([
                'user_id' => auth()->id() ?? null,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'buyer_name' => $this->buyer_name,
                'buyer_email' => $this->buyer_email,
                'buyer_phone' => $this->buyer_phone,
                'buyer_game_id' => $this->buyer_game_id,
                'buyer_note' => $this->order_note,
            ]);

            // 建立訂單項目
            foreach ($this->cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $product->user_id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'product_image' => $item['image'],
                    'game_type' => $product->game_type,
                    'category' => $product->category,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'trade_type' => $product->trade_type,
                    'trade_instructions' => $product->trade_instructions,
                    'game_server' => $product->game_server,
                    'game_region' => $product->game_region,
                    'delivery_status' => 'pending',
                ]);

                // 扣除庫存
                if ($product->stock > 0) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // 清空購物車
            $this->cart = [];
            $this->cartCount = 0;
            cookie()->queue(cookie()->forget('shopping_cart'));

            session()->flash('success', '訂單已成立！訂單編號：' . $order->order_number);
            session()->flash('order_number', $order->order_number);

            return redirect()->route('checkout.success', ['order' => $order->order_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '訂單建立失敗：' . $e->getMessage()
            ]);
        }
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.checkout-component');
    }
}
