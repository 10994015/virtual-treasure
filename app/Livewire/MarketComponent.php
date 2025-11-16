<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MarketComponent extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $searchTerm = '';

    #[Url(as: 'category')]
    public $selectedCategories = [];

    #[Url(as: 'game')]
    public $selectedGames = [];

    #[Url(as: 'rarity')]
    public $selectedRarities = [];

    public $minPrice = '';
    public $maxPrice = '';

    #[Url(as: 'sort')]
    public $sortBy = 'newest';

    public $viewMode = 'grid';
    public $perPage = 12;

    // 購物車
    public $cart = [];
    public $cartCount = 0;

    // 選項數據
    public $categories = [
        '武器',
        '防具',
        '消耗品',
        '材料',
        '皮膚',
        '坐騎',
        '點數卡',
        '其他',
    ];

    public $games = [
        'World of Warcraft',
        'League of Legends',
        'Dota 2',
        'CS:GO',
        'Minecraft',
        'Genshin Impact',
        '其他',
    ];

    public $rarities = [
        'common' => '普通',
        'uncommon' => '優秀',
        'rare' => '精良',
        'epic' => '史詩',
        'legendary' => '傳說',
        'mythic' => '神話',
    ];

    public function mount()
    {
        $this->loadCartFromCookie();
    }

    protected function loadCartFromCookie()
    {
        $cartCookie = request()->cookie('shopping_cart');
        if ($cartCookie) {
            $this->cart = json_decode($cartCookie, true) ?? [];
            $this->cartCount = count($this->cart);
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatingSelectedGames()
    {
        $this->resetPage();
    }

    public function updatingSelectedRarities()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'searchTerm',
            'selectedCategories',
            'selectedGames',
            'selectedRarities',
            'minPrice',
            'maxPrice',
        ]);
        $this->resetPage();
    }

    public function setSort($sort)
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function addToCart($productId)
    {
        $product = Product::with('primaryImage')->find($productId);

        if (!$product || !$product->is_published || $product->status !== 'active') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '此商品無法加入購物車'
            ]);
            return;
        }

        // 檢查庫存
        if ($product->stock > 0) {
            $existingKey = array_search($productId, array_column($this->cart, 'id'));
            if ($existingKey !== false) {
                $currentQuantity = $this->cart[$existingKey]['quantity'];
                if ($currentQuantity >= $product->stock) {
                    $this->dispatch('notify', [
                        'type' => 'warning',
                        'message' => '已達該商品庫存上限'
                    ]);
                    return;
                }
            }
        }

        // 檢查是否已在購物車
        $existingKey = array_search($productId, array_column($this->cart, 'id'));

        if ($existingKey !== false) {
            // 增加數量
            $this->cart[$existingKey]['quantity']++;
        } else {
            // 新增到購物車
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
                'quantity' => 1,
                'stock' => $product->stock,
                'slug' => $product->slug,
            ];
        }

        $this->cartCount = count($this->cart);
        $this->saveCartToCookie();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '已加入購物車'
        ]);

        $this->dispatch('cart-updated', ['count' => $this->cartCount]);
    }


    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            $this->cartCount = count($this->cart);

            $this->saveCartToCookie();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已從購物車移除'
            ]);

            $this->dispatch('cart-updated', ['count' => $this->cartCount]);
        }
    }

    public function updateCartQuantity($index, $quantity)
    {
        if (isset($this->cart[$index])) {
            $quantity = max(1, min($quantity, $this->cart[$index]['stock']));
            $this->cart[$index]['quantity'] = $quantity;

            $this->saveCartToCookie();
            $this->dispatch('cart-updated', ['count' => $this->cartCount]);
        }
    }

    protected function saveCartToCookie()
    {
        cookie()->queue('shopping_cart', json_encode($this->cart), 43200); // 30 天
    }

    public function getProductsProperty()
    {
        $query = Product::with(['primaryImage', 'user'])
            ->where('is_published', true)
            ->where('status', 'active');

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if (!empty($this->selectedCategories)) {
            $query->whereIn('category', $this->selectedCategories);
        }

        if (!empty($this->selectedGames)) {
            $query->whereIn('game_type', $this->selectedGames);
        }

        if (!empty($this->selectedRarities)) {
            $query->whereIn('rarity', $this->selectedRarities);
        }

        if ($this->minPrice !== '' && $this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== '' && $this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        switch ($this->sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getTotalProductsCountProperty()
    {
        $query = Product::where('is_published', true)
            ->where('status', 'active');

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if (!empty($this->selectedCategories)) {
            $query->whereIn('category', $this->selectedCategories);
        }

        if (!empty($this->selectedGames)) {
            $query->whereIn('game_type', $this->selectedGames);
        }

        if (!empty($this->selectedRarities)) {
            $query->whereIn('rarity', $this->selectedRarities);
        }

        if ($this->minPrice !== '' && $this->minPrice !== null) {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== '' && $this->maxPrice !== null) {
            $query->where('price', '<=', $this->maxPrice);
        }

        return $query->count();
    }

    public function getCartTotalProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    // 在 MarketComponent.php 中添加/更新這些方法


    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.market-component', [
            'products' => $this->products,
            'totalCount' => $this->totalProductsCount,
        ]);
    }
}
