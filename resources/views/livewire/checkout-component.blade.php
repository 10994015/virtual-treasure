<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">結帳</h1>
                <p class="text-lg text-gray-600">填寫訂單資訊，完成購買</p>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-2xl mx-auto mt-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 text-white bg-green-500 rounded-full">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">購物車</span>
                    </div>
                    <div class="w-16 h-1 mx-2 bg-blue-500"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 text-white bg-blue-500 rounded-full">
                            <span>2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">結帳</span>
                    </div>
                    <div class="w-16 h-1 mx-2 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 text-white bg-gray-300 rounded-full">
                            <span>3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">完成</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <form wire:submit="placeOrder">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Left Column - Forms -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Buyer Information -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                                <i class="mr-2 text-blue-500 fas fa-user"></i>
                                買家資訊
                            </h2>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        姓名 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model.live="buyer_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_name') border-red-500 @enderror"
                                        placeholder="請輸入您的姓名">
                                    @error('buyer_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        手機號碼
                                    </label>
                                    <input
                                        type="tel"
                                        wire:model.live="buyer_phone"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_phone') border-red-500 @enderror"
                                        placeholder="0912345678">
                                    @error('buyer_phone')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        電子郵件 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        wire:model.live="buyer_email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_email') border-red-500 @enderror"
                                        placeholder="example@email.com">
                                    <p class="mt-1 text-xs text-gray-500">訂單確認信將發送至此信箱</p>
                                    @error('buyer_email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        遊戲ID / 角色名稱
                                    </label>
                                    <input
                                        type="text"
                                        wire:model.live="buyer_game_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_game_id') border-red-500 @enderror"
                                        placeholder="請輸入您的遊戲ID或角色名稱">
                                    <p class="mt-1 text-xs text-gray-500">賣家將使用此資訊與您進行遊戲內交易</p>
                                    @error('buyer_game_id')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Trade Information -->
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <h3 class="mb-2 font-semibold text-blue-900">交易說明</h3>
                                    <ul class="space-y-1 text-sm text-blue-800">
                                        <li>• 完成付款後，賣家將會提供兌換碼的方式交付商品</li>
                                        <li>• 請確保您的遊戲ID正確，以便賣家能順利與您聯繫</li>
                                        <li>• 如有任何問題，可透過訂單頁面與賣家聯繫</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                                <i class="mr-2 text-blue-500 fas fa-credit-card"></i>
                                付款方式
                            </h2>

                            <div class="space-y-3">
                                @foreach($paymentMethods as $key => $method)
                                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all {{ $payment_method === $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                                        <input
                                            type="radio"
                                            wire:model.live="payment_method"
                                            value="{{ $key }}"
                                            class="mt-1 text-blue-500 focus:ring-blue-500">
                                        <div class="flex-1 ml-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $method['icon'] }} text-gray-500 mr-2"></i>
                                                    <span class="font-medium text-gray-900">{{ $method['name'] }}</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $method['desc'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Order Note -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="flex items-center mb-4 text-xl font-semibold text-gray-900">
                                <i class="mr-2 text-blue-500 fas fa-comment"></i>
                                訂單備註
                            </h2>

                            <textarea
                                wire:model.live="order_note"
                                rows="4"
                                maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="如有特殊需求或想對賣家說的話，請在此留言..."></textarea>
                            <p class="mt-1 text-sm text-right text-gray-500">
                                {{ strlen($order_note) }}/500
                            </p>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="sticky bg-white border border-gray-200 rounded-lg shadow-sm top-4">
                            <div class="p-4 border-b border-gray-200">
                                <h2 class="font-semibold text-gray-900">訂單摘要</h2>
                            </div>

                            <!-- Cart Items -->
                            <div class="p-4 overflow-y-auto border-b border-gray-200 max-h-64">
                                <div class="space-y-4">
                                    @foreach($cart as $item)
                                        <div class="flex gap-3">
                                            @if($item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="object-cover w-16 h-16 rounded">
                                            @else
                                                <div class="flex items-center justify-center w-16 h-16 bg-gray-200 rounded">
                                                    <i class="text-gray-400 fas fa-image"></i>
                                                </div>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                   storage                             <p class="text-xs text-gray-500">
                                                    @switch($item['trade_type'] ?? 'in_game')
                                                        @case('code')
                                                            <i class="mr-1 fas fa-key"></i>兌換碼交易
                                                            @break
                                                        @case('account')
                                                            <i class="mr-1 fas fa-user-circle"></i>帳號交易
                                                            @break
                                                        @case('auto')
                                                            <i class="mr-1 fas fa-bolt"></i>自動發貨
                                                            @break
                                                        @default
                                                            <i class="mr-1 fas fa-handshake"></i>遊戲內交易
                                                    @endswitch
                                                </p>
                                                <p class="text-sm text-gray-500">數量：{{ $item['quantity'] }}</p>
                                                <p class="text-sm font-semibold text-blue-600">NT$ {{ number_format($item['price'] * $item['quantity']) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Summary -->
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between text-gray-600">
                                    <span>商品小計</span>
                                    <span class="font-medium">NT$ {{ number_format($this->subtotal) }}</span>
                                </div>

                                <div class="flex justify-between text-green-600">
                                    <span>運費</span>
                                    <span class="font-medium">虛寶商品免運費</span>
                                </div>

                                <div class="pt-3 border-t">
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($this->total) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Terms Agreement -->
                                <div class="pt-3 border-t">
                                    <label class="flex items-start cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model.live="agreed_terms"
                                            class="mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">
                                            我已閱讀並同意
                                            <a href="#" class="text-blue-600 hover:underline">服務條款</a>
                                            和
                                            <a href="#" class="text-blue-600 hover:underline">隱私政策</a>
                                        </span>
                                    </label>
                                    @error('agreed_terms')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full px-6 py-3 text-lg font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                    <i class="mr-2 fas fa-lock"></i>確認並送出訂單
                                </button>

                                <!-- Back to Cart -->
                                <a
                                    href="{{ route('cart') }}"
                                    class="block w-full px-6 py-2 text-center text-gray-600 transition-colors hover:text-gray-800">
                                    <i class="mr-2 fas fa-arrow-left"></i>返回購物車
                                </a>

                                <!-- Security Info -->
                                <div class="pt-3 text-sm text-center text-gray-500 border-t">
                                    <i class="mr-1 fas fa-shield-alt"></i>
                                    SSL 安全加密連線
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Loading Indicator -->
    <div wire:loading.flex wire:target="placeOrder" style="width:100%;height:100%;position:fixed;top:0;left:0;z-index:9999;align-items:center;justify-content:center;background-color:rgba(0, 0, 0, 0.5);">
        <div class="flex flex-col items-center justify-center p-6 bg-white rounded-lg">
            <div class="mx-auto">
                <img src="{{ asset('images/loading.gif') }}" width="150" alt="載入中" />
            </div>
            <p class="mt-4 text-gray-600">正在處理訂單，請稍候...</p>
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
