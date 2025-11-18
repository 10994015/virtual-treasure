<div>
    <section class="py-20 bg-gray-50">
        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-lg">
                <!-- Success Icon -->
                <div class="mb-6 text-center">
                    <div class="flex items-center justify-center w-20 h-20 mx-auto bg-green-100 rounded-full">
                        <i class="text-4xl text-green-500 fas fa-check"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="mb-4 text-3xl font-bold text-center text-gray-900">訂單已成立！</h1>
                <p class="mb-8 text-lg text-center text-gray-600">
                    感謝您的購買，訂單確認信已發送至您的電子郵件
                </p>

                <!-- Order Info -->
                <div class="p-6 mb-6 border border-blue-200 rounded-lg bg-blue-50">
                    <div class="text-center">
                        <div class="mb-2 text-sm text-gray-600">訂單編號</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $order->order_number }}</div>
                    </div>
                </div>

                <!-- Order Items Preview -->
                @if($order->items->count() > 0)
                    <div class="py-4 mb-6 border-t border-b">
                        <h3 class="mb-3 font-semibold text-gray-900">訂單商品</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3 text-sm">
                                    @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="object-cover w-12 h-12 rounded">
                                    @else
                                        <div class="flex items-center justify-center w-12 h-12 bg-gray-200 rounded">
                                            <i class="text-gray-400 fas fa-image"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                        <div class="text-gray-500">數量：{{ $item->quantity }}</div>
                                    </div>
                                    <div class="font-semibold text-blue-600">
                                        NT$ {{ number_format($item->subtotal) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-4 mt-4 border-t">
                            <span class="font-semibold text-gray-900">總計</span>
                            <span class="text-xl font-bold text-blue-600">NT$ {{ number_format($order->total) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <a
                        href="{{ route('products.index') }}"
                        class="px-6 py-3 font-semibold text-center text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                        <i class="mr-2 fas fa-shopping-bag"></i>繼續購物
                    </a>
                    <a
                        href="{{ route('orders.show', $order->id) }}"
                        class="px-6 py-3 font-semibold text-center text-gray-700 transition-colors bg-gray-200 rounded-lg hover:bg-gray-300">
                        <i class="mr-2 fas fa-eye"></i>查看訂單
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="pt-8 mt-8 text-center border-t">
                    <p class="mb-2 text-sm text-gray-500">如有任何問題，請聯繫客服</p>
                    <div class="flex items-center justify-center gap-4 text-sm">
                        <a href="mailto:support@cyim.com" class="text-blue-600 hover:underline">
                            <i class="mr-1 fas fa-envelope"></i>support@cyim.com
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="text-blue-600 hover:underline">
                            <i class="mr-1 fas fa-comments"></i>線上客服
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
