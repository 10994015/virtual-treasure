<div>
    <!-- Market Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    虛擬寶物
                    <span class="text-blue-600">市場</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-8 leading-relaxed">
                    探索數千種遊戲虛擬物品，找到您心儀的寶物
                </p>
            </div>
        </div>
    </section>

    <!-- Market Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">篩選條件</h3>
                            <button
                                wire:click="clearFilters"
                                class="text-sm text-blue-600 hover:text-blue-700">
                                清除全部
                            </button>
                        </div>

                        <div class="p-4 space-y-6">
                            <!-- Category Filter -->
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-tags text-gray-500"></i>
                                    <h4 class="font-medium text-gray-900">商品類別</h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach($categories as $category)
                                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <input
                                                type="checkbox"
                                                value="{{ $category }}"
                                                wire:model.live="selectedCategories"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $category }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Game Filter -->
                            <div class="border-t pt-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-gamepad text-gray-500"></i>
                                    <h4 class="font-medium text-gray-900">遊戲類型</h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach($games as $game)
                                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <input
                                                type="checkbox"
                                                value="{{ $game }}"
                                                wire:model.live="selectedGames"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $game }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="border-t pt-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-dollar-sign text-gray-500"></i>
                                    <h4 class="font-medium text-gray-900">價格範圍</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        wire:model.live.debounce.500ms="minPrice"
                                        placeholder="最低"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                    <span class="text-gray-500">-</span>
                                    <input
                                        type="number"
                                        wire:model.live.debounce.500ms="maxPrice"
                                        placeholder="最高"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                </div>
                            </div>

                            <!-- Rarity Filter -->
                            <div class="border-t pt-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="fas fa-star text-gray-500"></i>
                                    <h4 class="font-medium text-gray-900">稀有度</h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach($rarities as $value => $label)
                                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <input
                                                type="checkbox"
                                                value="{{ $value }}"
                                                wire:model.live="selectedRarities"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Search and Controls -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                            <!-- Search Box -->
                            <div class="w-full md:w-96">
                                <div class="relative">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="searchTerm"
                                        placeholder="搜尋商品..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Sort Controls -->
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-gray-600">排序：</span>
                                <select
                                    wire:model.live="sortBy"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                    <option value="newest">最新上架</option>
                                    <option value="price-low">價格從低到高</option>
                                    <option value="price-high">價格從高到低</option>
                                    <option value="popular">最熱門</option>
                                </select>

                                <!-- View Toggle -->
                                <div class="flex border border-gray-300 rounded-lg">
                                    <button
                                        wire:click="setViewMode('grid')"
                                        class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-l-lg transition-colors">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button
                                        wire:click="setViewMode('list')"
                                        class="px-3 py-2 {{ $viewMode === 'list' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-r-lg transition-colors border-l">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Count -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            找到 <span class="font-semibold text-gray-900">{{ $totalCount }}</span> 件商品
                        </p>
                    </div>

                    <!-- Items Grid/List -->
                    @if($products->count() > 0)
                        <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-4' }}">
                            @foreach($products as $product)
                                @if($viewMode === 'grid')
                                    <!-- Grid View -->
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                        <!-- Product Image -->
                                        <div class="relative bg-gray-100 rounded-t-lg h-48 overflow-hidden">
                                            @if($product->primaryImage)
                                                <img
                                                    src="{{ $product->primaryImage->image_url }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                 <img
                                                    src="{{ asset('images/no-image.png') }}"
                                                    class="w-full h-full object-cover">
                                            @endif

                                            <!-- Rarity Badge -->
                                            <div class="absolute top-2 left-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded {{
                                                    $product->rarity === 'legendary' ? 'bg-yellow-500 text-white' :
                                                    ($product->rarity === 'epic' ? 'bg-purple-500 text-white' :
                                                    ($product->rarity === 'rare' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'))
                                                }}">
                                                    {{ $rarities[$product->rarity] ?? $product->rarity }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="p-4">
                                            <h3 class="font-semibold text-gray-900 mb-2 truncate">
                                                {{ $product->name }}
                                            </h3>

                                            <div class="space-y-1 mb-4">
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-gamepad mr-1"></i>
                                                    {{ $product->game_type }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $product->category }}
                                                </p>
                                            </div>

                                            <div class="flex items-center justify-between mb-4">
                                                <span class="text-2xl font-bold text-blue-600">
                                                    NT$ {{ number_format($product->price) }}
                                                </span>
                                                @if($product->original_price && $product->original_price > $product->price)
                                                    <span class="text-sm text-gray-400 line-through">
                                                        NT$ {{ number_format($product->original_price) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex gap-2">
                                                <a
                                                    href="{{ route('products.show', $product->slug) }}"
                                                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                                    查看詳情
                                                </a>
                                                <button
                                                    wire:click="addToCart({{ $product->id }})"
                                                    class="flex-1 px-4 py-2 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                                    <i class="fas fa-cart-plus mr-1"></i>
                                                    加入購物車
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- List View -->
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                        <div class="p-4 flex gap-4">
                                            <!-- Product Image -->
                                            <div class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                @if($product->primaryImage)
                                                    <img
                                                        src="{{ $product->primaryImage->image_url }}"
                                                        alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h3 class="font-semibold text-lg text-gray-900">
                                                        {{ $product->name }}
                                                    </h3>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded {{
                                                        $product->rarity === 'legendary' ? 'bg-yellow-500 text-white' :
                                                        ($product->rarity === 'epic' ? 'bg-purple-500 text-white' :
                                                        ($product->rarity === 'rare' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'))
                                                    }}">
                                                        {{ $rarities[$product->rarity] ?? $product->rarity }}
                                                    </span>
                                                </div>

                                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                    {{ Str::limit($product->description, 150) }}
                                                </p>

                                                <div class="flex items-center gap-4 mb-3">
                                                    <span class="text-sm text-gray-600">
                                                        <i class="fas fa-gamepad mr-1"></i>
                                                        {{ $product->game_type }}
                                                    </span>
                                                    <span class="text-sm text-gray-600">
                                                        <i class="fas fa-tag mr-1"></i>
                                                        {{ $product->category }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="text-2xl font-bold text-blue-600">
                                                            NT$ {{ number_format($product->price) }}
                                                        </span>
                                                        @if($product->original_price && $product->original_price > $product->price)
                                                            <span class="text-sm text-gray-400 line-through ml-2">
                                                                NT$ {{ number_format($product->original_price) }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex gap-2">
                                                        <a
                                                            href="{{ route('products.show', $product->slug) }}"
                                                            class="px-4 py-2 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                                            查看詳情
                                                        </a>
                                                        <button
                                                            wire:click="addToCart({{ $product->id }})"
                                                            class="px-4 py-2 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                                            <i class="fas fa-cart-plus mr-1"></i>
                                                            加入購物車
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">沒有找到商品</h3>
                            <p class="text-gray-500 mb-6">請嘗試調整篩選條件或搜尋關鍵字</p>
                            <button
                                wire:click="clearFilters"
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                清除所有篩選
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Shopping Cart Sidebar (Fixed) -->
    <div
        x-data="{ open: false }"
        @cart-updated.window="open = true; setTimeout(() => open = false, 3000)"
        class="fixed bottom-4 right-4 z-50">

        <!-- Cart Button -->
        <button
            @click="open = !open"
            class="bg-blue-500 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg hover:bg-blue-600 transition-all hover:scale-110 relative">
            <i class="fas fa-shopping-cart text-xl"></i>
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                    {{ $cartCount }}
                </span>
            @endif
        </button>

        <!-- Cart Dropdown -->
        <div
            x-show="open"
            x-transition
            @click.away="open = false"
            class="absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">

            <div class="p-4 border-b bg-blue-50">
                <h3 class="font-semibold text-gray-900">購物車 ({{ $cartCount }})</h3>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @if(empty($cart))
                    <div class="p-8 text-center">
                        <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">購物車是空的</p>
                    </div>
                @else
                    @foreach($cart as $index => $item)
                        <div class="p-4 border-b hover:bg-gray-50">
                            <div class="flex gap-3">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-blue-600 font-semibold">NT$ {{ number_format($item['price']) }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input
                                            type="number"
                                            wire:change="updateCartQuantity({{ $index }}, $event.target.value)"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            class="w-16 px-2 py-1 text-sm border border-gray-300 rounded">
                                        <button
                                            wire:click="removeFromCart({{ $index }})"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-semibold text-gray-900">總計：</span>
                            <span class="text-xl font-bold text-blue-600">NT$ {{ number_format($this->cartTotal) }}</span>
                        </div>
                        <a
                            href="{{ route('checkout') }}"
                            class="block w-full px-4 py-3 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                            前往結帳
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading.flex style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center justify-center">
            <div class="mx-auto">
                <img src="{{ asset('images/loading.gif') }}" width="150" />
            </div>
            <p class="mt-4 text-gray-600">載入中...</p>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('notify', (event) => {
        const data = event[0];
        // 使用你的通知系統
        if (data.type === 'success') {
            // 成功提示
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${data.message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        } else {
            alert(data.message);
        }
    });
</script>
@endscript
