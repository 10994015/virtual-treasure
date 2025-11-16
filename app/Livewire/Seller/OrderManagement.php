<?php

namespace App\Livewire\Seller;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManagement extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $searchTerm = '';

    #[Url(as: 'status')]
    public $statusFilter = '';

    #[Url(as: 'payment')]
    public $paymentStatusFilter = '';

    public $showAllOrders = false;

    public $perPage = 15;

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

    // 付款狀態選項
    public $paymentStatuses = [
        'pending' => '待付款',
        'paid' => '已付款',
        'failed' => '付款失敗',
        'refunded' => '已退款',
    ];

    // 付款方式選項
    public $paymentMethods = [
        'credit_card' => '信用卡',
        'atm' => 'ATM轉帳',
        'convenience_store' => '超商繳費',
        'wallet' => '電子錢包',
    ];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleViewAllOrders()
    {
        $this->showAllOrders = !$this->showAllOrders;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'statusFilter', 'paymentStatusFilter']);
        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['user', 'items.product', 'items.seller']);

        // 權限檢查：如果不是管理員或未開啟查看所有訂單，只顯示與自己相關的訂單
        if (!auth()->user()->is_admin || !$this->showAllOrders) {
            // 顯示作為買家的訂單 或 作為賣家的訂單（透過 order_items）
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('items', function($itemQuery) {
                      $itemQuery->where('seller_id', auth()->id());
                  });
            });
        }

        // 搜尋（訂單編號、買家姓名、買家信箱）
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('buyer_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('buyer_email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // 訂單狀態篩選
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // 付款狀態篩選
        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getTotalOrdersCountProperty()
    {
        $query = Order::query();

        if (!auth()->user()->is_admin || !$this->showAllOrders) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('items', function($itemQuery) {
                      $itemQuery->where('seller_id', auth()->id());
                  });
            });
        }

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('buyer_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('buyer_email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }

        return $query->count();
    }

    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        // 權限檢查
        if (!auth()->user()->is_admin && $order->user_id !== auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        // 檢查是否可以取消
        if (!in_array($order->status, ['pending', 'paid'])) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '此訂單無法取消'
            ]);
            return;
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        // 恢復庫存
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->stock > 0) {
                $product->increment('stock', $item->quantity);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '訂單已取消'
        ]);
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.order-management', [
            'orders' => $this->orders,
            'totalCount' => $this->totalOrdersCount,
        ]);
    }
}
