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
                            ($order->status === 'paid' ? 'bg-blue-500 text-white' : 'bg-yellow-500 text-white'))
                        }}">
                            {{ $statuses[$order->status] ?? $order->status }}
                        </span>
                    </div>
                    <p class="text-lg text-gray-600">訂單編號：{{ $order->order_number }}</p>
                </div>
                <div class="flex gap-3">
                    <a
                        href="{{ route('seller.orders.index') }}"
                        class="inline-flex items-center px-6 py-3 text-gray-600 transition-colors hover:text-gray-800">
                        <i class="mr-2 fas fa-arrow-left"></i>返回列表
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column - Order Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Order Info -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                            <i class="mr-2 text-blue-500 fas fa-info-circle"></i>
                            訂單資訊
                        </h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <div class="mb-1 text-sm text-gray-500">下單時間</div>
                                <div class="font-medium text-gray-900">{{ $order->created_at->format('Y/m/d H:i:s') }}</div>
                            </div>

                            @if($order->paid_at)
                                <div>
                                    <div class="mb-1 text-sm text-gray-500">付款時間</div>
                                    <div class="font-medium text-gray-900">{{ $order->paid_at->format('Y/m/d H:i:s') }}</div>
                                </div>
                            @endif

                            @if($order->completed_at)
                                <div>
                                    <div class="mb-1 text-sm text-gray-500">完成時間</div>
                                    <div class="font-medium text-gray-900">{{ $order->completed_at->format('Y/m/d H:i:s') }}</div>
                                </div>
                            @endif

                            @if($order->cancelled_at)
                                <div>
                                    <div class="mb-1 text-sm text-gray-500">取消時間</div>
                                    <div class="font-medium text-gray-900">{{ $order->cancelled_at->format('Y/m/d H:i:s') }}</div>
                                </div>
                            @endif

                            <div>
                                <div class="mb-1 text-sm text-gray-500">付款方式</div>
                                <div class="font-medium text-gray-900">{{ $paymentMethods[$order->payment_method] ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="mb-1 text-sm text-gray-500">付款狀態</div>
                                <span class="px-2 py-1 text-sm rounded {{
                                    $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                    ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                                }}">
                                    {{
                                        $order->payment_status === 'paid' ? '已付款' :
                                        ($order->payment_status === 'failed' ? '付款失敗' :
                                        ($order->payment_status === 'refunded' ? '已退款' : '待付款'))
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Info -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                            <i class="mr-2 text-blue-500 fas fa-user"></i>
                            買家資訊
                        </h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <div class="mb-1 text-sm text-gray-500">姓名</div>
                                <div class="font-medium text-gray-900">{{ $order->buyer_name }}</div>
                            </div>

                            <div>
                                <div class="mb-1 text-sm text-gray-500">電子郵件</div>
                                <div class="font-medium text-gray-900">{{ $order->buyer_email }}</div>
                            </div>

                            @if($order->buyer_phone)
                                <div>
                                    <div class="mb-1 text-sm text-gray-500">手機號碼</div>
                                    <div class="font-medium text-gray-900">{{ $order->buyer_phone }}</div>
                                </div>
                            @endif

                            @if($order->buyer_game_id)
                                <div>
                                    <div class="mb-1 text-sm text-gray-500">遊戲ID</div>
                                    <div class="font-medium text-gray-900">{{ $order->buyer_game_id }}</div>
                                </div>
                            @endif

                            @if($order->buyer_note)
                                <div class="md:col-span-2">
                                    <div class="mb-1 text-sm text-gray-500">買家備註</div>
                                    <div class="p-3 font-medium text-gray-900 rounded bg-gray-50">{{ $order->buyer_note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                            <i class="mr-2 text-blue-500 fas fa-shopping-bag"></i>
                            訂單商品
                        </h2>

                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex gap-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0 w-24 h-24 overflow-hidden bg-gray-100 rounded">
                                            @if($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="object-contain w-full">
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

                                            <!-- Seller Info -->
                                            @if(auth()->user()->is_admin || $order->user_id === auth()->id())
                                                <div class="p-2 mb-3 text-sm rounded bg-gray-50">
                                                    <span class="text-gray-600">賣家：</span>
                                                    <span class="font-medium text-gray-900">{{ $item->seller->name ?? '-' }}</span>
                                                </div>
                                            @endif

                                            <!-- Delivery Status -->
                                            <div class="flex items-center justify-between">
                                                <span class="px-3 py-1 text-sm rounded {{
                                                    $item->delivery_status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                    ($item->delivery_status === 'processing' ? 'bg-blue-100 text-blue-800' :
                                                    ($item->delivery_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))
                                                }}">
                                                    交付狀態：{{
                                                        $item->delivery_status === 'delivered' ? '已交付' :
                                                        ($item->delivery_status === 'processing' ? '處理中' :
                                                        ($item->delivery_status === 'failed' ? '失敗' : '待處理'))
                                                    }}
                                                </span>

                                                @if($item->delivery_status !== 'delivered' &&
                                                    $order->payment_status === 'paid' &&
                                                    (auth()->user()->is_admin || $item->seller_id === auth()->id()))
                                                    <button
                                                        wire:click="$set('selectedItemId', {{ $item->id }})"
                                                        class="px-3 py-1 text-sm text-white transition-colors bg-blue-500 rounded hover:bg-blue-600">
                                                        <i class="mr-1 fas fa-check"></i>標記已交付
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Delivery Info -->
                                            @if($item->delivery_code)
                                                <div class="p-3 mt-3 border border-green-200 rounded bg-green-50">
                                                    <div class="mb-1 text-sm font-medium text-green-900">交付代碼/序號</div>
                                                    <div class="p-2 font-mono text-green-800 bg-white rounded">{{ $item->delivery_code }}</div>
                                                    @if($item->delivered_at)
                                                        <div class="mt-1 text-xs text-green-700">
                                                            交付時間：{{ $item->delivered_at->format('Y/m/d H:i:s') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Delivery Form -->
                                            @if($selectedItemId === $item->id)
                                                <div class="p-4 mt-3 border border-blue-200 rounded bg-blue-50">
                                                    <h4 class="mb-3 font-semibold text-gray-900">交付商品</h4>
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                                                交付代碼/序號 <span class="text-red-500">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                wire:model="deliveryCode"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                                                placeholder="輸入兌換碼或序號">
                                                        </div>
                                                        <div>
                                                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                                                備註說明
                                                            </label>
                                                            <textarea
                                                                wire:model="deliveryInfo"
                                                                rows="2"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                                                placeholder="如有其他說明請在此填寫"></textarea>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button
                                                                wire:click="deliverItem({{ $item->id }})"
                                                                class="px-4 py-2 text-white transition-colors bg-green-500 rounded-lg hover:bg-green-600">
                                                                <i class="mr-1 fas fa-check"></i>確認交付
                                                            </button>
                                                            <button
                                                                wire:click="$set('selectedItemId', null)"
                                                                class="px-4 py-2 text-gray-700 transition-colors bg-gray-300 rounded-lg hover:bg-gray-400">
                                                                取消
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Admin Note (Only for Admin) -->
                    @if(auth()->user()->is_admin)
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                                <i class="mr-2 text-blue-500 fas fa-sticky-note"></i>
                                管理員備註
                            </h2>

                            <div>
                                <textarea
                                    wire:model="adminNote"
                                    rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                    placeholder="內部備註，買家看不到..."></textarea>
                                <div class="flex justify-end mt-2">
                                    <button
                                        wire:click="updateAdminNote"
                                        class="px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                        <i class="mr-1 fas fa-save"></i>儲存備註
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Cancel Info -->
                    @if($order->status === 'cancelled')
                        <div class="p-6 border border-red-200 rounded-lg bg-red-50">
                            <h3 class="mb-2 font-semibold text-red-900">訂單已取消</h3>
                            @if($order->cancellation_reason)
                                <p class="mb-2 text-sm text-red-800">取消原因：{{ $order->cancellation_reason }}</p>
                            @endif
                            @if($order->cancelledBy)
                                <p class="text-sm text-red-700">
                                    取消者：{{ $order->cancelledBy->name }}
                                    ({{ $order->cancelled_at->format('Y/m/d H:i:s') }})
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="lg:col-span-1">
                    <!-- Order Summary -->
                    <div class="sticky p-6 bg-white border border-gray-200 rounded-lg shadow-sm top-4">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">訂單摘要</h2>

                        <div class="mb-4 space-y-3">
                            <div class="flex justify-between text-gray-600">
                                <span>商品小計</span>
                                <span class="font-medium">NT$ {{ number_format($order->subtotal) }}</span>
                            </div>

                            @if($order->coupon_discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>優惠折扣</span>
                                    <span class="font-medium">-NT$ {{ number_format($order->coupon_discount) }}</span>
                                </div>
                            @endif


                            <div class="pt-3 border-t">
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-semibold text-gray-900">總計</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                        NT$ {{ number_format($order->total) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 space-y-2 border-t">
                            @if(auth()->user()->is_admin)
                                @if($order->payment_status === 'pending')
                                    <button
                                        wire:click="markAsPaid"
                                        wire:confirm="確定要標記此訂單為已付款嗎？"
                                        class="w-full px-4 py-2 text-white transition-colors bg-green-500 rounded-lg hover:bg-green-600">
                                        <i class="mr-1 fas fa-check"></i>標記為已付款
                                    </button>
                                @endif

                                @if(in_array($order->status, ['paid', 'processing', 'delivering']) && $order->status !== 'completed')
                                    <button
                                        wire:click="completeOrder"
                                        wire:confirm="確定要標記此訂單為已完成嗎？"
                                        class="w-full px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                        <i class="mr-1 fas fa-flag-checkered"></i>標記為已完成
                                    </button>
                                @endif
                            @endif

                            @if(in_array($order->status, ['pending', 'paid']))
                                <button
                                    wire:click="cancelOrder"
                                    wire:confirm="確定要取消此訂單嗎？庫存將會恢復。"
                                    class="w-full px-4 py-2 text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600">
                                    <i class="mr-1 fas fa-times"></i>取消訂單
                                </button>
                            @endif
                        </div>

                        <!-- Timeline -->
                        <div class="pt-6 mt-6 border-t">
                            <h3 class="mb-3 font-semibold text-gray-900">訂單時間軸</h3>
                            <div class="space-y-3">
                                <div class="flex gap-2 text-sm">
                                    <i class="mt-1 text-xs text-blue-500 fas fa-circle"></i>
                                    <div>
                                        <div class="font-medium text-gray-900">訂單建立</div>
                                        <div class="text-gray-500">{{ $order->created_at->format('Y/m/d H:i:s') }}</div>
                                    </div>
                                </div>

                                @if($order->paid_at)
                                    <div class="flex gap-2 text-sm">
                                        <i class="mt-1 text-xs text-green-500 fas fa-circle"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">付款完成</div>
                                            <div class="text-gray-500">{{ $order->paid_at->format('Y/m/d H:i:s') }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($order->completed_at)
                                    <div class="flex gap-2 text-sm">
                                        <i class="mt-1 text-xs text-green-500 fas fa-circle"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">訂單完成</div>
                                            <div class="text-gray-500">{{ $order->completed_at->format('Y/m/d H:i:s') }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($order->cancelled_at)
                                    <div class="flex gap-2 text-sm">
                                        <i class="mt-1 text-xs text-red-500 fas fa-circle"></i>
                                        <div>
                                            <div class="font-medium text-gray-900">訂單取消</div>
                                            <div class="text-gray-500">{{ $order->cancelled_at->format('Y/m/d H:i:s') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading.flex  style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);">
        <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg">
            <div class="mx-auto">
                <img src="{{ asset('images/loading.gif') }}" width="150" alt="載入中" />
            </div>
            <p class="mt-4 text-gray-600">處理中...</p>
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
