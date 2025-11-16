<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">訂單管理</h1>
                    <p class="text-lg text-gray-600">管理您的訂單和交易</p>
                </div>
                <div class="flex gap-3">
                    @if(auth()->user()->is_admin)
                        <button
                            wire:click="toggleViewAllOrders"
                            class="px-6 py-3 {{ $showAllOrders ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white rounded-lg font-semibold transition-colors">
                            <i class="fas fa-{{ $showAllOrders ? 'user-check' : 'users' }} mr-2"></i>
                            {{ $showAllOrders ? '查看所有訂單' : '查看我的訂單' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="py-8 bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- 搜尋 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">搜尋訂單</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="訂單編號、買家姓名或信箱..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                    </div>

                    <!-- 訂單狀態 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">訂單狀態</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="">全部狀態</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 付款狀態 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">付款狀態</label>
                        <select
                            wire:model.live="paymentStatusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                            <option value="">全部狀態</option>
                            @foreach($paymentStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 篩選資訊 -->
                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        共 <span class="font-semibold text-blue-600">{{ $totalCount }}</span> 筆訂單
                        @if($showAllOrders && auth()->user()->is_admin)
                            <span class="ml-2 text-green-600">(所有訂單)</span>
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

    <!-- Orders List -->
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Order Header -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div>
                                            <div class="text-sm text-gray-500">訂單編號</div>
                                            <div class="font-semibold text-gray-900">{{ $order->order_number }}</div>
                                        </div>
                                        <div class="h-10 w-px bg-gray-300"></div>
                                        <div>
                                            <div class="text-sm text-gray-500">下單時間</div>
                                            <div class="text-sm text-gray-900">{{ $order->created_at->format('Y/m/d H:i') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <!-- 訂單狀態 -->
                                        <span class="px-3 py-1 text-sm rounded-full {{
                                            $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                            ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                            ($order->status === 'paid' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'))
                                        }}">
                                            {{ $statuses[$order->status] ?? $order->status }}
                                        </span>

                                        <!-- 付款狀態 -->
                                        <span class="px-3 py-1 text-sm rounded-full {{
                                            $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                            ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')
                                        }}">
                                            {{ $paymentStatuses[$order->payment_status] ?? $order->payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Content -->
                            <div class="p-6">
                                <!-- Buyer Info -->
                                <div class="mb-4 pb-4 border-b border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">買家：</span>
                                            <span class="font-medium text-gray-900">{{ $order->buyer_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">信箱：</span>
                                            <span class="text-gray-900">{{ $order->buyer_email }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">遊戲ID：</span>
                                            <span class="text-gray-900">{{ $order->buyer_game_id ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
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
                                                <h4 class="font-medium text-gray-900 mb-1">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600">
                                                    <span><i class="fas fa-gamepad mr-1"></i>{{ $item->game_type }}</span>
                                                    <span><i class="fas fa-tag mr-1"></i>{{ $item->product_category }}</span>
                                                    <span>數量：{{ $item->quantity }}</span>
                                                    @if($showAllOrders && auth()->user()->is_admin)
                                                        <span class="text-blue-600">
                                                            <i class="fas fa-user mr-1"></i>
                                                            賣家：{{ $item->seller->username ?? '-' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mt-2">
                                                    <span class="px-2 py-1 text-xs rounded {{
                                                        $item->delivery_status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                        ($item->delivery_status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                                                    }}">
                                                        交付狀態：{{
                                                            $item->delivery_status === 'delivered' ? '已交付' :
                                                            ($item->delivery_status === 'processing' ? '處理中' :
                                                            ($item->delivery_status === 'failed' ? '失敗' : '待處理'))
                                                        }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-sm text-gray-500">單價</div>
                                                <div class="font-semibold text-gray-900">NT$ {{ number_format($item->price) }}</div>
                                                <div class="text-sm text-gray-500 mt-1">小計</div>
                                                <div class="font-bold text-blue-600">NT$ {{ number_format($item->subtotal) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Summary & Actions -->
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-gray-200">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('seller.orders.show', $order) }}"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                            <i class="fas fa-eye mr-1"></i>查看詳情
                                        </a>

                                        @if(in_array($order->status, ['pending', 'paid']))
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="確定要取消此訂單嗎？"
                                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                                                <i class="fas fa-times mr-1"></i>取消訂單
                                            </button>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm text-gray-500 mb-1">訂單總額</div>
                                        <div class="text-2xl font-bold text-blue-600">NT$ {{ number_format($order->total) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            付款方式：{{ $paymentMethods[$order->payment_method] ?? '-' }}
                                        </div>
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
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">沒有找到訂單</h3>
                    <p class="text-gray-500 mb-6">
                        @if($searchTerm || $statusFilter || $paymentStatusFilter)
                            請嘗試調整篩選條件
                        @else
                            目前沒有任何訂單
                        @endif
                    </p>
                    @if($searchTerm || $statusFilter || $paymentStatusFilter)
                        <button
                            wire:click="clearFilters"
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            清除所有篩選
                        </button>
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
        const toast = document.createElement('div');

        let bgColor = 'bg-blue-500';
        if (data.type === 'success') bgColor = 'bg-green-500';
        if (data.type === 'error') bgColor = 'bg-red-500';
        if (data.type === 'warning') bgColor = 'bg-yellow-500';

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
        toast.innerHTML = `<i class="fas fa-${data.type === 'success' ? 'check' : 'info'}-circle mr-2"></i>${data.message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
</script>
@endscript
