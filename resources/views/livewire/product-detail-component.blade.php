<div>
    <!-- Breadcrumb -->
    <section class="py-4 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-blue-600">
                    <i class="fas fa-home"></i>
                </a>
                <i class="text-xs fas fa-chevron-right"></i>
                <a href="{{ route('products.index') }}" class="hover:text-blue-600">商城</a>
                <i class="text-xs fas fa-chevron-right"></i>
                <a href="{{ route('products.index', ['game_type' => $product->game_type]) }}" class="hover:text-blue-600">
                    {{ $product->game_type }}
                </a>
                <i class="text-xs fas fa-chevron-right"></i>
                <span class="max-w-xs font-medium text-gray-900 truncate">{{ $product->name }}</span>
            </nav>
        </div>
    </section>

    <!-- Product Detail -->
    <section class="py-12 bg-white">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                <!-- Left Column - Images -->
                <div>
                    <!-- Main Image -->
                    <div class="mb-4 overflow-hidden bg-gray-100 rounded-lg aspect-square">
                        @if($selectedImage)
                            <img
                                src="/storage/{{ $selectedImage }}"
                                alt="{{ $product->name }}"
                                class="object-cover w-full h-full">
                        @else
                            <div class="flex items-center justify-center w-full h-full">
                                <i class="text-6xl text-gray-400 fas fa-image"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Images -->
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($product->images as $image)
                                <button
                                    wire:click="selectImage('{{ $image->image_path }}')"
                                    class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 {{ $selectedImage === $image->image_path ? 'border-blue-500' : 'border-transparent' }} hover:border-blue-300 transition-all">
                                    <img
                                        src="/storage/{{ $image->image_path }}"
                                        alt="{{ $product->name }}"
                                        class="object-cover w-full h-full">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Product Info Cards (Desktop) -->
                    <div class="hidden mt-6 lg:block">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Seller Card -->
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="mb-2 text-sm text-gray-500">賣家</div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-10 h-10 font-semibold text-white bg-blue-500 rounded-full">
                                        {{ substr($product->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $product->user->name }}</div>
                                        <div class="text-xs text-gray-500">查看店鋪 →</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="mb-2 text-sm text-gray-500">交付方式</div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-{{ $deliveryMethods[$product->delivery_method]['icon'] ?? 'box' }} text-blue-500 text-xl"></i>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $deliveryMethods[$product->delivery_method]['label'] ?? '手動交付' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $deliveryMethods[$product->delivery_method]['desc'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div>
                    <!-- Title & Badges -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 {{ $rarityColors[$product->rarity]['bg'] }} {{ $rarityColors[$product->rarity]['text'] }} rounded-full text-sm font-medium">
                                {{ $rarityColors[$product->rarity]['label'] }}
                            </span>
                            <span class="px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full">
                                {{ $product->game_type }}
                            </span>
                            @if($product->is_featured)
                                <span class="px-3 py-1 text-sm text-yellow-800 bg-yellow-100 rounded-full">
                                    <i class="mr-1 fas fa-star"></i>精選
                                </span>
                            @endif
                        </div>
                        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span><i class="mr-1 fas fa-tag"></i>{{ $product->category }}</span>
                            @if($product->game_server)
                                <span><i class="mr-1 fas fa-server"></i>{{ $product->game_server }}</span>
                            @endif
                            @if($product->game_region)
                                <span><i class="mr-1 fas fa-globe"></i>{{ $product->game_region }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="p-6 mb-6 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50">
                        <div class="flex items-end gap-3">
                            <div>
                                <div class="mb-1 text-sm text-gray-600">售價</div>
                                <div class="text-4xl font-bold text-blue-600">
                                    NT$ {{ number_format($product->price) }}
                                </div>
                            </div>
                            @if($product->original_price && $product->original_price > $product->price)
                                <div class="mb-2">
                                    <div class="text-lg text-gray-500 line-through">
                                        NT$ {{ number_format($product->original_price) }}
                                    </div>
                                    <div class="text-sm font-medium text-red-600">
                                        省下 {{ $product->discount_percentage }}%
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($product->is_negotiable)
                            <div class="flex items-center gap-2 mt-3 text-sm text-green-700">
                                <i class="fas fa-comments-dollar"></i>
                                <span>此商品支援議價</span>
                            </div>
                        @endif
                    </div>

                    <!-- Stock Info -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">庫存狀態</span>
                            <span class="font-medium {{ $product->stockStatusColor }}">
                                @if($product->stock === 0)
                                    <i class="mr-1 fas fa-times-circle"></i>已售完
                                @elseif($product->stock > 10)
                                    <i class="mr-1 fas fa-check-circle"></i>庫存充足
                                @else
                                    <i class="mr-1 fas fa-exclamation-circle"></i>僅剩 {{ $product->stock }} 件
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="mb-6">
                        <label class="block mb-2 font-medium text-gray-700">購買數量</label>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border-2 border-gray-300 rounded-lg">
                                <button
                                    wire:click="decreaseQuantity"
                                    @if(!$product->is_in_stock) disabled @endif
                                    class="px-4 py-3 transition-colors hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input
                                    type="number"
                                    wire:model="quantity"
                                    min="1"
                                    max="{{ $product->stock }}"
                                    @if(!$product->is_in_stock) disabled @endif
                                    class="w-20 py-3 text-lg font-semibold text-center border-gray-300 border-x-2 focus:outline-none disabled:bg-gray-100"
                                    readonly>
                                <button
                                    wire:click="increaseQuantity"
                                    @if(!$product->is_in_stock) disabled @endif
                                    class="px-4 py-3 transition-colors hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="text-sm text-gray-600">
                                小計：<span class="text-xl font-bold text-blue-600">NT$ {{ number_format($product->price * $quantity) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mb-6 space-y-3">
                        @if($product->user_id !== auth()->id())
                            <livewire:start-conversation-button
                                :product-id="$product->id"
                                :seller-id="$product->user_id"
                                button-text="聯繫賣家"
                                button-class="w-full px-6 py-3 text-lg font-semibold text-white transition-colors bg-green-500 rounded-lg hover:bg-green-600" />
                        @endif

                        @if($product->is_in_stock)
                            <button
                                wire:click="buyNow"
                                class="w-full px-6 py-4 text-lg font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                <i class="mr-2 fas fa-bolt"></i>立即購買
                            </button>
                            <button
                                wire:click="addToCart"
                                class="w-full px-6 py-4 text-lg font-semibold text-blue-500 transition-colors bg-white border-2 border-blue-500 rounded-lg hover:bg-blue-50">
                                <i class="mr-2 fas fa-shopping-cart"></i>加入購物車
                            </button>
                        @else
                            <div class="w-full px-6 py-4 text-lg font-semibold text-center text-white bg-gray-400 rounded-lg cursor-not-allowed">
                                <i class="mr-2 fas fa-times-circle"></i>已售完
                            </div>
                        @endif
                    </div>

                    <!-- Product Info Cards (Mobile) -->
                    <div class="mb-6 lg:hidden">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Seller Card -->
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="mb-2 text-sm text-gray-500">賣家資訊</div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-12 h-12 text-lg font-semibold text-white bg-blue-500 rounded-full">
                                        {{ substr($product->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $product->user->name }}</div>
                                        <div class="text-sm text-gray-500">查看店鋪 →</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="mb-2 text-sm text-gray-500">交付方式</div>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-{{ $deliveryMethods[$product->delivery_method]['icon'] ?? 'box' }} text-blue-500 text-2xl"></i>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $deliveryMethods[$product->delivery_method]['label'] ?? '手動交付' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $deliveryMethods[$product->delivery_method]['desc'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Badge -->
                    <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                        <div class="flex items-center gap-3">
                            <i class="text-2xl text-green-600 fas fa-shield-check"></i>
                            <div>
                                <div class="font-semibold text-green-900">安全交易保障</div>
                                <div class="text-sm text-green-700">平台保障，安心購買</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Description -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button class="px-6 py-4 font-medium text-blue-600 border-b-2 border-blue-500">
                            商品說明
                        </button>
                        @if($product->specifications)
                            <button class="px-6 py-4 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 hover:border-gray-300">
                                商品規格
                            </button>
                        @endif
                        @if($product->delivery_instructions)
                            <button class="px-6 py-4 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 hover:border-gray-300">
                                交付說明
                            </button>
                        @endif
                    </nav>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Description -->
                    <div class="prose max-w-none">
                        <div class="leading-relaxed text-gray-700 whitespace-pre-line">
                            {{ $product->description }}
                        </div>
                    </div>

                    <!-- Specifications -->
                    @if($product->specifications && is_array($product->specifications))
                        <div class="pt-8 mt-8 border-t">
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">商品規格</h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach($product->specifications as $key => $value)
                                    <div class="flex items-start gap-3 p-3 rounded bg-gray-50">
                                        <i class="mt-1 text-blue-500 fas fa-check-circle"></i>
                                        <div class="flex-1">
                                            <div class="mb-1 text-sm text-gray-500">{{ $key }}</div>
                                            <div class="font-medium text-gray-900">
                                                @if(is_array($value))
                                                    <!-- 如果是陣列，用逗號分隔或換行顯示 -->
                                                    <div class="space-y-1">
                                                        @foreach($value as $item)
                                                            <div class="flex items-center gap-1">
                                                                <i class="text-xs text-gray-400 fas fa-angle-right"></i>
                                                                <span>{{ is_string($item) ? $item : json_encode($item) }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(is_bool($value))
                                                    <!-- 如果是布林值 -->
                                                    <span class="px-2 py-1 rounded text-xs {{ $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $value ? '是' : '否' }}
                                                    </span>
                                                @elseif(is_numeric($value))
                                                    <!-- 如果是數字 -->
                                                    {{ number_format($value) }}
                                                @else
                                                    <!-- 一般字串 -->
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Delivery Instructions -->
                    @if($product->delivery_instructions)
                        <div class="pt-8 mt-8 border-t">
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">交付說明</h3>
                            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                                <div class="leading-relaxed text-gray-700 whitespace-pre-line">
                                    {{ $product->delivery_instructions }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="py-12 bg-white">
            <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
                <h2 class="mb-8 text-2xl font-bold text-gray-900">相關商品</h2>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="group">
                            <div class="overflow-hidden transition-all bg-white border border-gray-200 rounded-lg hover:shadow-lg">
                                <!-- Image -->
                                <div class="overflow-hidden bg-gray-100 aspect-square">
                                    @php
                                        $relatedImage = $relatedProduct->images->where('is_primary', true)->first()
                                                     ?? $relatedProduct->images->first();
                                    @endphp
                                    @if($relatedImage)
                                        <img
                                            src="/storage/{{ $relatedImage->image_path }}"
                                            alt="{{ $relatedProduct->name }}"
                                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full">
                                            <i class="text-4xl text-gray-400 fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="p-4">
                                    <div class="mb-1 text-xs text-gray-500">{{ $relatedProduct->game_type }}</div>
                                    <h3 class="mb-2 font-semibold text-gray-900 transition-colors line-clamp-2 group-hover:text-blue-600">
                                        {{ $relatedProduct->name }}
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-blue-600">
                                            NT$ {{ number_format($relatedProduct->price) }}
                                        </span>
                                        @if($relatedProduct->original_price && $relatedProduct->original_price > $relatedProduct->price)
                                            <span class="text-xs font-medium text-red-600">
                                                -{{ $relatedProduct->discount_percentage }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading.flex style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg">
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
        const toast = document.createElement('div');

        let bgColor = 'bg-blue-500';
        if (data.type === 'success') bgColor = 'bg-green-500';
        if (data.type === 'error') bgColor = 'bg-red-500';
        if (data.type === 'warning') bgColor = 'bg-yellow-500';

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
        toast.innerHTML = `<i class="fas fa-${data.type === 'success' ? 'check' : 'info'}-circle mr-2"></i>${data.message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });

    $wire.on('cart-updated', (event) => {
        // 更新導航欄的購物車數量
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            cartBadge.textContent = event[0].count;
        }
    });
</script>
@endscript
