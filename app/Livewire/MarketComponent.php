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
         // 🔥 檢查庫存（庫存 0 = 已售完）
        if ($product->stock === 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '商品已售完'
            ]);
            return;
        }
        // 🔥 修改：只檢查「一般商品」（不含議價商品）
        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            // 只有當商品 ID 相同「且」不是議價商品時，才視為相同商品
            if ($item['id'] == $productId && !isset($item['bargain_id'])) {
                $existingIndex = $index;
                break;
            }
        }

        // 檢查庫存
        if ($product->stock > 0 && $existingIndex !== null) {
            $currentQuantity = $this->cart[$existingIndex]['quantity'];
            if ($currentQuantity >= $product->stock) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => '已達該商品庫存上限'
                ]);
                return;
            }
        }

        if ($existingIndex !== null) {
            // 🔥 增加「一般商品」的數量
            $newQuantity = $this->cart[$existingIndex]['quantity'] + 1;

            // 再次檢查庫存
            if ($product->stock > 0 && $newQuantity > $product->stock) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => '超過庫存數量'
                ]);
                return;
            }

            $this->cart[$existingIndex]['quantity'] = $newQuantity;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已更新購物車數量'
            ]);
        } else {
            // 🔥 新增「一般商品」到購物車
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
                'quantity' => 1,
                'stock' => $product->stock,
                'slug' => $product->slug,
                // 🔥 關鍵：一般商品不標記 is_bargain 和 bargain_id
            ];

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已加入購物車'
            ]);
        }

        $this->cartCount = count($this->cart);
        $this->saveCartToCookie();
        $this->dispatch('cart-updated', ['count' => $this->cartCount]);
    }



    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            // 🔥 如果是議價商品，清除加入購物車標記
            if (isset($this->cart[$index]['bargain_id'])) {
                try {
                    $bargain = \App\Models\BargainHistory::find($this->cart[$index]['bargain_id']);
                    if ($bargain) {
                        $bargain->update(['added_to_cart_at' => null]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to clear bargain cart status: ' . $e->getMessage());
                }
            }

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
            // 🔥 檢查是否為議價商品（議價商品數量鎖定）
            if (isset($this->cart[$index]['locked_quantity']) && $this->cart[$index]['locked_quantity']) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => '議價商品數量已鎖定，無法修改'
                ]);
                return;
            }

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
