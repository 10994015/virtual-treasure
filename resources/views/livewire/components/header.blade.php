<header class="fixed top-0 z-50 w-full glass-nav">
    <nav class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-8 h-8 bg-system-blue rounded-system">
                    <i class="text-sm text-white fas fa-gem"></i>
                </div>
                <span class="text-lg font-semibold text-gray-900">虛擬寶物平台</span>
            </div>

            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="font-semibold text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-home"></i>首頁
                </a>
                <a href="{{ route('products.index') }}" class="text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-store"></i>市場
                </a>
                <a href="{{ route('cart') }}" class="relative">
                    <i class="text-xl fas fa-shopping-cart"></i>
                    <span
                        x-data
                        @cart-updated.window="$el.textContent = $event.detail[0].count"
                        class="absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-2 -right-2">
                        {{ count(json_decode(request()->cookie('shopping_cart', '[]'), true)) }}
                    </span>
                    購物車
                </a>
                <a href="{{ route('ai-chat') }}" class="text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-robot"></i>AI 客服
                </a>
                <!-- 訊息圖示 -->
                @auth
                    <a
                        href="{{ route('messages') }}"
                        class="relative text-gray-600 transition-colors hover:text-blue-600">
                        <i class="mr-1 fas fa-comments"></i>訊息
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
                            <span class="absolute flex items-center justify-center w-5 h-5 text-xs text-white bg-red-500 rounded-full -top-1 -right-1">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                @endauth
            </div>

            @if (!Auth::user())
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="px-4 py-2 transition-colors border text-system-blue border-system-blue rounded-system hover:bg-system-blue hover:text-white btn-system">
                    登入
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-white transition-colors bg-system-blue rounded-system hover:bg-blue-600 btn-system">
                    註冊
                </a>
            </div>
            @else
            <div class="relative group">
                <button class="flex items-center space-x-2 focus:outline-none">
                    @if(Auth::user()->profile_photo_url && !str_contains(Auth::user()->profile_photo_url, 'ui-avatars.com'))
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->last_name }}" class="object-cover w-8 h-8 rounded-full">
                    @else
                    <i class="text-2xl text-gray-700 fas fa-user-circle"></i>
                    @endif
                    <span class="font-semibold text-gray-700">{{ Auth::user()->last_name . Auth::user()->first_name }}</span>
                    <i class="text-gray-700 fas fa-caret-down"></i>
                </button>
                <div class="absolute right-0 invisible w-48 mt-2 transition-opacity bg-white border border-gray-200 shadow-lg opacity-0 rounded-system group-hover:opacity-100 group-hover:visible">
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="mr-2 fas fa-user-circle"></i>會員中心
                    </a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="mr-2 fas fa-shopping-bag"></i>我的訂單
                    </a>
                    @if (Auth::user()->is_seller)
                        <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="mr-2 fas fa-shop"></i>賣家中心
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100">
                            <i class="mr-2 fas fa-sign-out-alt"></i>登出
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Mobile Menu -->
        <div class="fixed left-0 hidden w-full bg-white border-t shadow-lg mobile-menu md:hidden top-full">
            <div class="flex flex-col py-4">
                <a href="{{ route('home') }}" class="flex items-center px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-home"></i>首頁
                </a>
                <a href="market.html" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-store"></i>市場
                </a>
                <a href="cart.html" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-shopping-cart"></i>購物車
                    <span class="px-2 py-1 ml-auto text-xs text-white bg-red-500 rounded-full cart-count">0</span>
                </a>
                <a href="{{ route('ai-chat') }}" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-robot"></i>AI 客服
                </a>
                @if (Auth::user())
                    <a href="messaging.html" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                        <i class="mr-3 fas fa-comments"></i>訊息
                    </a>
                @endif
                @if (!Auth::user())
                    <div class="px-6 pt-4 mt-4 border-t">
                        <a href="login.html" class="w-full py-2 text-left text-gray-700 transition-colors hover:text-system-blue">
                            <i class="mr-2 fas fa-sign-in-alt"></i>登入
                        </a>
                        <a href="register.html" class="w-full py-2 text-left transition-colors text-system-blue hover:text-blue-600">
                            <i class="mr-2 fas fa-user-plus"></i>註冊
                        </a>
                    </div>
                @else
                    <div class="px-6 pt-4 mt-4 border-t">
                        <a href="{{ route('home') }}" class="w-full py-2 text-left text-gray-700 transition-colors hover:text-system-blue">
                            <i class="mr-2 fas fa-user-circle"></i>會員中心
                        </a>
                        <a href="{{ route('orders.index') }}" class="w-full py-2 text-left text-gray-700 transition-colors hover:text-system-blue">
                            <i class="mr-2 fas fa-user-circle"></i>我的訂單
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full py-2 text-left text-gray-700 transition-colors hover:text-system-blue">
                                <i class="mr-2 fas fa-sign-out-alt"></i>登出
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
