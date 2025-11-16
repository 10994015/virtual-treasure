<header class="fixed top-0 w-full z-50 glass-nav">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-system-blue rounded-system flex items-center justify-center">
                    <i class="fas fa-gem text-white text-sm"></i>
                </div>
                <span class="text-lg font-semibold text-gray-900">虛擬寶物平台</span>
            </div>

            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-system-blue transition-colors font-semibold">
                    <i class="fas fa-home mr-1"></i>首頁
                </a>
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                    <i class="fas fa-store mr-1"></i>市場
                </a>
                <a href="{{ route('cart') }}" class="relative">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span
                        x-data
                        @cart-updated.window="$el.textContent = $event.detail[0].count"
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        {{ count(json_decode(request()->cookie('shopping_cart', '[]'), true)) }}
                    </span>
                    購物車
                </a>
                <a href="{{ route('ai-chat') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                    <i class="fas fa-robot mr-1"></i>AI 客服
                </a>
                <!-- 訊息圖示 -->
                @auth
                    <a
                        href="{{ route('messages') }}"
                        class="relative text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-comments mr-1"></i>訊息
                        @php
                            $unreadCount = \App\Models\Conversation::forUser(auth()->id())
                                ->where(function($q) {
                                    $q->where('buyer_id', auth()->id())
                                    ->where('buyer_unread_count', '>', 0)
                                    ->orWhere(function($q2) {
                                        $q2->where('seller_id', auth()->id())
                                            ->where('seller_unread_count', '>', 0);
                                    });
                                })->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                @endauth
                @if (Auth::check() && Auth::user()->is_seller)
                    <a href="{{ route('seller.dashboard') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                        <i class="fas fa-shop mr-1"></i>賣家中心
                    </a>
                @endif

            </div>

            @if (!Auth::user())
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-system-blue border border-system-blue rounded-system hover:bg-system-blue hover:text-white btn-system transition-colors">
                    登入
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-system-blue text-white rounded-system hover:bg-blue-600 btn-system transition-colors">
                    註冊
                </a>
            </div>
            @else
            <div class="relative group">
                <button class="flex items-center space-x-2 focus:outline-none">
                    <i class="fas fa-user-circle text-2xl text-gray-700"></i>
                    <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                    <i class="fas fa-caret-down text-gray-700"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-system shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-opacity">
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle mr-2"></i>會員中心
                    </a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-shopping-bag mr-2"></i>我的訂單
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>登出
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu hidden md:hidden fixed top-full left-0 w-full bg-white shadow-lg border-t">
            <div class="flex flex-col py-4">
                <a href="{{ route('home') }}" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center font-semibold">
                    <i class="fas fa-home mr-3"></i>首頁
                </a>
                <a href="market.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                    <i class="fas fa-store mr-3"></i>市場
                </a>
                <a href="cart.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                    <i class="fas fa-shopping-cart mr-3"></i>購物車
                    <span class="cart-count ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                </a>
                <a href="{{ route('ai-chat') }}" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                    <i class="fas fa-robot mr-3"></i>AI 客服
                </a>
                @if (Auth::user())
                    <a href="messaging.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                        <i class="fas fa-comments mr-3"></i>訊息
                    </a>
                @endif
                @if (!Auth::user())
                    <div class="border-t mt-4 pt-4 px-6">
                        <a href="login.html" class="w-full text-left py-2 text-gray-700 hover:text-system-blue transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>登入
                        </a>
                        <a href="register.html" class="w-full text-left py-2 text-system-blue hover:text-blue-600 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>註冊
                        </a>
                    </div>
                @else
                    <div class="border-t mt-4 pt-4 px-6">
                        <a href="{{ route('home') }}" class="w-full text-left py-2 text-gray-700 hover:text-system-blue transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>會員中心
                        </a>
                        <a href="{{ route('orders.index') }}" class="w-full text-left py-2 text-gray-700 hover:text-system-blue transition-colors">
                            <i class="fas fa-user-circle mr-2"></i>我的訂單
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left py-2 text-gray-700 hover:text-system-blue transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>登出
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </nav>
</header>
@push('scripts')
    <script>
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('header');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

@endpush
