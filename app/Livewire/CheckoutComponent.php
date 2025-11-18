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

            if ($product->stock === 0) {
                $hasChanges = true;
                $removedProducts[] = $item['name'] . '（已售完）';
                continue;
            }

            // 🔥 關鍵修改：議價商品不更新價格
            $isBargainItem = isset($item['is_bargain']) && $item['is_bargain'] === true;

            // 🔥 只有「一般商品」才更新價格
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

            // 🔥 先檢查所有商品是否有足夠的序號
            foreach ($this->cart as $item) {
                $product = Product::with('availableCodes')->find($item['id']);

                if (!$product) {
                    throw new \Exception("商品 {$item['name']} 不存在");
                }

                // 🔥 檢查是否有足夠的可用序號（庫存 > 0 且有實體序號的商品）
                if ($product->stock > 0) {
                    $availableCodesCount = $product->availableCodes()->count();

                    if ($availableCodesCount < $item['quantity']) {
                        throw new \Exception("商品「{$product->name}」的可用序號不足（需要 {$item['quantity']} 個，剩餘 {$availableCodesCount} 個）");
                    }
                }
            }

            // 🔥 建立訂單 - 直接設置為已付款和已完成
            $now = now();

            $order = Order::create([
                'user_id' => auth()->id() ?? null,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'payment_status' => 'paid', // 🔥 直接標記為已付款
                'status' => 'completed', // 🔥 直接標記為已完成
                'buyer_name' => $this->buyer_name,
                'buyer_email' => $this->buyer_email,
                'buyer_phone' => $this->buyer_phone,
                'buyer_game_id' => $this->buyer_game_id,
                'buyer_note' => $this->order_note,
                // 🔥 設置所有時間戳
                'paid_at' => $now, // 付款時間
                'completed_at' => $now, // 完成時間
            ]);

            // 建立訂單項目並分配序號
            foreach ($this->cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    continue;
                }

                // 🔥 檢查是否為議價商品
                $isBargainItem = isset($item['is_bargain']) && $item['is_bargain'];
                $bargainId = $isBargainItem && isset($item['bargain_id']) ? $item['bargain_id'] : null;
                $conversationId = isset($item['conversation_id']) ? $item['conversation_id'] : null;

                // 🔥 判斷是否有虛寶序號（自動交付）
                $hasProductCodes = $product->stock > 0 && $product->availableCodes()->exists();

                // 建立訂單項目
                $orderItem = OrderItem::create([
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
                    'trade_type' => $product->trade_type ?? 'in_game',
                    'trade_instructions' => $product->trade_instructions,
                    'game_server' => $product->game_server,
                    'game_region' => $product->game_region,
                    'delivery_status' => 'delivered', // 🔥 直接標記為已交付
                    'delivered_at' => $now, // 🔥 設置交付時間
                    'is_bargain' => $isBargainItem,
                    'bargain_id' => $bargainId,
                    'conversation_id' => $conversationId,
                ]);

                // 🔥 分配虛寶序號
                if ($product->stock > 0) {
                    $codes = $product->availableCodes()
                        ->take($item['quantity'])
                        ->get();

                    foreach ($codes as $code) {
                        $code->markAsSold($order->id, auth()->id());

                        \Illuminate\Support\Facades\Log::info('虛寶序號已分配', [
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                            'code_id' => $code->id,
                            'product_name' => $product->name,
                        ]);
                    }
                }

                // 🔥 扣除庫存
                if ($product->stock > 0) {
                    $product->decrement('stock', $item['quantity']);
                }

                // 🔥 如果是議價商品，更新議價狀態
                if ($bargainId) {
                    try {
                        $bargain = \App\Models\BargainHistory::find($bargainId);
                        if ($bargain) {
                            $bargain->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to update bargain status: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            // 清空購物車
            $this->cart = [];
            $this->cartCount = 0;
            cookie()->queue(cookie()->forget('shopping_cart'));

            session()->flash('success', '訂單已成立並完成！訂單編號：' . $order->order_number);
            session()->flash('order_number', $order->order_number);

            return redirect()->route('checkout.success', ['order' => $order->order_number]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('訂單建立失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

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
