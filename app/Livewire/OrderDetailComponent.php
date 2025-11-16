<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class OrderDetailComponent extends Component
{
    public Order $order;

    // 訂單狀態選項
    public $statuses = [
        'pending' => '待付款',
        'paid' => '已付款',
        'processing' => '處理中',
        'delivering' => '交付中',
        'completed' => '已完成',
        'cancelled' => '已取消',
        'refunding' => '退款中',
        'refunded' => '已退款',
        'dispute' => '爭議中',
    ];

    // 付款方式
    public $paymentMethods = [
        'credit_card' => '信用卡/金融卡',
        'atm' => 'ATM轉帳',
        'convenience_store' => '超商繳費',
        'wallet' => '電子錢包',
    ];

    public function mount($order)
    {
        $this->order = Order::where('id', $order->id)
            ->where('user_id', auth()->id())
            ->with(['items.product', 'items.seller'])
            ->firstOrFail();
    }

    public function cancelOrder()
    {
        if (!in_array($this->order->status, ['pending', 'paid'])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '此訂單無法取消'
            ]);
            return;
        }

        $this->order->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        // 恢復庫存
        foreach ($this->order->items as $item) {
            $product = $item->product;
            if ($product && $product->stock > 0) {
                $product->increment('stock', $item->quantity);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '訂單已取消'
        ]);

        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.order-detail-component');
    }
}
