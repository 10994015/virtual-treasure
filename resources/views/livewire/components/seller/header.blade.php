<header class="fixed top-0 w-full z-50 glass-nav bg-white">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-2">
                <button class="md:hidden mobile-menu-btn">
                    <i class="fas fa-bars text-gray-700"></i>
                </button>
                <div class="w-8 h-8 bg-system-blue rounded-system flex items-center justify-center">
                    <i class="fas fa-store text-white text-sm"></i>
                </div>
                <span class="text-lg font-semibold text-gray-900">賣家中心</span>
            </div>

            <div class="flex items-center space-x-8">
                <a href="{{ route('seller.dashboard') }}" class="text-gray-700 hover:text-system-blue transition-colors font-semibold">
                    <i class="fas fa-chart-line mr-1"></i>儀表板
                </a>
                <a href="{{ route('seller.products.index') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                    <i class="fas fa-box mr-1"></i>商品管理
                </a>
                <a href="{{ route('seller.orders.index') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                    <i class="fas fa-shopping-cart mr-1"></i>訂單管理
                </a>
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-system-blue transition-colors">
                    <i class="fas fa-person mr-1"></i>顧客模式
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="login.html" class="hidden sm:block px-4 py-2 text-system-blue border border-system-blue rounded-system hover:bg-system-blue hover:text-white btn-system transition-colors">
                    登出
                </a>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu hidden md:hidden fixed top-full left-0 w-full bg-white shadow-lg border-t">
            <div class="flex flex-col py-4">
                <a href="seller-dashboard.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center font-semibold">
                    <i class="fas fa-chart-line mr-3"></i>儀表板
                </a>
                <a href="seller-items.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                    <i class="fas fa-box mr-3"></i>商品管理
                </a>
                <a href="seller-orders.html" class="px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-system-blue transition-colors flex items-center">
                    <i class="fas fa-shopping-cart mr-3"></i>訂單管理
                </a>
                <div class="border-t mt-4 pt-4 px-6">
                    <a href="login.html" class="w-full text-left py-2 text-gray-700 hover:text-system-blue transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>登出
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
