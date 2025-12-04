<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">訂單管理</h1>
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
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- 搜尋 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">搜尋訂單</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="訂單編號、買家姓名或信箱..."
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <!-- 訂單狀態 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">訂單狀態</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">全部狀態</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 付款狀態 -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">付款狀態</label>
                        <select
                            wire:model.live="paymentStatusFilter"
                            class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">全部狀態</option>
                            @foreach($paymentStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 篩選資訊 -->
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        共 <span class="font-semibold text-blue-600">{{ $totalCount }}</span> 筆訂單
                        @if($showAllOrders && auth()->user()->is_admin)
                            <span class="ml-2 text-green-600">(所有訂單)</span>
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

    <!-- Orders List -->
    <section class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
                            <!-- Order Header -->
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div class="flex items-center gap-4">
                                        <div>
                                            <div class="text-sm text-gray-500">訂單編號</div>
                                            <div class="font-semibold text-gray-900">{{ $order->order_number }}</div>
                                        </div>
                                        <div class="w-px h-10 bg-gray-300"></div>
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
                                <div class="pb-4 mb-4 border-b border-gray-200">
                                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
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
                                <div class="mb-4 space-y-3">
                                    @foreach($order->items as $item)
                                        <div class="flex gap-4 p-3 rounded-lg bg-gray-50">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0 w-20 h-20 overflow-hidden bg-gray-200 rounded">
                                                @if($item->product_image)
                                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="object-contain object-center w-full h-full">
                                                @else
                                                    <div class="flex items-center justify-center w-full h-full">
                                                        <i class="text-gray-400 fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="mb-1 font-medium text-gray-900">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap text-sm text-gray-600 gap-x-4 gap-y-1">
                                                    <span><i class="mr-1 fas fa-gamepad"></i>{{ $item->game_type }}</span>
                                                    <span><i class="mr-1 fas fa-tag"></i>{{ $item->product_category }}</span>
                                                    <span>數量：{{ $item->quantity }}</span>
                                                    @if($showAllOrders && auth()->user()->is_admin)
                                                        <span class="text-blue-600">
                                                            <i class="mr-1 fas fa-user"></i>
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
                                            <div class="flex-shrink-0 text-right">
                                                <div class="text-sm text-gray-500">單價</div>
                                                <div class="font-semibold text-gray-900">NT$ {{ number_format($item->price) }}</div>
                                                <div class="mt-1 text-sm text-gray-500">小計</div>
                                                <div class="font-bold text-blue-600">NT$ {{ number_format($item->subtotal) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Summary & Actions -->
                                <div class="flex flex-col gap-4 pt-4 border-t border-gray-200 md:flex-row md:items-center md:justify-between">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('seller.orders.show', $order) }}"
                                            class="px-4 py-2 text-sm text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                            <i class="mr-1 fas fa-eye"></i>查看詳情
                                        </a>

                                        @if(in_array($order->status, ['pending', 'paid']))
                                            <button
                                                wire:click="cancelOrder({{ $order->id }})"
                                                wire:confirm="確定要取消此訂單嗎？"
                                                class="px-4 py-2 text-sm text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600">
                                                <i class="mr-1 fas fa-times"></i>取消訂單
                                            </button>
                                        @endif
                                    </div>

                                    <div class="text-right">
                                        <div class="mb-1 text-sm text-gray-500">訂單總額</div>
                                        <div class="text-2xl font-bold text-blue-600">NT$ {{ number_format($order->total) }}</div>
                                        <div class="mt-1 text-xs text-gray-500">
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
                <div class="py-16 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                    <i class="mb-4 text-6xl text-gray-300 fas fa-receipt"></i>
                    <h3 class="mb-2 text-xl font-semibold text-gray-600">沒有找到訂單</h3>
                    <p class="mb-6 text-gray-500">
                        @if($searchTerm || $statusFilter || $paymentStatusFilter)
                            請嘗試調整篩選條件
                        @else
                            目前沒有任何訂單
                        @endif
                    </p>
                    @if($searchTerm || $statusFilter || $paymentStatusFilter)
                        <button
                            wire:click="clearFilters"
                            class="px-6 py-3 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                            清除所有篩選
                        </button>
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
