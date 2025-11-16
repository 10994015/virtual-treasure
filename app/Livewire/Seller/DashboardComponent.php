<?php

namespace App\Livewire\Seller;

use App\Services\SellerDashboardService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;

class DashboardComponent extends Component
{
    public $period = '7days';
    public $adminMode = false;
    public $stats = [];
    public $topProducts = [];
    public $topSellers = [];
    public $recentOrders = [];
    public $recentActivities = [];
    public $salesTrend = [];

    protected $dashboardService;

    public function boot(SellerDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        if (!Gate::allows('seller')) {
            abort(403, '您沒有權限訪問此頁面');
        }

        if (auth()->user()->is_admin) {
            $this->adminMode = true;
        }

        $this->loadDashboardData();
    }

    public function updatedPeriod()
    {
        $this->loadDashboardData();
    }

    public function toggleAdminMode()
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限使用管理員模式'
            ]);
            return;
        }

        $this->adminMode = !$this->adminMode;
        $this->loadDashboardData();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->adminMode ? '已切換至平台總覽模式' : '已切換至個人賣家模式'
        ]);
    }

    protected function loadDashboardData()
    {
        $sellerId = $this->adminMode ? null : auth()->id();

        $this->stats = $this->dashboardService->getStats($sellerId, $this->period, $this->adminMode);
        $this->topProducts = $this->dashboardService->getTopProducts($sellerId, 5, $this->adminMode);
        
        if ($this->adminMode) {
            $this->topSellers = $this->dashboardService->getTopSellers(5);
        }
        
        $this->recentOrders = $this->dashboardService->getRecentOrders($sellerId, 5, $this->adminMode);
        $this->recentActivities = $this->dashboardService->getRecentActivities($sellerId, 10, $this->adminMode);
        $this->salesTrend = $this->dashboardService->getSalesTrend($sellerId, $this->period, $this->adminMode);
    }

    public function clearNotifications()
    {
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '已清除所有通知'
        ]);
    }

    /**
     * 取得訂單狀態對應的 CSS class
     */
    public function getStatusClass($status)
    {
        return match($status) {
            'pending' => 'pending',
            'paid', 'processing', 'delivering' => 'processing',
            'completed' => 'completed',
            'cancelled', 'refunded' => 'cancelled',
            default => 'pending',
        };
    }

    /**
     * 取得訂單狀態的中文名稱
     */
    public function getStatusText($status)
    {
        return match($status) {
            'pending' => '待付款',
            'paid' => '已付款',
            'processing' => '處理中',
            'delivering' => '交付中',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'refunded' => '已退款',
            default => $status,
        };
    }

    #[Title('銷售儀表板')]
    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.seller.dashboard-component');
    }
}