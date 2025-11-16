<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">商品管理</h1>
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
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-600 font-semibold transition-colors ml-3">
                        <i class="fas fa-plus mr-2"></i>上架新商品
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="py-8 bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- 搜尋 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">搜尋商品</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="輸入商品名稱..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                    </div>

                    <!-- 類別 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">商品類別</label>
                        <select
                            wire:model.live="categoryFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="">全部類別</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 遊戲 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">遊戲類型</label>
                        <select
                            wire:model.live="gameFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="">全部遊戲</option>
                            @foreach($games as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 狀態 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">商品狀態</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="">全部狀態</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 篩選資訊 -->
                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        共 <span class="font-semibold text-blue-600">{{ $totalCount }}</span> 件商品
                        @if($showAllProducts && auth()->user()->is_admin)
                            <span class="ml-2 text-green-600">(所有賣家)</span>
                        @endif
                    </div>
                    <button
                        wire:click="clearFilters"
                        class="px-4 py-2 text-blue-500 hover:text-blue-700 hover:underline transition-colors">
                        清除篩選
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Items Grid -->
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <!-- 商品圖片 -->
                            <div class="relative bg-gray-100 rounded-t-lg h-48 flex items-center justify-center overflow-hidden">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full max-h-48 object-cover"
                                         style="height: 200px;width:100%" >
                                @else
                                     <img src="{{ asset('images/no-image.png') }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full max-h-48 object-cover"
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
                                        <span class="px-2 py-1 text-xs bg-black bg-opacity-70 text-white rounded">
                                            {{ $product->user->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- 商品資訊 -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-semibold text-gray-900 text-lg truncate flex-1">
                                        {{ $product->name }}
                                    </h3>
                                    <span class="ml-2 px-2 py-1 text-xs rounded whitespace-nowrap" style="
                                        background-color: {{ $product->status === 'active' ? '#dcfce7' : ($product->status === 'inactive' ? '#f3f4f6' : ($product->status === 'draft' ? '#fef3c7' : '#fee2e2')) }};
                                        color: {{ $product->status === 'active' ? '#166534' : ($product->status === 'inactive' ? '#1f2937' : ($product->status === 'draft' ? '#854d0e' : '#991b1b')) }};
                                    ">
                                        {{ $statuses[$product->status] ?? $product->status }}
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">價格:</span>
                                        <span class="font-semibold text-blue-600">NT$ {{ number_format($product->price) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">遊戲:</span>
                                        <span class="truncate ml-2">{{ $product->game_type }}</span>
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
                                <div class="flex justify-between items-center pt-3 border-t">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('seller.products.edit', $product) }}"
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors"
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
                                        class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors"
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
                <div class="text-center py-16">
                    <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">沒有找到商品</h3>
                    <p class="text-gray-500 mb-6">
                        @if($searchTerm || $categoryFilter || $gameFilter || $statusFilter)
                            請嘗試調整篩選條件
                        @else
                            開始上架您的第一個商品
                        @endif
                    </p>
                    @if(!$searchTerm && !$categoryFilter && !$gameFilter && !$statusFilter)
                        <a href="{{ route('seller.products.create') }}"
                        style="background-color: #3b82f6;"
                           class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>上架新商品
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

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
        // 使用你的通知系統顯示訊息
        alert(data.message);
    });
</script>
@endscript
