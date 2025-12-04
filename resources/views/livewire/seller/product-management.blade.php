<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">商品管理</h1>
                    <p class="text-lg text-gray-600">管理您的商品上架、編輯和銷售狀況</p>
                </div>
                <div class="flex gap-3">
                    @if(auth()->user()->is_admin)
                        <button
                            wire:click="toggleViewAllProducts"
                            class="px-6 py-3 {{ $showAllProducts ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-{{ $showAllProducts ? 'user-check' : 'users' }} mr-2"></i>
                            {{ $showAllProducts ? '查看所有商品' : '查看我的商品' }}
                        </button>
                    @endif
                    <a href="{{ route('seller.products.create') }}"
                        style="background-color: #3b82f6;"
                       class="px-6 py-3 ml-3 font-semibold text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-600">
                        <i class="mr-2 fas fa-plus"></i>上架新商品
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="py-8 bg-white shadow-sm">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <!-- 搜尋 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">搜尋商品</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="輸入商品名稱..."
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <!-- 類別 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">商品類別</label>
                        <select
                            wire:model.live="categoryFilter"
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">全部類別</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 遊戲 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">遊戲類型</label>
                        <select
                            wire:model.live="gameFilter"
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">全部遊戲</option>
                            @foreach($games as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 狀態 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">商品狀態</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">全部狀態</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 篩選資訊 -->
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        共 <span class="font-semibold text-blue-600">{{ $totalCount }}</span> 件商品
                        @if($showAllProducts && auth()->user()->is_admin)
                            <span class="ml-2 text-green-600">(所有賣家)</span>
                        @endif
                    </div>
                    <button
                        wire:click="clearFilters"
                        class="px-4 py-2 text-blue-500 transition-colors hover:text-blue-700 hover:underline">
                        清除篩選
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Items Grid -->
    <section class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($products as $product)
                        <div class="transition-shadow duration-300 bg-white rounded-lg shadow-md hover:shadow-xl">
                            <!-- 商品圖片 -->
                            <div class="relative flex items-center justify-center h-48 overflow-hidden bg-gray-100 rounded-t-lg">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="object-contain w-full  mx-auto my-auto max-h-48"
                                         style="height: 200px;width:100%" >
                                @else
                                     <img src="{{ asset('images/no-image.png') }}"
                                         alt="{{ $product->name }}"
                                         class="object-contain w-full  mx-auto my-auto max-h-48"
                                         style="height: 200px;width:100%" >
                                @endif

                                <!-- 稀有度標籤 -->
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded" style="
                                        background-color: {{
                                            $product->rarity === 'legendary' ? '#eab308' :
                                            ($product->rarity === 'mythic' ? '#ec4899' :
                                            ($product->rarity === 'epic' ? '#a855f7' :
                                            ($product->rarity === 'rare' ? '#3b82f6' :
                                            ($product->rarity === 'uncommon' ? '#10b981' : '#6b7280'))))
                                        }};
                                        color: #ffffff;
">
                                        {{ $rarities[$product->rarity] ?? '未知' }}
                                    </span>
                                </div>

                                @if($showAllProducts && auth()->user()->is_admin)
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 text-xs text-white bg-black rounded bg-opacity-70">
                                            {{ $product->user->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- 商品資訊 -->
                            <div class="p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="flex-1 text-lg font-semibold text-gray-900 truncate">
                                        {{ $product->name }}
                                    </h3>
                                    <span class="px-2 py-1 ml-2 text-xs rounded whitespace-nowrap" style="
                                        background-color: {{ $product->status === 'active' ? '#dcfce7' : ($product->status === 'inactive' ? '#f3f4f6' : ($product->status === 'draft' ? '#fef3c7' : '#fee2e2')) }};
                                        color: {{ $product->status === 'active' ? '#166534' : ($product->status === 'inactive' ? '#1f2937' : ($product->status === 'draft' ? '#854d0e' : '#991b1b')) }};
                                    ">
                                        {{ $statuses[$product->status] ?? $product->status }}
                                    </span>
                                </div>

                                <div class="mb-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">價格:</span>
                                        <span class="font-semibold text-blue-600">NT$ {{ number_format($product->price) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">遊戲:</span>
                                        <span class="ml-2 truncate">{{ $product->game_type }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">庫存:</span>
                                        <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $product->stock }} 件
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">類別:</span>
                                        <span>{{ $product->category }}</span>
                                    </div>
                                </div>

                                <!-- 操作按鈕 -->
                                <div class="flex items-center justify-between pt-3 border-t">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('seller.products.edit', $product) }}"
                                           class="p-2 text-blue-600 transition-colors rounded hover:bg-blue-50"
                                           title="編輯">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button
                                            wire:click="toggleProductStatus({{ $product->id }})"
                                            wire:confirm="確定要{{ $product->status === 'active' ? '下架' : '上架' }}此商品嗎？"
                                            class="p-2 text-{{ $product->status === 'active' ? 'orange' : 'green' }}-600 hover:bg-{{ $product->status === 'active' ? 'orange' : 'green' }}-50 rounded transition-colors"
                                            title="{{ $product->status === 'active' ? '下架' : '上架' }}">
                                            <i class="fas fa-eye{{ $product->status === 'active' ? '-slash' : '' }}"></i>
                                        </button>

                                    </div>
                                     <button
                                        wire:click="deleteProduct({{ $product->id }})"
                                        wire:confirm="確定要刪除此商品嗎？此操作無法復原！"
                                        class="p-2 text-red-600 transition-colors rounded hover:bg-red-50"
                                        title="刪除">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <i class="mb-4 text-6xl text-gray-300 fas fa-box-open"></i>
                    <h3 class="mb-2 text-xl font-semibold text-gray-600">沒有找到商品</h3>
                    <p class="mb-6 text-gray-500">
                        @if($searchTerm || $categoryFilter || $gameFilter || $statusFilter)
                            請嘗試調整篩選條件
                        @else
                            開始上架您的第一個商品
                        @endif
                    </p>
                    @if(!$searchTerm && !$categoryFilter && !$gameFilter && !$statusFilter)
                        <a href="{{ route('seller.products.create') }}"
                        style="background-color: #3b82f6;"
                           class="inline-block px-6 py-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-600">
                            <i class="mr-2 fas fa-plus"></i>上架新商品
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

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
        // 使用你的通知系統顯示訊息
        alert(data.message);
    });
</script>
@endscript
