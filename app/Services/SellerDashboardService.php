<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerDashboardService
{
    /**
     * 取得賣家統計數據 (支援管理員模式)
     */
    public function getStats(?int $sellerId, string $period = '7days', bool $isAdminMode = false): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'total_sales' => $this->getTotalSales($sellerId, $dateRange, $isAdminMode),
            'total_orders' => $this->getTotalOrders($sellerId, $dateRange, $isAdminMode),
            'active_products' => $this->getActiveProducts($sellerId, $isAdminMode),
            'average_rating' => $this->getAverageRating($sellerId, $isAdminMode),
            'total_users' => $isAdminMode ? $this->getTotalUsers() : null,
            'total_sellers' => $isAdminMode ? $this->getTotalSellers() : null,
            'pending_orders' => $isAdminMode ? $this->getPendingOrders() : null,
            'comparison' => $this->getComparison($sellerId, $period, $isAdminMode),
        ];
    }
    
    /**
     * 取得總銷售額
     */
    protected function getTotalSales(?int $sellerId, array $dateRange, bool $isAdminMode = false): float
    {
        $query = OrderItem::query();
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        return $query->whereHas('order', function($q) use ($dateRange) {
                $q->whereIn('status', ['paid', 'processing', 'delivering', 'completed'])
                  ->whereBetween('created_at', $dateRange);
            })
            ->sum('subtotal');
    }
    
    /**
     * 取得總訂單數
     */
    protected function getTotalOrders(?int $sellerId, array $dateRange, bool $isAdminMode = false): int
    {
        $query = OrderItem::query();
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        return $query->whereHas('order', function($q) use ($dateRange) {
                $q->whereIn('status', ['paid', 'processing', 'delivering', 'completed'])
                  ->whereBetween('created_at', $dateRange);
            })
            ->distinct('order_id')
            ->count('order_id');
    }
    
    /**
     * 取得在售商品數
     */
    protected function getActiveProducts(?int $sellerId, bool $isAdminMode = false): int
    {
        $query = Product::where('is_published', true)
            ->where('status', 'active');
        
        if (!$isAdminMode && $sellerId) {
            $query->where('user_id', $sellerId);
        }
        
        return $query->count();
    }
    
    /**
     * 取得平均評價
     */
    protected function getAverageRating(?int $sellerId, bool $isAdminMode = false): array
    {
        $query = OrderItem::whereNotNull('rating');
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        $ratings = $query->select(
                DB::raw('AVG(rating) as average'),
                DB::raw('COUNT(rating) as total')
            )
            ->first();
        
        return [
            'average' => round($ratings->average ?? 0, 1),
            'total' => $ratings->total ?? 0,
        ];
    }
    
    /**
     * 取得用戶總數 (僅管理員)
     */
    protected function getTotalUsers(): int
    {
        return User::count();
    }
    
    /**
     * 取得賣家總數 (僅管理員)
     */
    protected function getTotalSellers(): int
    {
        return Product::distinct('user_id')->count('user_id');
    }
    
    /**
     * 取得待處理訂單 (僅管理員)
     */
    protected function getPendingOrders(): int
    {
        return Order::whereIn('status', ['pending', 'paid'])->count();
    }
    
    /**
     * 取得與上期比較
     */
    protected function getComparison(?int $sellerId, string $period, bool $isAdminMode = false): array
    {
        $currentRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);
        
        $currentSales = $this->getTotalSales($sellerId, $currentRange, $isAdminMode);
        $previousSales = $this->getTotalSales($sellerId, $previousRange, $isAdminMode);
        
        $currentOrders = $this->getTotalOrders($sellerId, $currentRange, $isAdminMode);
        $previousOrders = $this->getTotalOrders($sellerId, $previousRange, $isAdminMode);
        
        return [
            'sales_change' => $this->calculatePercentageChange($currentSales, $previousSales),
            'sales_diff' => $currentSales - $previousSales,
            'orders_change' => $this->calculatePercentageChange($currentOrders, $previousOrders),
            'orders_diff' => $currentOrders - $previousOrders,
        ];
    }
    
    /**
     * 取得銷售趨勢數據
     */
    public function getSalesTrend(?int $sellerId, string $period = '7days', bool $isAdminMode = false): array
    {
        $dateRange = $this->getDateRange($period);
        $groupBy = $this->getGroupByFormat($period);
        
        $query = OrderItem::query();
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        $sales = $query->whereHas('order', function($q) use ($dateRange) {
                $q->whereIn('status', ['paid', 'processing', 'delivering', 'completed'])
                  ->whereBetween('created_at', $dateRange);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw("DATE_FORMAT(orders.created_at, '{$groupBy}') as date")
            ->selectRaw('SUM(order_items.subtotal) as total')
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return $sales->toArray();
    }
    
    /**
     * 取得熱門商品
     */
    public function getTopProducts(?int $sellerId, int $limit = 5, bool $isAdminMode = false): array
    {
        $query = OrderItem::whereHas('order', function($q) {
                $q->whereIn('status', ['paid', 'processing', 'delivering', 'completed']);
            });
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        $result = $query->select(
                'product_id',
                'product_name',
                'product_image',
                'seller_id',
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('product_id', 'product_name', 'product_image', 'seller_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();
        
        // 如果是管理員模式,加入賣家資訊
        if ($isAdminMode) {
            $result->load('seller:id,last_name,first_name');
        }
        
        return $result->toArray();
    }
    
    /**
     * 取得頂級賣家 (僅管理員)
     */
    public function getTopSellers(int $limit = 5): array
    {
        return OrderItem::whereHas('order', function($query) {
                $query->whereIn('status', ['paid', 'processing', 'delivering', 'completed']);
            })
            ->select(
                'seller_id',
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                DB::raw('AVG(CASE WHEN rating IS NOT NULL THEN rating END) as avg_rating')
            )
            ->with('seller:id,last_name,first_name,email')
            ->groupBy('seller_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'seller_id' => $item->seller_id,
                    'seller_name' => ($item->seller->last_name . $item->seller->first_name) ?? '-',
                    'seller_email' => $item->seller->email ?? '',
                    'total_sales' => $item->total_sales,
                    'total_orders' => $item->total_orders,
                    'avg_rating' => round($item->avg_rating ?? 0, 1),
                ];
            })
            ->toArray();
    }
    
    /**
     * 取得最新訂單
     */
    public function getRecentOrders(?int $sellerId, int $limit = 10, bool $isAdminMode = false): array
    {
        $query = OrderItem::query();
        
        if (!$isAdminMode && $sellerId) {
            $query->where('seller_id', $sellerId);
        }
        
        return $query->with(['order', 'order.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) use ($isAdminMode) {
                $data = [
                    'order_number' => $item->order->order_number,
                    'product_name' => $item->product_name,
                    'buyer_name' => $item->order->buyer_name,
                    'price' => $item->subtotal,
                    'status' => $item->order->status,
                    'created_at' => $item->created_at,
                ];
                
                // 管理員模式顯示賣家名稱
                if ($isAdminMode && $item->seller) {
                    $data['seller_name'] = $item->seller->name;
                }
                
                return $data;
            })
            ->toArray();
    }
    
    /**
     * 取得最新活動
     */
    public function getRecentActivities(?int $sellerId, int $limit = 10, bool $isAdminMode = false): array
    {
        $activities = [];
        
        // 新訂單
        $orderQuery = OrderItem::with('order');
        
        if (!$isAdminMode && $sellerId) {
            $orderQuery->where('seller_id', $sellerId);
        }
        
        $newOrders = $orderQuery->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        foreach ($newOrders as $item) {
            $description = "訂單 #{$item->order->order_number} 已建立，等待處理";
            if ($isAdminMode && $item->seller) {
                $description .= " (賣家: {$item->seller->name})";
            }
            
            $activities[] = [
                'type' => 'new_order',
                'icon' => 'shopping-cart',
                'color' => 'green',
                'title' => '新訂單成立',
                'description' => $description,
                'time' => $item->created_at,
            ];
        }
        
        // 新評價
        $reviewQuery = OrderItem::whereNotNull('rating')->whereNotNull('reviewed_at');
        
        if (!$isAdminMode && $sellerId) {
            $reviewQuery->where('seller_id', $sellerId);
        }
        
        $newReviews = $reviewQuery->where('reviewed_at', '>=', now()->subDays(7))
            ->orderBy('reviewed_at', 'desc')
            ->limit($limit)
            ->get();
        
        foreach ($newReviews as $item) {
            $activities[] = [
                'type' => 'new_review',
                'icon' => 'star',
                'color' => 'blue',
                'title' => '收到新評價',
                'description' => "「{$item->product_name}」獲得{$item->rating}星評價",
                'time' => $item->reviewed_at,
            ];
        }
        
        // 庫存警告
        $stockQuery = Product::where('is_published', true)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5);
        
        if (!$isAdminMode && $sellerId) {
            $stockQuery->where('user_id', $sellerId);
        }
        
        $lowStockProducts = $stockQuery->get();
        
        foreach ($lowStockProducts as $product) {
            $activities[] = [
                'type' => 'low_stock',
                'icon' => 'exclamation-triangle',
                'color' => 'yellow',
                'title' => '庫存警告',
                'description' => "「{$product->name}」剩餘數量不足",
                'time' => now()->subHours(rand(1, 48)),
            ];
        }
        
        // 管理員模式額外顯示新用戶
        if ($isAdminMode) {
            $newUsers = User::where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
            
            foreach ($newUsers as $user) {
                $activities[] = [
                    'type' => 'new_user',
                    'icon' => 'user-plus',
                    'color' => 'purple',
                    'title' => '新用戶註冊',
                    'description' => $user->name,
                    'time' => $user->created_at,
                ];
            }
        }
        
        // 依時間排序
        usort($activities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        return array_slice($activities, 0, $limit);
    }
    
    /**
     * 計算百分比變化
     */
    protected function calculatePercentageChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }
    
    /**
     * 取得日期範圍
     */
    protected function getDateRange(string $period): array
    {
        switch ($period) {
            case '7days':
                return [now()->subDays(7), now()];
            case '30days':
                return [now()->subDays(30), now()];
            case '90days':
                return [now()->subDays(90), now()];
            case '1year':
                return [now()->subYear(), now()];
            default:
                return [now()->subDays(7), now()];
        }
    }
    
    /**
     * 取得上一期日期範圍
     */
    protected function getPreviousDateRange(string $period): array
    {
        switch ($period) {
            case '7days':
                return [now()->subDays(14), now()->subDays(7)];
            case '30days':
                return [now()->subDays(60), now()->subDays(30)];
            case '90days':
                return [now()->subDays(180), now()->subDays(90)];
            case '1year':
                return [now()->subYears(2), now()->subYear()];
            default:
                return [now()->subDays(14), now()->subDays(7)];
        }
    }
    
    /**
     * 取得分組格式
     */
    protected function getGroupByFormat(string $period): string
    {
        switch ($period) {
            case '7days':
                return '%Y-%m-%d';
            case '30days':
                return '%Y-%m-%d';
            case '90days':
                return '%Y-%u';
            case '1year':
                return '%Y-%m';
            default:
                return '%Y-%m-%d';
        }
    }
}