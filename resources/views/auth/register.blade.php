<x-guest-layout>
    <div class="min-h-screen pt-20 pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="glass-card register-form-container rounded-2xl p-8 shadow-2xl bg-white">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('加入我們') }}</h1>
                        <p class="text-gray-600">{{ __('創建帳戶開始您的虛擬寶物交易之旅') }}</p>
                    </div>

                    <!-- Validation Errors -->
                    <x-validation-errors class="mb-6" />

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- First Name & Last Name -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('姓氏') }}
                                </x-label>
                                <x-input
                                    id="last_name"
                                    class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    type="text"
                                    name="last_name"
                                    :value="old('last_name')"
                                    required
                                    autocomplete="family-name"
                                    placeholder="{{ __('您的姓氏') }}"
                                />
                            </div>
                            <div>
                                <x-label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">

                                    {{ __('名字') }}
                                </x-label>
                                <x-input
                                    id="first_name"
                                    class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    type="text"
                                    name="first_name"
                                    :value="old('first_name')"
                                    required
                                    autofocus
                                    autocomplete="given-name"
                                    placeholder="{{ __('您的名字') }}"
                                />
                            </div>

                        </div>

                        <!-- Email Field -->
                        <div>
                            <x-label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ __('電子郵件') }}
                            </x-label>
                            <x-input
                                id="email"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autocomplete="username"
                                placeholder="{{ __('請輸入有效的電子郵件') }}"
                            />
                        </div>

                        <!-- Username Field -->
                        <div>
                            <x-label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                {{ __('用戶名稱') }}
                            </x-label>
                            <x-input
                                id="username"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                type="text"
                                name="username"
                                :value="old('username')"
                                required
                                autocomplete="username"
                                placeholder="{{ __('請輸入用戶名稱') }}"
                            />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <x-label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                {{ __('密碼') }}
                            </x-label>
                            <div class="relative">
                                <x-input
                                    id="password"
                                    class="block w-full px-4 py-3 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="{{ __('至少 8 個字元') }}"
                                />
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password', 'eye-icon-password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="flex gap-1 mt-2">
                                <div id="strength1" class="h-1 flex-1 bg-gray-200 rounded transition-colors duration-300"></div>
                                <div id="strength2" class="h-1 flex-1 bg-gray-200 rounded transition-colors duration-300"></div>
                                <div id="strength3" class="h-1 flex-1 bg-gray-200 rounded transition-colors duration-300"></div>
                            </div>
                            <p id="strengthText" class="text-xs text-gray-500 mt-1">{{ __('密碼強度') }}</p>
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <x-label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                {{ __('確認密碼') }}
                            </x-label>
                            <div class="relative">
                                <x-input
                                    id="password_confirmation"
                                    class="block w-full px-4 py-3 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="{{ __('請重新輸入密碼') }}"
                                />
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-confirm')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <svg id="eye-icon-confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Terms and Privacy Policy -->
                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="flex items-start">
                                <x-checkbox
                                    name="terms"
                                    id="terms"
                                    required
                                    class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="terms" class="ml-2 text-sm text-gray-600">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-indigo-600 hover:text-indigo-500 font-medium">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-indigo-600 hover:text-indigo-500 font-medium">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </label>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-semibold transition duration-200 transform hover:scale-[1.02]"
                        >
                            {{ __('註冊') }}
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            {{ __('已經有帳號?') }}
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-semibold">
                                {{ __('立即登入') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 等待 DOM 完全載入後執行
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            window.togglePasswordVisibility = function(inputId, iconId) {
                const passwordInput = document.getElementById(inputId);
                const eyeIcon = document.getElementById(iconId);

                if (passwordInput && eyeIcon) {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                    }
                }
            };

            // Password strength checker
            const passwordInput = document.getElementById('password');

            if (passwordInput) {
                passwordInput.addEventListener('input', function(e) {
                    const password = e.target.value;
                    const strength1 = document.getElementById('strength1');
                    const strength2 = document.getElementById('strength2');
                    const strength3 = document.getElementById('strength3');
                    const strengthText = document.getElementById('strengthText');

                    if (!strength1 || !strength2 || !strength3 || !strengthText) {
                        console.error('Password strength elements not found');
                        return;
                    }

                    let strength = 0;

                    // 評估密碼強度
                    if (password.length >= 0) strength++;
                    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                    if (password.match(/[0-9]/) && password.match(/[^a-zA-Z0-9]/)) strength++;

                    // 重置所有進度條
                    [strength1, strength2, strength3].forEach(bar => {
                        bar.className = 'h-1 flex-1 bg-gray-200 rounded transition-colors duration-300';
                    });

                    // 根據強度更新
                    if (password.length === 0) {
                        strengthText.textContent = '{{ __("Password strength") }}';
                        strengthText.className = 'text-xs text-gray-500 mt-1';
                    } else if (strength === 1) {
                        strength1.classList.remove('bg-gray-200');
                        strength1.classList.add('bg-red-500');
                        strengthText.textContent = '{{ __("弱") }}';
                        strengthText.className = 'text-xs text-red-500 mt-1';
                    } else if (strength === 2) {
                        strength1.classList.remove('bg-gray-200');
                        strength1.classList.add('bg-yellow-500');
                        strength2.classList.remove('bg-gray-200');
                        strength2.classList.add('bg-yellow-500');
                        strengthText.textContent = '{{ __("中") }}';
                        strengthText.className = 'text-xs text-yellow-600 mt-1';
                    } else if (strength === 3) {
                        strength1.classList.remove('bg-gray-200');
                        strength1.classList.add('bg-green-500');
                        strength2.classList.remove('bg-gray-200');
                        strength2.classList.add('bg-green-500');
                        strength3.classList.remove('bg-gray-200');
                        strength3.classList.add('bg-green-500');
                        strengthText.textContent = '{{ __("強") }}';
                        strengthText.className = 'text-xs text-green-600 mt-1';
                    }
                });
            }
        });
    </script>
</x-guest-layout>
