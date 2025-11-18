<footer class="py-12 text-white bg-gray-900">
    <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center mb-4 space-x-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-system-blue rounded-system">
                        <i class="text-sm text-white fas fa-gem"></i>
                    </div>
                    <span class="text-lg font-semibold">虛擬寶物平台</span>
                </div>
                <p class="text-sm leading-relaxed text-gray-400">
                    專業的虛擬寶物交易平台，提供安全、透明、高效的遊戲道具交易服務。
                </p>
            </div>

            <div>
                <h4 class="mb-4 text-lg font-semibold">快速鏈接</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('products.index') }}" class="text-gray-400 transition-colors hover:text-system-blue">商品市場</a></li>
                    <li><a href="{{ route('cart') }}" class="text-gray-400 transition-colors hover:text-system-blue">購物車</a></li>
                    <li><a href="{{ route('ai-chat') }}" class="text-gray-400 transition-colors hover:text-system-blue">幫助中心</a></li>
                    <li><a href="#about" class="text-gray-400 transition-colors hover:text-system-blue">關於我們</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-lg font-semibold">客戶服務</h4>
                <ul class="space-y-2">
                    <li><a href="#" onclick="openChat()" class="text-gray-400 transition-colors hover:text-system-blue">聯絡客服</a></li>
                    <li><a href="{{ route('ai-chat') }}" class="text-gray-400 transition-colors hover:text-system-blue">常見問題</a></li>
                    <li><a href="{{ route('ai-chat') }}" class="text-gray-400 transition-colors hover:text-system-blue">退款政策</a></li>
                    <li><a href="{{ route('ai-chat') }}" class="text-gray-400 transition-colors hover:text-system-blue">服務條款</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-lg font-semibold">關注我們</h4>
                <div class="flex space-x-4">
                    <a href="#" class="flex items-center justify-center w-10 h-10 transition-colors bg-gray-800 rounded-system-lg hover:bg-system-blue">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="flex items-center justify-center w-10 h-10 transition-colors bg-gray-800 rounded-system-lg hover:bg-system-blue">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="flex items-center justify-center w-10 h-10 transition-colors bg-gray-800 rounded-system-lg hover:bg-system-blue">
                        <i class="fab fa-discord"></i>
                    </a>
                    <a href="#" class="flex items-center justify-center w-10 h-10 transition-colors bg-gray-800 rounded-system-lg hover:bg-system-blue">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-8 mt-8 text-center border-t border-gray-800">
            <p class="text-gray-400">
                &copy; 2025 虛擬寶物店商平台. All rights reserved.
            </p>
        </div>
    </div>
</footer>
