<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">我的訂單</h1>
                <p class="text-lg text-gray-600">查看您的購買記錄和訂單狀態</p>
            </div>
        </div>
    </section>

    <!-- Status Tabs -->
    <section class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto gap-1 py-4">
                <button
                    wire:click="$set('statusFilter', '')"
                    class="px-6 py-3 rounded-lg whitespace-nowrap transition-all {{ !$statusFilter ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    全部
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ !$statusFilter ? 'bg-blue-600' : 'bg-gray-300' }}">
                        {{ $statusCounts['all'] }}
                    </span>
                </button>
                <button
                    wire:click="$set('statusFilter', 'pending')"
                    class="px-6 py-3 rounded-lg whitespace-nowrap transition-all {{ $statusFilter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    待付款
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $statusFilter === 'pending' ? 'bg-yellow-600' : 'bg-gray-300' }}">
                        {{ $statusCounts['pending'] }}
                    </span>
                </button>
                <button
                    wire:click="$set('statusFilter', 'processing')"
                    class="px-6 py-3 rounded-lg whitespace-nowrap transition-all {{ $statusFilter === 'processing' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    處理中
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $statusFilter === 'processing' ? 'bg-blue-600' : 'bg-gray-300' }}">
                        {{ $statusCounts['processing'] }}
                    </span>
                </button>
                <button
                    wire:click="$set('statusFilter', 'delivering')"
                    class="px-6 py-3 rounded-lg whitespace-nowrap transition-all {{ $statusFilter === 'delivering' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    交付中
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $statusFilter === 'delivering' ? 'bg-purple-600' : 'bg-gray-300' }}">
                        {{ $statusCounts['delivering'] }}
                    </span>
                </button>
                <button
                    wire:click="$set('statusFilter', 'completed')"
                    class="px-6 py-3 rounded-lg whitespace-nowrap transition-all {{ $statusFilter === 'completed' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    已完成
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $statusFilter === 'completed' ? 'bg-green-600' : 'bg-gray-300' }}">
                        {{ $statusCounts['completed'] }}
                    </span>
                </button>
            </div>
        </div>
    </section>

    <!-- Search Bar -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex-1 w-full">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="搜尋訂單編號或商品名稱..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                @if($searchTerm || $statusFilter)
                    <button
                        wire:click="clearFilters"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors whitespace-nowrap">
                        <i class="fas fa-times mr-2"></i>清除篩選
                    </button>
                @endif
            </div>
        </div>
    </section>

    <!-- Orders List -->
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Order Header -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">訂單編號</div>
                                            <div class="font-semibold text-gray-900">{{ $order->order_number }}</div>
                                        </div>
                                        <div class="h-10 w-px bg-gray-300 hidden sm:block"></div>
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">下單時間</div>
                                            <div class="text-sm text-gray-900">{{ $order->created_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <!-- 訂單狀態 -->
                                        <span class="px-3 py-1 text-sm font-medium rounded-full {{
                                            $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                            ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                            ($order->status === 'delivering' ? 'bg-purple-100 text-purple-800' :
                                            ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')))
                                        }}">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            {{ $statuses[$order->status] ?? $order->status }}
                                        </span>

                                        <!-- 付款狀態 -->
                                        @if($order->payment_status !== 'paid')
                                            <span class="px-3 py-1 text-sm rounded-full {{
                                                $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'
                                            }}">
                                                {{ $order->payment_status === 'failed' ? '付款失敗' : '待付款' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="p-6">
                                <div class="space-y-3 mb-4">
                                    @foreach($order->items as $item)
                                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg">
                                            <!-- Product Image -->
                                            <div class="w-20 h-20 flex-shrink-0 bg-gray-200 rounded overflow-hidden">
                                                @if($item->product_image)
                                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 mb-1 truncate">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-600 mb-2">
                                                    <span><i class="fas fa-gamepad mr-1"></i>{{ $item->game_type }}</span>
                                                    <span><i class="fas fa-layer-group mr-1"></i>{{ $item->product_category }}</span>
                                                    <span>x {{ $item->quantity }}</span>
                                                </div>

                                                <!-- Delivery Status -->
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-1 text-xs rounded {{
                                                        $item->delivery_status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                        ($item->delivery_status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                                                    }}">
                                                        {{
                                                            $item->delivery_status === 'delivered' ? '✓ 已交付' :
                                                            ($item->delivery_status === 'processing' ? '⏱ 處理中' : '⏳ 待處理')
                                                        }}
                                                    </span>

                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-sm text-gray-500">NT$</div>
                                                <div class="font-bold text-gray-900">{{ number_format($item->subtotal) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Footer -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-200">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('orders.show', $order->id) }}"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm inline-flex items-center">
                                            <i class="fas fa-eye mr-2"></i>查看詳情
                                        </a>

                                        @if($order->status === 'pending')
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="確定要取消此訂單嗎？"
                                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                                                <i class="fas fa-times mr-2"></i>取消訂單
                                            </button>
                                        @endif

                                    </div>

                                    <div class="text-right">
                                        <div class="text-xs text-gray-500 mb-1">訂單金額</div>
                                        <div class="text-2xl font-bold text-blue-600">NT$ {{ number_format($order->total) }}</div>
                                        @if($order->status === 'pending')
                                            <button
                                                class="mt-2 text-sm text-blue-600 hover:underline">
                                                前往付款 →
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="mb-4">
                        <i class="fas fa-shopping-bag text-gray-300 text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">
                        @if($searchTerm || $statusFilter)
                            沒有找到符合的訂單
                        @else
                            還沒有任何訂單
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6">
                        @if($searchTerm || $statusFilter)
                            請嘗試調整搜尋條件或篩選器
                        @else
                            快去商城挑選您喜歡的虛寶商品吧！
                        @endif
                    </p>
                    @if($searchTerm || $statusFilter)
                        <button
                            wire:click="clearFilters"
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-filter-circle-xmark mr-2"></i>清除篩選
                        </button>
                    @else
                        <a
                            href="{{ route('market') }}"
                            class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-shopping-bag mr-2"></i>前往商城
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
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


</script>
@endscript
