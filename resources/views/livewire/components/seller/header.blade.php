<header class="fixed top-0 z-50 w-full bg-white glass-nav">
    <nav class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center space-x-2">
                <button class="md:hidden mobile-menu-btn">
                    <i class="text-gray-700 fas fa-bars"></i>
                </button>
                <div class="flex items-center justify-center w-8 h-8 bg-system-blue rounded-system">
                    <i class="text-sm text-white fas fa-store"></i>
                </div>
                <span class="text-lg font-semibold text-gray-900">賣家中心</span>
            </div>

            <div class="flex items-center space-x-8">
                <a href="{{ route('seller.dashboard') }}" class="font-semibold text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-chart-line"></i>儀表板
                </a>
                <a href="{{ route('seller.products.index') }}" class="text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-box"></i>商品管理
                </a>
                <a href="{{ route('seller.orders.index') }}" class="text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-shopping-cart"></i>訂單管理
                </a>
                <a href="{{ route('home') }}" class="text-gray-700 transition-colors hover:text-system-blue">
                    <i class="mr-1 fas fa-person"></i>顧客模式
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="login.html" class="hidden px-4 py-2 transition-colors border sm:block text-system-blue border-system-blue rounded-system hover:bg-system-blue hover:text-white btn-system">
                    登出
                </a>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="fixed left-0 hidden w-full bg-white border-t shadow-lg mobile-menu md:hidden top-full">
            <div class="flex flex-col py-4">
                <a href="seller-dashboard.html" class="flex items-center px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-chart-line"></i>儀表板
                </a>
                <a href="seller-items.html" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-box"></i>商品管理
                </a>
                <a href="seller-orders.html" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-shopping-cart"></i>訂單管理
                </a>
                <a href="{{ route('home') }}" class="flex items-center px-6 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-system-blue">
                    <i class="mr-3 fas fa-person"></i>顧客模式
                </a>
                <div class="px-6 pt-4 mt-4 border-t">
                    <a href="login.html" class="w-full py-2 text-left text-gray-700 transition-colors hover:text-system-blue">
                        <i class="mr-2 fas fa-sign-out-alt"></i>登出
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
