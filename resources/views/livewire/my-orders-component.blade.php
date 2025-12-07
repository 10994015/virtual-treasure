<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">我的訂單</h1>
                <p class="text-lg text-gray-600">查看您的購買記錄和訂單狀態</p>
            </div>
        </div>
    </section>

    <!-- Status Tabs -->
    <section class="bg-white shadow-sm">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-1 py-4 overflow-x-auto">
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
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-4 sm:flex-row">
                <div class="flex-1 w-full">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="搜尋訂單編號或商品名稱..."
                            class="w-full py-3 pl-10 pr-4 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <i class="absolute text-gray-400 -translate-y-1/2 fas fa-search left-3 top-1/2"></i>
                    </div>
                </div>
                @if($searchTerm || $statusFilter)
                    <button
                        wire:click="clearFilters"
                        class="px-6 py-3 text-gray-700 transition-colors bg-gray-200 rounded-lg hover:bg-gray-300 whitespace-nowrap">
                        <i class="mr-2 fas fa-times"></i>清除篩選
                    </button>
                @endif
            </div>
        </div>
    </section>

    <!-- Orders List -->
    <section class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="overflow-hidden transition-shadow bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md">
                            <!-- Order Header -->
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-4">
                                        <div>
                                            <div class="mb-1 text-xs text-gray-500">訂單編號</div>
                                            <div class="font-semibold text-gray-900">{{ $order->order_number }}</div>
                                        </div>
                                        <div class="hidden w-px h-10 bg-gray-300 sm:block"></div>
                                        <div>
                                            <div class="mb-1 text-xs text-gray-500">下單時間</div>
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
                                            <i class="mr-1 text-xs fas fa-circle"></i>
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
                                <div class="mb-4 space-y-3">
                                    @foreach($order->items as $item)
                                        <div class="flex gap-4 p-3 rounded-lg bg-gray-50">
                                            <!-- Product Image -->
                                            <div class="flex items-center justify-center flex-shrink-0 w-20 h-20 overflow-hidden bg-gray-200 rounded">
                                                @if($item->product_image)
                                                    <img
                                                        src="{{ $item->product_image }}"
                                                        onerror="this.onerror=null; this.src='/storage/{{ $item->product_image }}';"
                                                        alt="{{ $item->product_name }}"
                                                        class="object-contain w-full h-full">
                                                @else
                                                    <div class="flex items-center justify-center w-full h-full">
                                                        <i class="text-gray-400 fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="mb-1 font-medium text-gray-900 truncate">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap mb-2 text-xs text-gray-600 gap-x-3 gap-y-1">
                                                    <span><i class="mr-1 fas fa-gamepad"></i>{{ $item->game_type }}</span>
                                                    <span><i class="mr-1 fas fa-layer-group"></i>{{ $item->product_category }}</span>
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
                                            <div class="flex-shrink-0 text-right">
                                                <div class="text-sm text-gray-500">NT$</div>
                                                <div class="font-bold text-gray-900">{{ number_format($item->subtotal) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Footer -->
                                <div class="flex flex-col gap-4 pt-4 border-t border-gray-200 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('orders.show', $order->id) }}"
                                            class="inline-flex items-center px-4 py-2 text-sm text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                            <i class="mr-2 fas fa-eye"></i>查看詳情
                                        </a>

                                        @if($order->status === 'pending')
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="確定要取消此訂單嗎？"
                                                class="px-4 py-2 text-sm text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600">
                                                <i class="mr-2 fas fa-times"></i>取消訂單
                                            </button>
                                        @endif

                                    </div>

                                    <div class="text-right">
                                        <div class="mb-1 text-xs text-gray-500">訂單金額</div>
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
                <div class="py-16 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="mb-4">
                        <i class="text-6xl text-gray-300 fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-gray-600">
                        @if($searchTerm || $statusFilter)
                            沒有找到符合的訂單
                        @else
                            還沒有任何訂單
                        @endif
                    </h3>
                    <p class="mb-6 text-gray-500">
                        @if($searchTerm || $statusFilter)
                            請嘗試調整搜尋條件或篩選器
                        @else
                            快去商城挑選您喜歡的虛寶商品吧！
                        @endif
                    </p>
                    @if($searchTerm || $statusFilter)
                        <button
                            wire:click="clearFilters"
                            class="px-6 py-3 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                            <i class="mr-2 fas fa-filter-circle-xmark"></i>清除篩選
                        </button>
                    @else
                        <a
                            href="{{ route('products.index') }}"
                            class="inline-block px-6 py-3 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                            <i class="mr-2 fas fa-shopping-bag"></i>前往商城
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="p-6 bg-white rounded-lg">
            <div class="w-12 h-12 mx-auto border-b-2 border-blue-500 rounded-full animate-spin"></div>
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
