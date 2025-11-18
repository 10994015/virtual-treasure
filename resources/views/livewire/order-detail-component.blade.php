<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">訂單詳情</h1>
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
                        class="inline-flex items-center px-6 py-3 text-gray-600 transition-colors hover:text-gray-800">
                        <i class="mr-2 fas fa-arrow-left"></i>返回訂單列表
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Order Status Progress -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h2 class="mb-6 text-xl font-semibold text-gray-900">訂單進度</h2>

                        <div class="relative">
                            <!-- Progress Line -->
                            <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200"></div>

                            <div class="relative space-y-8">
                                <!-- Order Created -->
                                <div class="flex items-start gap-4">
                                    <div class="relative z-10 flex items-center justify-center flex-shrink-0 w-8 h-8 text-white bg-green-500 rounded-full">
                                        <i class="text-sm fas fa-check"></i>
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
                    <!-- 🔥 虛寶序號區塊 -->
                    @if($this->codesGroupedByProduct->isNotEmpty())
                        <div class="p-6 border-2 border-yellow-300 rounded-lg shadow-lg bg-gradient-to-br from-yellow-50 to-orange-50">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex items-center justify-center w-12 h-12 bg-yellow-500 rounded-full">
                                    <i class="text-xl text-white fas fa-key"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-yellow-900">您的虛寶序號</h3>
                                    <p class="text-sm text-yellow-700">請妥善保管您的序號</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @foreach($this->codesGroupedByProduct as $productId => $codes)
                                    @php
                                        $product = $codes->first()->product;
                                    @endphp

                                    <div class="p-5 bg-white border-2 border-yellow-200 rounded-lg shadow-sm">
                                        <!-- 商品名稱 -->
                                        <div class="flex items-center gap-3 pb-3 mb-4 border-b border-yellow-100">
                                            <i class="text-yellow-600 fas fa-box"></i>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h4>
                                            <span class="ml-auto text-sm text-gray-500">共 {{ $codes->count() }} 個序號</span>
                                        </div>

                                        <!-- 序號列表 -->
                                        <div class="space-y-3">
                                            @foreach($codes as $index => $code)
                                                <div class="p-4 transition-all border border-gray-200 rounded-lg bg-gradient-to-r from-gray-50 to-gray-100 hover:shadow-md">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <div class="flex items-center flex-1 min-w-0 gap-3">
                                                            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm font-bold text-white bg-yellow-500 rounded-full">
                                                                {{ $index + 1 }}
                                                            </div>
                                                            <code class="flex-1 font-mono text-lg font-bold text-gray-900 break-all select-all">
                                                                {{ $code->code }}
                                                            </code>
                                                        </div>

                                                        <button
                                                            onclick="copyCode('{{ $code->code }}', this)"
                                                            class="flex items-center flex-shrink-0 gap-2 px-4 py-2 font-medium text-white transition-all bg-blue-500 rounded-lg shadow-sm hover:bg-blue-600 active:bg-blue-700 hover:shadow">
                                                            <i class="fas fa-copy"></i>
                                                            <span class="copy-text">複製</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- 提示訊息 -->
                            <div class="p-4 mt-6 bg-yellow-100 border border-yellow-300 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="mb-2 font-semibold text-yellow-900">重要提示：</p>
                                        <ul class="space-y-1 text-sm text-yellow-800 list-disc list-inside">
                                            <li>請立即複製並保存您的序號到安全的地方</li>
                                            <li>每個序號僅能使用一次，請勿分享給他人</li>
                                            <li>如有序號無法使用，請聯繫賣家或客服</li>
                                            <li>序號一經使用，恕不接受退款</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Order Items -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">訂單商品</h2>

                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex gap-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0 w-24 h-24 overflow-hidden bg-gray-100 rounded">
                                            @if($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="object-cover w-full h-full">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full">
                                                    <i class="text-2xl text-gray-400 fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="mb-2 font-semibold text-gray-900">{{ $item->product_name }}</h3>

                                            <div class="grid grid-cols-2 gap-2 mb-3 text-sm text-gray-600">
                                                <div><i class="mr-1 fas fa-gamepad"></i>{{ $item->game_type }}</div>
                                                <div><i class="mr-1 fas fa-tag"></i>{{ $item->product_category }}</div>
                                                @if($item->game_server)
                                                    <div><i class="mr-1 fas fa-server"></i>{{ $item->game_server }}</div>
                                                @endif
                                                @if($item->game_region)
                                                    <div><i class="mr-1 fas fa-globe"></i>{{ $item->game_region }}</div>
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
                                                <div class="p-3 mt-3 border border-green-200 rounded bg-green-50">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm font-medium text-green-900">兌換碼/序號</span>
                                                        <script>
                                                             function copyToClipboard(text) {
                                                                navigator.clipboard.writeText(text).then(() => {
                                                                    const toast = document.createElement('div');
                                                                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                                                                    toast.innerHTML = '<i class="mr-2 fas fa-check-circle"></i>兌換碼已複製到剪貼簿！';
                                                                    document.body.appendChild(toast);
                                                                    setTimeout(() => toast.remove(), 2000);
                                                                });
                                                            }
                                                        </script>
                                                        <button
                                                            onclick="copyToClipboard('{{ $item->delivery_code }}')"
                                                            class="text-sm text-green-700 underline hover:text-green-900">
                                                            <i class="mr-1 fas fa-copy"></i>複製
                                                        </button>
                                                    </div>
                                                    <div class="p-3 font-mono text-lg text-green-800 bg-white border border-green-300 rounded select-all">
                                                        {{ $item->delivery_code }}
                                                    </div>
                                                    @if($item->delivered_at)
                                                        <div class="mt-2 text-xs text-green-700">
                                                            <i class="mr-1 fas fa-clock"></i>
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
                    <div class="sticky p-6 space-y-6 bg-white border border-gray-200 rounded-lg shadow-sm top-4">
                        <!-- Order Summary -->
                        <div>
                            <h2 class="mb-4 text-xl font-semibold text-gray-900">訂單摘要</h2>

                            <div class="mb-4 space-y-3">
                                <div class="flex justify-between text-gray-600">
                                    <span>商品小計</span>
                                    <span class="font-medium">NT$ {{ number_format($order->subtotal) }}</span>
                                </div>

                                <div class="flex justify-between text-sm text-green-600">
                                    <span>運費</span>
                                    <span class="font-medium">免運費</span>
                                </div>

                                <div class="pt-3 border-t">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($order->total) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="pt-4 space-y-2 text-sm border-t">
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
                                    class="w-full px-4 py-3 font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                    <i class="mr-2 fas fa-credit-card"></i>前往付款
                                </button>
                                <button
                                    wire:click="cancelOrder"
                                    wire:confirm="確定要取消此訂單嗎？"
                                    class="w-full px-4 py-2 text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600">
                                    <i class="mr-2 fas fa-times"></i>取消訂單
                                </button>
                            </div>
                        @endif



                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // 複製序號功能
    function copyCode(code, button) {
        navigator.clipboard.writeText(code).then(() => {
            // 改變按鈕文字和樣式
            const originalText = button.querySelector('.copy-text').textContent;
            const icon = button.querySelector('i');

            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            icon.className = 'fas fa-check';
            button.querySelector('.copy-text').textContent = '已複製';

            // 顯示提示
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-fade-in flex items-center gap-3';
            toast.innerHTML = `
                <i class="text-xl fas fa-check-circle"></i>
                <div>
                    <div class="font-semibold">序號已複製！</div>
                    <div class="text-sm opacity-90">${code}</div>
                </div>
            `;
            document.body.appendChild(toast);

            // 2秒後移除提示並恢復按鈕
            setTimeout(() => {
                toast.remove();
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-blue-500', 'hover:bg-blue-600');
                icon.className = 'fas fa-copy';
                button.querySelector('.copy-text').textContent = originalText;
            }, 2000);
        }).catch(err => {
            console.error('複製失敗:', err);

            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.innerHTML = '<i class="mr-2 fas fa-times-circle"></i>複製失敗，請手動選取';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }

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
@endpush
