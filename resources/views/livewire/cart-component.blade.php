<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">購物車</h1>
                <p class="text-lg text-gray-600">確認您的商品並前往結帳</p>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="min-h-screen py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            @if(empty($cart))
                <!-- Empty Cart -->
                <div class="py-16 text-center bg-white border border-gray-200 rounded-lg shadow-sm">
                    <i class="mb-4 text-6xl text-gray-300 fas fa-shopping-cart"></i>
                    <h3 class="mb-2 text-xl font-semibold text-gray-600">購物車是空的</h3>
                    <p class="mb-6 text-gray-500">快去挑選您喜歡的商品吧！</p>
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-block px-6 py-3 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                        <i class="mr-2 fas fa-shopping-bag"></i>前往商城
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        {{-- 🔥 區分議價商品和一般商品 --}}
                        @php
                            $bargainItems = collect($cart)->filter(fn($item) =>
                                isset($item['is_bargain']) && $item['is_bargain']
                            );
                            $normalItems = collect($cart)->filter(fn($item) =>
                                !isset($item['is_bargain']) || !$item['is_bargain']
                            );
                        @endphp

                        {{-- 🔥 議價商品區塊 --}}
                        @if($bargainItems->isNotEmpty())
                            <div class="p-4 mb-6 border-2 border-orange-300 rounded-lg shadow-sm bg-gradient-to-r from-orange-50 to-yellow-50">
                                <h3 class="flex items-center mb-4 text-lg font-bold text-orange-800">
                                    <i class="mr-2 fas fa-handshake"></i>
                                    議價成交商品
                                </h3>

                                <div class="space-y-4">
                                    @foreach($bargainItems as $index => $item)
                                        @php
                                            $actualIndex = array_search($item, $cart);
                                        @endphp
                                        <div class="p-4 transition-all bg-white border border-orange-200 rounded-lg hover:shadow-md">
                                            <div class="flex gap-4">
                                                <!-- Product Image -->
                                                <div class="flex-shrink-0 w-24 h-24 overflow-hidden bg-gray-100 rounded-lg">
                                                    @if($item['image'])
                                                        <img
                                                            src="{{ $item['image'] }}"
                                                            alt="{{ $item['name'] }}"
                                                            class="object-cover w-full h-full">
                                                    @else
                                                        <div class="flex items-center justify-center w-full h-full">
                                                            <i class="text-3xl text-gray-400 fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Product Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <div>
                                                            <h3 class="font-semibold text-gray-900">
                                                                {{ $item['name'] }}
                                                            </h3>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gradient-to-r from-orange-100 to-yellow-100 text-orange-800 border border-orange-300 mt-1">
                                                                <i class="mr-1 fas fa-handshake"></i>
                                                                議價成交
                                                            </span>
                                                        </div>
                                                        <button
                                                            wire:click="removeFromCart({{ $actualIndex }})"
                                                            class="ml-4 text-red-500 transition-colors hover:text-red-700">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>

                                                    <!-- 🔥 議價價格顯示 -->
                                                    <div class="mb-3">
                                                        <p class="text-lg font-bold text-orange-600">
                                                            NT$ {{ number_format($item['price']) }} / 個
                                                        </p>
                                                    </div>

                                                    <!-- 🔥 數量鎖定顯示 -->
                                                    <div class="flex items-center gap-4 p-3 border border-orange-200 rounded-lg bg-orange-50">
                                                        <div class="flex items-center gap-2">
                                                            <i class="text-orange-600 fas fa-lock"></i>
                                                            <span class="text-2xl font-bold text-orange-700">
                                                                {{ $item['quantity'] }} 個
                                                            </span>
                                                        </div>
                                                        <span class="px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded">
                                                            議價數量已鎖定
                                                        </span>
                                                    </div>

                                                    <!-- Subtotal -->
                                                    <div class="mt-3 text-right">
                                                        <span class="text-sm text-gray-600">小計：</span>
                                                        <span class="text-xl font-bold text-orange-600">
                                                            NT$ {{ number_format($item['price'] * $item['quantity']) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 🔥 一般商品區塊 --}}
                        @if($normalItems->isNotEmpty())
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <!-- Cart Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                    <h2 class="font-semibold text-gray-900">
                                        <i class="mr-2 fas fa-shopping-cart"></i>
                                        一般商品 ({{ $normalItems->count() }})
                                    </h2>
                                    <button
                                        wire:click="clearCart"
                                        wire:confirm="確定要清空購物車嗎？"
                                        class="text-sm text-red-500 transition-colors hover:text-red-700">
                                        <i class="mr-1 fas fa-trash"></i>清空購物車
                                    </button>
                                </div>

                                <!-- Cart Items List -->
                                <div class="divide-y">
                                    @foreach($normalItems as $index => $item)
                                        @php
                                            $actualIndex = array_search($item, $cart);
                                        @endphp
                                        <div class="p-4 transition-colors hover:bg-gray-50">
                                            <div class="flex gap-4">
                                                <!-- Product Image -->
                                                <div class="flex-shrink-0 w-24 h-24 overflow-hidden bg-gray-100 rounded-lg">
                                                    @if($item['image'])
                                                        <img
                                                            src="{{ $item['image'] }}"
                                                            alt="{{ $item['name'] }}"
                                                            class="object-cover w-full h-full">
                                                    @else
                                                        <div class="flex items-center justify-center w-full h-full">
                                                            <i class="text-3xl text-gray-400 fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Product Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <h3 class="font-semibold text-gray-900">
                                                            {{ $item['name'] }}
                                                        </h3>
                                                        <button
                                                            wire:click="removeFromCart({{ $actualIndex }})"
                                                            class="ml-4 text-red-500 transition-colors hover:text-red-700">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>

                                                    <p class="mb-3 text-lg font-bold text-blue-600">
                                                        NT$ {{ number_format($item['price']) }}
                                                    </p>

                                                    <!-- Quantity Controls -->
                                                    <div class="flex items-center gap-4">
                                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                                            <button
                                                                wire:click="decreaseQuantity({{ $actualIndex }})"
                                                                class="px-3 py-2 transition-colors hover:bg-gray-100">
                                                                <i class="text-sm fas fa-minus"></i>
                                                            </button>
                                                            <input
                                                                type="number"
                                                                wire:change="updateQuantity({{ $actualIndex }}, $event.target.value)"
                                                                value="{{ $item['quantity'] }}"
                                                                min="1"
                                                                max="{{ $item['stock'] }}"
                                                                class="w-16 py-2 text-center border-gray-300 border-x focus:outline-none">
                                                            <button
                                                                wire:click="increaseQuantity({{ $actualIndex }})"
                                                                class="px-3 py-2 transition-colors hover:bg-gray-100">
                                                                <i class="text-sm fas fa-plus"></i>
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
                        @else
                            {{-- 只有議價商品時的說明 --}}
                            @if($bargainItems->isNotEmpty())
                                <div class="p-4 text-center bg-white border border-gray-200 rounded-lg">
                                    <p class="text-gray-600">
                                        <i class="mr-2 fas fa-info-circle"></i>
                                        您也可以繼續購買其他商品
                                    </p>
                                </div>
                            @endif
                        @endif

                        <!-- Continue Shopping -->
                        <div class="mt-4">
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex items-center text-blue-600 transition-colors hover:text-blue-700">
                                <i class="mr-2 fas fa-arrow-left"></i>
                                繼續購物
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="sticky bg-white border border-gray-200 rounded-lg shadow-sm top-4">
                            <div class="p-4 border-b border-gray-200">
                                <h2 class="font-semibold text-gray-900">訂單摘要</h2>
                            </div>

                            <div class="p-4 space-y-3">
                                <div class="flex justify-between text-gray-600">
                                    <span>商品小計</span>
                                    <span class="font-medium">NT$ {{ number_format($this->subtotal) }}</span>
                                </div>

                                <div class="pt-3 border-t">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($this->total) }}
                                        </span>
                                    </div>
                                </div>

                                <button
                                    onclick="window.location='{{ route('checkout') }}'"
                                    class="w-full px-6 py-3 text-lg font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                    <i class="mr-2 fas fa-lock"></i>前往結帳
                                </button>

                                <!-- Security Badge -->
                                <div class="pt-3 text-sm text-center text-gray-500 border-t">
                                    <i class="mr-1 fas fa-shield-alt"></i>
                                    安全加密交易
                                </div>
                            </div>
                        </div>

                        <!-- Virtual Items Info -->
                        <div class="p-4 mt-4 border border-blue-200 rounded-lg bg-blue-50">
                            <h3 class="flex items-center mb-2 font-medium text-blue-900">
                                <i class="mr-2 fas fa-info-circle"></i>
                                虛寶交易說明
                            </h3>
                            <ul class="space-y-1 text-sm text-blue-800">
                                <li>• 虛寶商品無需實體配送，免運費</li>
                                <li>• 完成付款後，賣家將提供兌換碼</li>
                                <li>• 請確保您的遊戲ID正確</li>
                                {{-- 🔥 議價商品說明 --}}
                                @if($bargainItems->isNotEmpty())
                                    <li class="font-medium text-orange-700">
                                        • 購物車中包含 {{ $bargainItems->count() }} 件議價成交商品
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Payment Methods -->
                        <div class="p-4 mt-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h3 class="mb-3 font-medium text-gray-900">支援付款方式</h3>
                            <div class="flex flex-wrap gap-2">
                                <div class="px-3 py-2 text-sm border border-gray-200 rounded bg-gray-50">
                                    <i class="text-blue-600 fab fa-cc-visa"></i> VISA
                                </div>
                                <div class="px-3 py-2 text-sm border border-gray-200 rounded bg-gray-50">
                                    <i class="text-red-600 fab fa-cc-mastercard"></i> Mastercard
                                </div>
                                <div class="px-3 py-2 text-sm border border-gray-200 rounded bg-gray-50">
                                    <i class="text-green-600 fas fa-university"></i> ATM
                                </div>
                                <div class="px-3 py-2 text-sm border border-gray-200 rounded bg-gray-50">
                                    <i class="text-orange-600 fas fa-store"></i> 超商付款
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

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
        toast.innerHTML = `<i class="fas fa-${data.type === 'success' ? 'check' : 'info'}-circle mr-2"></i>${data.message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
</script>
@endscript
