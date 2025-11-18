<?php

namespace App\Livewire;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HomeComponent extends Component
{
    public $topSellers = [];
    public $topProducts = [];

    public function mount()
    {
        // 這裡可以加入初始化邏輯，例如載入熱門商品
        $query = OrderItem::whereHas('order', function($q) {
                $q->whereIn('status', ['paid', 'processing', 'delivering', 'completed']);
            });
            //加入products.slug
        $this->topProducts = $query
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_image',
                'order_items.seller_id',
                'products.slug',
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_image',
                'order_items.seller_id',
                'products.slug'
            )
            ->orderBy('total_sales', 'desc')
            ->limit(4)
            ->get();
    }
    public function render()
    {
        return view('livewire.home-component');
    }
}
