<div>
    <section class="py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-8">
                <!-- Success Icon -->
                <div class="text-center mb-6">
                    <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-500 text-4xl"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4 text-center">訂單已成立！</h1>
                <p class="text-lg text-gray-600 mb-8 text-center">
                    感謝您的購買，訂單確認信已發送至您的電子郵件
                </p>

                <!-- Order Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-2">訂單編號</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $order->order_number }}</div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-list-check text-blue-500 mr-2"></i>
                        接下來該做什麼？
                    </h3>
                    <ol class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2 mt-0.5">1</span>
                            <span>完成付款（如選擇非即時付款方式）</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2 mt-0.5">2</span>
                            <span>等待賣家確認並準備商品</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2 mt-0.5">3</span>
                            <span>賣家將透過遊戲內交易或提供兌換碼的方式交付商品</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2 mt-0.5">4</span>
                            <span>收到商品後，請記得給予評價！</span>
                        </li>
                    </ol>
                </div>

                <!-- Order Items Preview -->
                @if($order->items->count() > 0)
                    <div class="border-t border-b py-4 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">訂單商品</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3 text-sm">
                                    @if($item->product_image)
                                        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
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
                        <div class="mt-4 pt-4 border-t flex justify-between items-center">
                            <span class="font-semibold text-gray-900">總計</span>
                            <span class="text-xl font-bold text-blue-600">NT$ {{ number_format($order->total) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a
                        href="{{ route('products.index') }}"
                        class="px-6 py-3 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                        <i class="fas fa-shopping-bag mr-2"></i>繼續購物
                    </a>
                    <a
                        href="/"
                        class="px-6 py-3 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        <i class="fas fa-home mr-2"></i>返回首頁
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 pt-8 border-t text-center">
                    <p class="text-sm text-gray-500 mb-2">如有任何問題，請聯繫客服</p>
                    <div class="flex items-center justify-center gap-4 text-sm">
                        <a href="mailto:support@cyim.com" class="text-blue-600 hover:underline">
                            <i class="fas fa-envelope mr-1"></i>support@cyim.com
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="text-blue-600 hover:underline">
                            <i class="fas fa-comments mr-1"></i>線上客服
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
