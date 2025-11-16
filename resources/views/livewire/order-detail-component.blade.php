<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">訂單詳情</h1>
                        <span class="px-3 py-1 text-sm rounded-full {{
                            $order->status === 'completed' ? 'bg-green-500 text-white' :
                            ($order->status === 'cancelled' ? 'bg-red-500 text-white' :
                            ($order->status === 'delivering' ? 'bg-purple-500 text-white' :
                            ($order->status === 'processing' ? 'bg-blue-500 text-white' : 'bg-yellow-500 text-white')))
                        }}">
                            {{ $statuses[$order->status] ?? $order->status }}
                        </span>
                    </div>
                    <p class="text-lg text-gray-600">訂單編號：{{ $order->order_number }}</p>
                </div>
                <div>
                    <a
                        href="{{ route('orders.index') }}"
                        class="inline-flex items-center px-6 py-3 text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>返回訂單列表
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Status Progress -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">訂單進度</h2>

                        <div class="relative">
                            <!-- Progress Line -->
                            <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200"></div>

                            <div class="space-y-8 relative">
                                <!-- Order Created -->
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white flex-shrink-0 relative z-10">
                                        <i class="fas fa-check text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">訂單已建立</div>
                                        <div class="text-sm text-gray-500">{{ $order->created_at->format('Y/m/d H:i:s') }}</div>
                                    </div>
                                </div>

                                <!-- Payment -->
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-full {{ $order->paid_at ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white flex-shrink-0 relative z-10">
                                        <i class="fas fa-{{ $order->paid_at ? 'check' : 'clock' }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">付款確認</div>
                                        @if($order->paid_at)
                                            <div class="text-sm text-gray-500">{{ $order->paid_at->format('Y/m/d H:i:s') }}</div>
                                        @else
                                            <div class="text-sm text-yellow-600">等待付款</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Delivering -->
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['delivering', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white flex-shrink-0 relative z-10">
                                        <i class="fas fa-{{ in_array($order->status, ['delivering', 'completed']) ? 'check' : 'clock' }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">商品交付</div>
                                        @if(in_array($order->status, ['delivering', 'completed']))
                                            <div class="text-sm text-gray-500">賣家交付中</div>
                                        @else
                                            <div class="text-sm text-gray-400">等待處理</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Completed -->
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-full {{ $order->completed_at ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white flex-shrink-0 relative z-10">
                                        <i class="fas fa-{{ $order->completed_at ? 'check' : 'clock' }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">訂單完成</div>
                                        @if($order->completed_at)
                                            <div class="text-sm text-gray-500">{{ $order->completed_at->format('Y/m/d H:i:s') }}</div>
                                        @else
                                            <div class="text-sm text-gray-400">尚未完成</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">訂單商品</h2>

                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex gap-4">
                                        <!-- Product Image -->
                                        <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                                            @if($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 mb-2">{{ $item->product_name }}</h3>

                                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                                <div><i class="fas fa-gamepad mr-1"></i>{{ $item->game_type }}</div>
                                                <div><i class="fas fa-tag mr-1"></i>{{ $item->product_category }}</div>
                                                @if($item->game_server)
                                                    <div><i class="fas fa-server mr-1"></i>{{ $item->game_server }}</div>
                                                @endif
                                                @if($item->game_region)
                                                    <div><i class="fas fa-globe mr-1"></i>{{ $item->game_region }}</div>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-4 mb-3">
                                                <div>
                                                    <span class="text-sm text-gray-500">單價：</span>
                                                    <span class="font-semibold text-gray-900">NT$ {{ number_format($item->price) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500">數量：</span>
                                                    <span class="font-semibold text-gray-900">{{ $item->quantity }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500">小計：</span>
                                                    <span class="font-bold text-blue-600">NT$ {{ number_format($item->subtotal) }}</span>
                                                </div>
                                            </div>

                                            <!-- Delivery Status -->
                                            <div class="flex items-center justify-between">
                                                <span class="px-3 py-1 text-sm rounded {{
                                                    $item->delivery_status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                    ($item->delivery_status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')
                                                }}">
                                                    {{
                                                        $item->delivery_status === 'delivered' ? '✓ 已交付' :
                                                        ($item->delivery_status === 'processing' ? '⏱ 處理中' : '⏳ 待處理')
                                                    }}
                                                </span>
                                            </div>

                                            <!-- Delivery Code -->
                                            @if($item->delivery_code && $item->delivery_status === 'delivered')
                                                <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm font-medium text-green-900">兌換碼/序號</span>
                                                        <script>
                                                             function copyToClipboard(text) {
                                                                navigator.clipboard.writeText(text).then(() => {
                                                                    const toast = document.createElement('div');
                                                                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                                                                    toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i>兌換碼已複製到剪貼簿！';
                                                                    document.body.appendChild(toast);
                                                                    setTimeout(() => toast.remove(), 2000);
                                                                });
                                                            }
                                                        </script>
                                                        <button
                                                            onclick="copyToClipboard('{{ $item->delivery_code }}')"
                                                            class="text-sm text-green-700 hover:text-green-900 underline">
                                                            <i class="fas fa-copy mr-1"></i>複製
                                                        </button>
                                                    </div>
                                                    <div class="font-mono text-lg text-green-800 bg-white p-3 rounded border border-green-300 select-all">
                                                        {{ $item->delivery_code }}
                                                    </div>
                                                    @if($item->delivered_at)
                                                        <div class="text-xs text-green-700 mt-2">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            交付時間：{{ $item->delivered_at->format('Y/m/d H:i:s') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                </div>

                <!-- Right Column - Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4 space-y-6">
                        <!-- Order Summary -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">訂單摘要</h2>

                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-gray-600">
                                    <span>商品小計</span>
                                    <span class="font-medium">NT$ {{ number_format($order->subtotal) }}</span>
                                </div>

                                <div class="flex justify-between text-green-600 text-sm">
                                    <span>運費</span>
                                    <span class="font-medium">免運費</span>
                                </div>

                                <div class="border-t pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($order->total) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="pt-4 border-t space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">付款方式</span>
                                    <span class="text-gray-900">{{ $paymentMethods[$order->payment_method] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">付款狀態</span>
                                    <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                                        {{ $order->payment_status === 'paid' ? '已付款' : '待付款' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        @if($order->status === 'pending')
                            <div class="space-y-2">
                                <button
                                    class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                                    <i class="fas fa-credit-card mr-2"></i>前往付款
                                </button>
                                <button
                                    wire:click="cancelOrder"
                                    wire:confirm="確定要取消此訂單嗎？"
                                    class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                    <i class="fas fa-times mr-2"></i>取消訂單
                                </button>
                            </div>
                        @endif



                    </div>
                </div>
            </div>
        </div>
    </section>
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
