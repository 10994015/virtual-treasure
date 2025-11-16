<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $searchTerm = '';

    #[Url(as: 'category')]
    public $categoryFilter = '';

    #[Url(as: 'game')]
    public $gameFilter = '';

    #[Url(as: 'status')]
    public $statusFilter = '';

    public $showAllProducts = false;

    // 類別選項
    public $categories = [
        '武器' => '武器',
        '防具' => '防具',
        '消耗品' => '消耗品',
        '材料' => '材料',
        '皮膚' => '皮膚',
        '坐騎' => '坐騎',
    ];

    public $rarities = [
        'common' => '普通',
        'uncommon' => '優秀',
        'rare' => '精良',
        'epic' => '史詩',
        'legendary' => '傳說',
        'mythic' => '神話',
    ];

    // 遊戲選項
    public $games = [
        'World of Warcraft' => 'World of Warcraft',
        'League of Legends' => 'League of Legends',
        'Dota 2' => 'Dota 2',
        'CS:GO' => 'CS:GO',
        '原神' => '原神',
    ];

    // 狀態選項
    public $statuses = [
        'draft' => '草稿',
        'active' => '上架中',
        'inactive' => '已下架',
        'sold_out' => '已售完',
        'suspended' => '已暫停',
    ];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'gameFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingGameFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleViewAllProducts()
    {
        $this->showAllProducts = !$this->showAllProducts;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'categoryFilter', 'gameFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function toggleProductStatus($productId)
    {
        $product = Product::findOrFail($productId);

        // 權限檢查
        if (!auth()->user()->is_admin && $product->user_id !== auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        if ($product->status === 'active') {
            $product->update([
                'status' => 'inactive',
                'is_published' => false,
            ]);
            $message = '商品已下架';
        } else {
            $product->update([
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
            ]);
            $message = '商品已上架';
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);

        // 權限檢查
        if (!auth()->user()->is_admin && $product->user_id !== auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '您沒有權限執行此操作'
            ]);
            return;
        }

        $product->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '商品已刪除'
        ]);
    }

    public function getProductsProperty()
    {
        $query = Product::with(['primaryImage', 'user']);

        // 如果不是管理員或未開啟查看所有商品，只顯示自己的
        if (!auth()->user()->is_admin || !$this->showAllProducts) {
            $query->where('user_id', auth()->id());
        }

        // 搜尋
        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        // 類別篩選
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // 遊戲篩選
        if ($this->gameFilter) {
            $query->where('game_type', $this->gameFilter);
        }

        // 狀態篩選
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(12);
    }

    public function getTotalProductsCountProperty()
    {
        $query = Product::query();

        if (!auth()->user()->is_admin || !$this->showAllProducts) {
            $query->where('user_id', auth()->id());
        }

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->gameFilter) {
            $query->where('game_type', $this->gameFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->count();
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.product-management', [
            'products' => $this->products,
            'totalCount' => $this->totalProductsCount,
        ]);
    }
}
