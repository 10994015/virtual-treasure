<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">購物車</h1>
                <p class="text-lg text-gray-600">確認您的商品並前往結帳</p>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(empty($cart))
                <!-- Empty Cart -->
                <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">購物車是空的</h3>
                    <p class="text-gray-500 mb-6">快去挑選您喜歡的商品吧！</p>
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>前往商城
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <!-- Cart Header -->
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="font-semibold text-gray-900">
                                    購物車商品 ({{ $cartCount }})
                                </h2>
                                <button
                                    wire:click="clearCart"
                                    wire:confirm="確定要清空購物車嗎？"
                                    class="text-sm text-red-500 hover:text-red-700 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>清空購物車
                                </button>
                            </div>

                            <!-- Cart Items List -->
                            <div class="divide-y">
                                @foreach($cart as $index => $item)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex gap-4">
                                            <!-- Product Image -->
                                            <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                @if($item['image'])
                                                    <img
                                                        src="{{ $item['image'] }}"
                                                        alt="{{ $item['name'] }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h3 class="font-semibold text-gray-900">
                                                        {{ $item['name'] }}
                                                    </h3>
                                                    <button
                                                        wire:click="removeFromCart({{ $index }})"
                                                        class="text-red-500 hover:text-red-700 transition-colors ml-4">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <p class="text-lg font-bold text-blue-600 mb-3">
                                                    NT$ {{ number_format($item['price']) }}
                                                </p>

                                                <!-- Quantity Controls -->
                                                <div class="flex items-center gap-4">
                                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                                        <button
                                                            wire:click="decreaseQuantity({{ $index }})"
                                                            class="px-3 py-2 hover:bg-gray-100 transition-colors">
                                                            <i class="fas fa-minus text-sm"></i>
                                                        </button>
                                                        <input
                                                            type="number"
                                                            wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                            value="{{ $item['quantity'] }}"
                                                            min="1"
                                                            max="{{ $item['stock'] }}"
                                                            class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none">
                                                        <button
                                                            wire:click="increaseQuantity({{ $index }})"
                                                            class="px-3 py-2 hover:bg-gray-100 transition-colors">
                                                            <i class="fas fa-plus text-sm"></i>
                                                        </button>
                                                    </div>

                                                    <div class="text-sm text-gray-500">
                                                        @if($item['stock'] > 0)
                                                            庫存：{{ $item['stock'] }}
                                                        @else
                                                            無限庫存
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Subtotal -->
                                                <div class="mt-3 text-right">
                                                    <span class="text-sm text-gray-600">小計：</span>
                                                    <span class="text-lg font-bold text-gray-900">
                                                        NT$ {{ number_format($item['price'] * $item['quantity']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Continue Shopping -->
                        <div class="mt-4">
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                繼續購物
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                            <div class="p-4 border-b border-gray-200">
                                <h2 class="font-semibold text-gray-900">訂單摘要</h2>
                            </div>

                            <div class="p-4 space-y-3">
                                <div class="flex justify-between text-gray-600">
                                    <span>商品小計</span>
                                    <span class="font-medium">NT$ {{ number_format($this->subtotal) }}</span>
                                </div>


                                <div class="border-t pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($this->total) }}
                                        </span>
                                    </div>
                                </div>

                                <button
                                    onclick="window.location='{{ route('checkout') }}'"
                                    class="w-full px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold text-lg">
                                    <i class="fas fa-lock mr-2"></i>前往結帳
                                </button>

                                <!-- Security Badge -->
                                <div class="text-center text-sm text-gray-500 pt-3 border-t">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    安全加密交易
                                </div>
                            </div>
                        </div>

                        <!-- Virtual Items Info -->
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-medium text-blue-900 mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                虛寶交易說明
                            </h3>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• 虛寶商品無需實體配送，免運費</li>
                                <li>• 完成付款後，賣家將透過遊戲內交易或提供兌換碼</li>
                                <li>• 請確保您的遊戲ID正確</li>
                            </ul>
                        </div>

                        <!-- Payment Methods -->
                        <div class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 class="font-medium text-gray-900 mb-3">支援付款方式</h3>
                            <div class="flex flex-wrap gap-2">
                                <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200 text-sm">
                                    <i class="fab fa-cc-visa text-blue-600"></i> VISA
                                </div>
                                <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200 text-sm">
                                    <i class="fab fa-cc-mastercard text-red-600"></i> Mastercard
                                </div>
                                <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200 text-sm">
                                    <i class="fas fa-university text-green-600"></i> ATM
                                </div>
                                <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200 text-sm">
                                    <i class="fas fa-store text-orange-600"></i> 超商付款
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading.flex style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center justify-center">
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

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
        toast.innerHTML = `<i class="fas fa-${data.type === 'success' ? 'check' : 'info'}-circle mr-2"></i>${data.message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
</script>
@endscript
