<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrdersComponent extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $searchTerm = '';

    #[Url(as: 'status')]
    public $statusFilter = '';

    public $perPage = 10;

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

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'statusFilter']);
        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['items.product', 'items.seller'])
            ->where('user_id', auth()->id());

        // 搜尋（訂單編號、商品名稱）
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('items', function($itemQuery) {
                      $itemQuery->where('product_name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        // 訂單狀態篩選
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getTotalOrdersCountProperty()
    {
        return Order::where('user_id', auth()->id())->count();
    }

    public function getStatusCountsProperty()
    {
        $userId = auth()->id();

        return [
            'all' => Order::where('user_id', $userId)->count(),
            'pending' => Order::where('user_id', $userId)->where('status', 'pending')->count(),
            'paid' => Order::where('user_id', $userId)->where('status', 'paid')->count(),
            'processing' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
            'delivering' => Order::where('user_id', $userId)->where('status', 'delivering')->count(),
            'completed' => Order::where('user_id', $userId)->where('status', 'completed')->count(),
        ];
    }

    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        // 權限檢查
        if ($order->user_id !== auth()->id()) {
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

    public function render()
    {
        return view('livewire.my-orders-component', [
            'orders' => $this->orders,
            'totalCount' => $this->totalOrdersCount,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
