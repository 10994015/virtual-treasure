<div>
    <!-- Hero Section -->
    <section class="py-24 bg-gradient-to-br from-blue-50 to-indigo-100 sm:py-32">
        <div class="max-w-6xl px-4 mx-auto text-center sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <h1 class="mb-6 text-4xl font-bold text-gray-900 sm:text-6xl lg:text-7xl">
                    虛擬寶物
                    <span class="text-system-blue">交易平台</span>
                </h1>
                <p class="max-w-2xl mx-auto mb-8 text-lg leading-relaxed text-gray-600 sm:text-xl">
                    使用 AI 智能技術，讓您的遊戲交易更加安全、透明、高效
                </p>
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-4 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system shadow-system">
                        <i class="mr-2 fas fa-shopping-bag"></i>
                        探索市場
                    </a>
                    <button onclick="openChat()" class="inline-flex items-center px-8 py-4 font-semibold transition-all border-2 border-system-blue text-system-blue rounded-system-lg hover:bg-system-blue hover:text-white btn-system">
                        <i class="mr-2 fas fa-robot"></i>
                        AI 客服
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-card-white">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">核心功能</h2>
                <p class="max-w-2xl mx-auto text-lg text-gray-600">我們提供最先進的AI技術和用戶友好的介面</p>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-robot"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">AI 智能客服</h3>
                    <p class="leading-relaxed text-gray-600">24小時在線 AI 客服，解答您的所有問題，提供即時支援</p>
                </div>

                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-shield-alt"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">安全交易</h3>
                    <p class="leading-relaxed text-gray-600">多重安全措施，保障您的交易安全，保護個人隱私</p>
                </div>

                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-chart-line"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">價格預測</h3>
                    <p class="leading-relaxed text-gray-600">AI 分析市場趨勢，提供準確價格建議，優化交易決策</p>
                </div>

                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-crosshairs"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">風險評估</h3>
                    <p class="leading-relaxed text-gray-600">智能風險評估，識別潛在交易風險，保障交易安全</p>
                </div>

                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-gamepad"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">多平台支援</h3>
                    <p class="leading-relaxed text-gray-600">支援多個遊戲平台和虛擬物品交易，一站式解決方案</p>
                </div>

                <div class="p-8 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-bell"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">個人化推薦</h3>
                    <p class="leading-relaxed text-gray-600">根據您的偏好提供個性化商品推薦，發現心儀物品</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Market Preview Section -->
    <section class="py-20 bg-system-gray">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">熱門商品</h2>
                <p class="text-lg text-gray-600">探索最受歡迎的虛擬物品，開始您的遊戲冒險</p>
            </div>
            <div class="grid grid-cols-1 gap-6 mb-12 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Sample items for demo -->
                @foreach($topProducts as $product)
                <div class="p-6 transition-all bg-card-white rounded-system-lg shadow-system hover:shadow-system-lg">
                    <div class="flex items-center justify-center w-full h-48 mb-4 overflow-hidden rounded-system-lg">
                        @if ($product->product_image)
                            <img src="{{ $product->product_image }}" alt="{{ $product->product_name }}" class="object-cover w-full h-full">
                        @else
                            <div class="flex items-center justify-center w-full h-full bg-gray-200">
                                <i class="text-gray-400 fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="mb-2 font-semibold text-gray-900">{{ $product->product_name }}</h3>
                    <p class="mb-3 text-sm text-gray-600">{{ $product->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-system-blue">NT$ {{ number_format($product->price, 0) }}</span>
                        <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center px-4 py-2 font-semibold text-white transition-all bg-system-blue rounded-system hover:bg-blue-600 btn-system">查看商品</a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-4 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system shadow-system">
                    <i class="mr-2 fas fa-store"></i>
                    探索完整市場
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-system-blue">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                <div class="text-center">
                    <div class="mb-2 text-4xl font-bold text-white lg:text-5xl">10K+</div>
                    <div class="text-sm text-blue-100 lg:text-base">活躍用戶</div>
                </div>
                <div class="text-center">
                    <div class="mb-2 text-4xl font-bold text-white lg:text-5xl">50K+</div>
                    <div class="text-sm text-blue-100 lg:text-base">成功交易</div>
                </div>
                <div class="text-center">
                    <div class="mb-2 text-4xl font-bold text-white lg:text-5xl">99.9%</div>
                    <div class="text-sm text-blue-100 lg:text-base">安全率</div>
                </div>
                <div class="text-center">
                    <div class="mb-2 text-4xl font-bold text-white lg:text-5xl">24/7</div>
                    <div class="text-sm text-blue-100 lg:text-base">客服支援</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20 bg-card-white">
        <div class="max-w-4xl px-4 mx-auto text-center sm:px-6 lg:px-8">
            <h2 class="mb-8 text-3xl font-bold text-gray-900 sm:text-4xl">關於我們</h2>
            <div class="space-y-6 text-lg leading-relaxed text-gray-600">
                <p class="max-w-3xl mx-auto">
                    我們是專注於虛擬寶物交易的創新平台，整合先進的 AI 技術，
                    為遊戲玩家提供安全、透明、高效的交易體驗。
                </p>
                <p class="max-w-3xl mx-auto">
                    我們的使命是讓虛擬物品交易像現實交易一樣簡單和可靠。
                    通過 AI 驅動的價格預測、智能風險評估和個人化推薦，
                    我們正在重塑遊戲經濟的未來。
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 bg-system-gray">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">聯絡我們</h2>
                <p class="text-lg text-gray-600">有任何問題？我們隨時為您提供幫助</p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Contact Info Cards -->
                <div class="p-8 text-center bg-card-white rounded-system-lg shadow-system">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-envelope"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">電子郵件</h3>
                    <p class="text-gray-600">support@cyim.com</p>
                </div>

                <div class="p-8 text-center bg-card-white rounded-system-lg shadow-system">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-system-blue rounded-system-lg">
                        <i class="text-xl text-white fas fa-clock"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900">服務時間</h3>
                    <p class="text-gray-600">24/7 在線客服</p>
                </div>

                <div class="p-8 text-center text-white bg-system-blue rounded-system-lg">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-white rounded-system-lg">
                        <i class="text-xl fas fa-robot text-system-blue"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold">AI 客服</h3>
                    <p class="mb-4 opacity-90">即時智能回應</p>
                    <a href="{{ route('ai-chat') }}" class="inline-flex items-center px-6 py-2 text-sm font-semibold transition-all bg-white text-system-blue rounded-system hover:bg-gray-50 btn-system">
                        <i class="mr-2 fas fa-comments"></i>開始對話
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="py-20 bg-card-white">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">探索我們的平台</h2>
                <p class="text-lg text-gray-600">從這裡開始您的遊戲物品交易之旅</p>
            </div>

            <div class="grid grid-cols-1 gap-8 mb-12 md:grid-cols-3">
                <div class="p-8 text-center transition-all bg-system-gray rounded-system-lg hover:bg-gray-100">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-2xl text-white fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">商品市場</h3>
                    <p class="mb-6 text-gray-600">瀏覽數千種遊戲虛擬物品，找到您心儀的寶物</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system">
                        <i class="mr-2 fas fa-arrow-right"></i>立即瀏覽
                    </a>
                </div>

                <div class="p-8 text-center transition-all bg-system-gray rounded-system-lg hover:bg-gray-100">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-2xl text-white fas fa-question-circle"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">幫助中心</h3>
                    <p class="mb-6 text-gray-600">AI智能客服和完整幫助文檔，隨時為您解答</p>
                    <a href="{{ route('ai-chat') }}" class="inline-flex items-center px-6 py-3 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system">
                        <i class="mr-2 fas fa-question-circle"></i>獲取幫助
                    </a>
                </div>

                <div class="p-8 text-center transition-all bg-system-gray rounded-system-lg hover:bg-gray-100">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-system-blue rounded-system-lg">
                        <i class="text-2xl text-white fas fa-robot"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-semibold text-gray-900">AI 客服</h3>
                    <p class="mb-6 text-gray-600">24小時在線AI助手，提供即時問題解答</p>
                    <button onclick="openChat()" class="inline-flex items-center px-6 py-3 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system">
                        <i class="mr-2 fas fa-comments"></i>開始對話
                    </button>
                </div>
            </div>

            <div class="text-center">
                <p class="mb-6 text-gray-600">準備開始您的遊戲物品交易嗎？</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-4 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system shadow-system">
                    <i class="mr-2 fas fa-rocket"></i>立即開始交易
                </a>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-card-white rounded-system-lg shadow-system-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex-1 text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-system-blue rounded-system-lg">
                            <i class="text-2xl text-white fas fa-user"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">歡迎回來</h3>
                        <p class="mt-1 text-gray-600">登入您的帳號</p>
                    </div>
                    <button onclick="closeLoginModal()" class="-mt-4 -mr-4 text-gray-400 hover:text-gray-600">
                        <i class="text-xl fas fa-times"></i>
                    </button>
                </div>

                <!-- Social Login Options -->
                <div class="mb-6 space-y-3">
                    <button class="flex items-center justify-center w-full px-4 py-3 transition-colors border border-gray-300 rounded-system-lg hover:bg-gray-50">
                        <i class="mr-3 text-red-500 fab fa-google"></i>
                        <span class="text-gray-700">使用 Google 登入</span>
                    </button>
                    <button class="flex items-center justify-center w-full px-4 py-3 transition-colors border border-gray-300 rounded-system-lg hover:bg-gray-50">
                        <i class="mr-3 text-blue-600 fab fa-facebook"></i>
                        <span class="text-gray-700">使用 Facebook 登入</span>
                    </button>
                </div>

                <!-- Divider -->
                <div class="flex items-center mb-6">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="px-4 text-sm text-gray-500">或</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-6">
                    <div>
                        <label for="loginEmail" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-envelope text-system-blue"></i>電子郵件
                        </label>
                        <input type="email" id="loginEmail" name="email" required
                                class="w-full px-4 py-3 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                placeholder="輸入您的電子郵件">
                    </div>
                    <div>
                        <label for="loginPassword" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-lock text-system-blue"></i>密碼
                        </label>
                        <div class="relative">
                            <input type="password" id="loginPassword" name="password" required
                                    class="w-full px-4 py-3 pr-12 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                    placeholder="輸入您的密碼">
                            <button type="button" onclick="togglePassword('loginPassword')"
                                    class="absolute text-gray-400 transform -translate-y-1/2 right-3 top-1/2 hover:text-gray-600">
                                <i class="fas fa-eye" id="loginPasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" class="border-gray-300 rounded text-system-blue focus:ring-system-blue">
                            <span class="ml-2 text-sm text-gray-600">記住我</span>
                        </label>
                        <a href="#" class="text-sm text-system-blue hover:underline">忘記密碼？</a>
                    </div>

                    <!-- Error Message -->
                    <div id="loginError" class="hidden px-4 py-3 text-sm text-red-600 border border-red-200 bg-red-50 rounded-system">
                        <i class="mr-2 fas fa-exclamation-triangle"></i>
                        <span id="loginErrorText"></span>
                    </div>

                    <button type="submit" id="loginSubmitBtn"
                            class="w-full px-6 py-3 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="mr-2 fas fa-sign-in-alt"></i>
                        <span id="loginBtnText">登入</span>
                    </button>
                </form>

                <!-- Switch to Register -->
                <p class="mt-6 text-center text-gray-600">
                    還沒有帳號？
                    <button type="button" onclick="switchToRegister()" class="ml-1 font-medium text-system-blue hover:underline">
                        立即註冊
                    </button>
                </p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-card-white rounded-system-lg shadow-system-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex-1 text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-system-blue rounded-system-lg">
                            <i class="text-2xl text-white fas fa-user-plus"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">加入我們</h3>
                        <p class="mt-1 text-gray-600">創建您的帳號</p>
                    </div>
                    <button onclick="closeRegisterModal()" class="-mt-4 -mr-4 text-gray-400 hover:text-gray-600">
                        <i class="text-xl fas fa-times"></i>
                    </button>
                </div>

                <!-- Social Register Options -->
                <div class="mb-6 space-y-3">
                    <button class="flex items-center justify-center w-full px-4 py-3 transition-colors border border-gray-300 rounded-system-lg hover:bg-gray-50">
                        <i class="mr-3 text-red-500 fab fa-google"></i>
                        <span class="text-gray-700">使用 Google 註冊</span>
                    </button>
                    <button class="flex items-center justify-center w-full px-4 py-3 transition-colors border border-gray-300 rounded-system-lg hover:bg-gray-50">
                        <i class="mr-3 text-blue-600 fab fa-facebook"></i>
                        <span class="text-gray-700">使用 Facebook 註冊</span>
                    </button>
                </div>

                <!-- Divider -->
                <div class="flex items-center mb-6">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="px-4 text-sm text-gray-500">或</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                <!-- Register Form -->
                <form id="registerForm" class="space-y-6">
                    <div>
                        <label for="registerUsername" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-user text-system-blue"></i>用戶名稱
                        </label>
                        <input type="text" id="registerUsername" name="username" required
                                class="w-full px-4 py-3 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                placeholder="選擇您的用戶名稱">
                    </div>

                    <div>
                        <label for="registerEmail" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-envelope text-system-blue"></i>電子郵件
                        </label>
                        <input type="email" id="registerEmail" name="email" required
                                class="w-full px-4 py-3 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                placeholder="輸入您的電子郵件">
                    </div>

                    <div>
                        <label for="registerPassword" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-lock text-system-blue"></i>密碼
                        </label>
                        <div class="relative">
                            <input type="password" id="registerPassword" name="password" required
                                    class="w-full px-4 py-3 pr-12 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                    placeholder="設定密碼（至少6個字符）">
                            <button type="button" onclick="togglePassword('registerPassword')"
                                    class="absolute text-gray-400 transform -translate-y-1/2 right-3 top-1/2 hover:text-gray-600">
                                <i class="fas fa-eye" id="registerPasswordIcon"></i>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex space-x-1">
                                <div id="strength1" class="w-1/4 h-1 bg-gray-200 rounded-full"></div>
                                <div id="strength2" class="w-1/4 h-1 bg-gray-200 rounded-full"></div>
                                <div id="strength3" class="w-1/4 h-1 bg-gray-200 rounded-full"></div>
                                <div id="strength4" class="w-1/4 h-1 bg-gray-200 rounded-full"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" id="passwordStrength">密碼強度</p>
                        </div>
                    </div>

                    <div>
                        <label for="registerConfirmPassword" class="block mb-2 text-sm font-medium text-gray-700">
                            <i class="mr-2 fas fa-lock text-system-blue"></i>確認密碼
                        </label>
                        <input type="password" id="registerConfirmPassword" name="confirmPassword" required
                                class="w-full px-4 py-3 transition-all border-2 border-gray-200 rounded-system-lg focus:border-system-blue focus:ring-0 form-system"
                                placeholder="再次輸入密碼">
                    </div>

                    <!-- Terms and Privacy -->
                    <div>
                        <label class="flex items-start">
                            <input type="checkbox" id="termsAgree" required class="mt-1 border-gray-300 rounded text-system-blue focus:ring-system-blue">
                            <span class="ml-2 text-sm text-gray-600">
                                我同意
                                <a href="#" class="text-system-blue hover:underline">服務條款</a>
                                和
                                <a href="#" class="text-system-blue hover:underline">隱私政策</a>
                            </span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="newsletterSubscribe" class="border-gray-300 rounded text-system-blue focus:ring-system-blue">
                            <span class="ml-2 text-sm text-gray-600">訂閱電子報，接收最新優惠資訊</span>
                        </label>
                    </div>

                    <!-- Error Message -->
                    <div id="registerError" class="hidden px-4 py-3 text-sm text-red-600 border border-red-200 bg-red-50 rounded-system">
                        <i class="mr-2 fas fa-exclamation-triangle"></i>
                        <span id="registerErrorText"></span>
                    </div>

                    <!-- Success Message -->
                    <div id="registerSuccess" class="hidden px-4 py-3 text-sm text-green-600 border border-green-200 bg-green-50 rounded-system">
                        <i class="mr-2 fas fa-check-circle"></i>
                        <span id="registerSuccessText"></span>
                    </div>

                    <button type="submit" id="registerSubmitBtn"
                            class="w-full px-6 py-3 font-semibold text-white transition-all bg-system-blue rounded-system-lg hover:bg-blue-600 btn-system disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="mr-2 fas fa-user-plus"></i>
                        <span id="registerBtnText">創建帳號</span>
                    </button>
                </form>

                <!-- Switch to Login -->
                <p class="mt-6 text-center text-gray-600">
                    已有帳號？
                    <button type="button" onclick="switchToLogin()" class="ml-1 font-medium text-system-blue hover:underline">
                        立即登入
                    </button>
                </p>
            </div>
        </div>
    </div>





</div>

