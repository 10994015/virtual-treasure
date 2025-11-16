<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">結帳</h1>
                <p class="text-lg text-gray-600">填寫訂單資訊，完成購買</p>
            </div>

            <!-- Progress Steps -->
            <div class="max-w-2xl mx-auto mt-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">購物車</span>
                    </div>
                    <div class="w-16 h-1 bg-blue-500 mx-2"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white rounded-full">
                            <span>2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">結帳</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-300 mx-2"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-white rounded-full">
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
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="placeOrder">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Forms -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Buyer Information -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                買家資訊
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        姓名 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="buyer_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_name') border-red-500 @enderror"
                                        placeholder="請輸入您的姓名">
                                    @error('buyer_name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        手機號碼
                                    </label>
                                    <input
                                        type="tel"
                                        wire:model="buyer_phone"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_phone') border-red-500 @enderror"
                                        placeholder="0912345678">
                                    @error('buyer_phone')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        電子郵件 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        wire:model="buyer_email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('buyer_email') border-red-500 @enderror"
                                        placeholder="example@email.com">
                                    <p class="mt-1 text-xs text-gray-500">訂單確認信將發送至此信箱</p>
                                    @error('buyer_email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        遊戲ID / 角色名稱 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="buyer_game_id"
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
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-blue-900 mb-2">交易說明</h3>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>• 完成付款後，賣家將會透過遊戲內交易或提供兌換碼的方式交付商品</li>
                                        <li>• 請確保您的遊戲ID正確，以便賣家能順利與您聯繫</li>
                                        <li>• 如有任何問題，可透過訂單頁面與賣家聯繫</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                付款方式
                            </h2>

                            <div class="space-y-3">
                                @foreach($paymentMethods as $key => $method)
                                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all {{ $payment_method === $key ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                                        <input
                                            type="radio"
                                            wire:model="payment_method"
                                            value="{{ $key }}"
                                            class="mt-1 text-blue-500 focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
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
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-comment text-blue-500 mr-2"></i>
                                訂單備註
                            </h2>

                            <textarea
                                wire:model="order_note"
                                rows="4"
                                maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="如有特殊需求或想對賣家說的話，請在此留言..."></textarea>
                            <p class="mt-1 text-sm text-gray-500 text-right">
                                {{ strlen($order_note) }}/500
                            </p>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                            <div class="p-4 border-b border-gray-200">
                                <h2 class="font-semibold text-gray-900">訂單摘要</h2>
                            </div>

                            <!-- Cart Items -->
                            <div class="p-4 border-b border-gray-200 max-h-64 overflow-y-auto">
                                <div class="space-y-4">
                                    @foreach($cart as $item)
                                        <div class="flex gap-3">
                                            @if($item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                                <p class="text-xs text-gray-500">
                                                    @switch($item['trade_type'] ?? 'in_game')
                                                        @case('code')
                                                            <i class="fas fa-key mr-1"></i>兌換碼交易
                                                            @break
                                                        @case('account')
                                                            <i class="fas fa-user-circle mr-1"></i>帳號交易
                                                            @break
                                                        @case('auto')
                                                            <i class="fas fa-bolt mr-1"></i>自動發貨
                                                            @break
                                                        @default
                                                            <i class="fas fa-handshake mr-1"></i>遊戲內交易
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

                                <div class="border-t pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">總計</span>
                                        <span class="text-2xl font-bold text-blue-600">
                                            NT$ {{ number_format($this->total) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Terms Agreement -->
                                <div class="border-t pt-3">
                                    <label class="flex items-start cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="agreed_terms"
                                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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
                                    class="w-full px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold text-lg">
                                    <i class="fas fa-lock mr-2"></i>確認並送出訂單
                                </button>

                                <!-- Back to Cart -->
                                <a
                                    href="{{ route('cart') }}"
                                    class="block w-full text-center px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>返回購物車
                                </a>

                                <!-- Security Info -->
                                <div class="text-center text-sm text-gray-500 pt-3 border-t">
                                    <i class="fas fa-shield-alt mr-1"></i>
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
        <div class="bg-white rounded-lg p-6 flex flex-col items-center justify-center">
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
