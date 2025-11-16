<?php

namespace App\Livewire\Seller;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class OrderDetailManagement extends Component
{
    public Order $order;
    public $adminNote = '';
    public $deliveryCode = '';
    public $deliveryInfo = '';
    public $selectedItemId = null;

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

    public function mount(Order $order)
    {
        // 權限檢查
        if (!auth()->user()->is_admin &&
            $order->user_id !== auth()->id() &&
            !$order->items->contains('seller_id', auth()->id())) {
            abort(403, '您沒有權限查看此訂單');
        }

        $this->order = $order->load(['user', 'items.product', 'items.seller', 'cancelledBy']);
        $this->adminNote = $order->admin_note ?? '';
    }

    public function updateAdminNote()
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        $this->order->update([
            'admin_note' => $this->adminNote,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '備註已更新'
        ]);
    }

    public function markAsPaid()
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        if ($this->order->payment_status === 'paid') {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '訂單已經標記為已付款'
            ]);
            return;
        }

        $this->order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '訂單已標記為已付款'
        ]);

        $this->order->refresh();
    }

    public function deliverItem($itemId)
    {
        $item = OrderItem::findOrFail($itemId);

        // 權限檢查：只有賣家或管理員可以交付
        if (!auth()->user()->is_admin && $item->seller_id !== auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        if (empty($this->deliveryCode)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '請輸入交付代碼/序號'
            ]);
            return;
        }

        $item->update([
            'delivery_status' => 'delivered',
            'delivery_code' => $this->deliveryCode,
            'delivery_info' => $this->deliveryInfo ? json_encode(['note' => $this->deliveryInfo]) : null,
            'delivered_at' => now(),
        ]);

        // 檢查所有商品是否都已交付
        $allDelivered = $this->order->items()->where('delivery_status', '!=', 'delivered')->count() === 0;

        if ($allDelivered) {
            $this->order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } else {
            $this->order->update([
                'status' => 'delivering',
            ]);
        }

        $this->deliveryCode = '';
        $this->deliveryInfo = '';
        $this->selectedItemId = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '商品已標記為已交付'
        ]);

        $this->order->refresh();
    }

    public function cancelOrder()
    {
        // 權限檢查
        if (!auth()->user()->is_admin && $this->order->user_id !== auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

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

    public function completeOrder()
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        $this->order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '訂單已完成'
        ]);

        $this->order->refresh();
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.order-detail-management');
    }
}
