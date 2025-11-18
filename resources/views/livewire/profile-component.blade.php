<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-4">
                    <!-- 照片 -->
                    <div class="relative">
                        <img
                            src="{{ $currentPhotoUrl }}"
                            alt=""
                            class="object-cover w-20 h-20 border-4 border-white rounded-full shadow-lg">
                        @if(auth()->user()->is_seller)
                            <div class="absolute bottom-0 right-0 flex items-center justify-center w-6 h-6 text-xs text-white bg-yellow-500 border-2 border-white rounded-full">
                                <i class="fas fa-star"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="mb-1 text-3xl font-bold text-gray-900">{{ $last_name }} {{ $first_name }} </h1>
                        <p class="text-lg text-gray-600">{{ '@'.$username }}</p>
                        <div class="flex gap-2 mt-2">
                            @if(auth()->user()->is_admin)
                                <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                    <i class="mr-1 fas fa-shield-alt"></i>管理員
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-8 bg-white border-b">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="p-4 text-center rounded-lg bg-blue-50">
                    <div class="text-3xl font-bold text-blue-600">{{ $this->stats['orders'] }}</div>
                    <div class="text-sm text-gray-600">我的訂單</div>
                </div>
                @if(auth()->user()->is_seller)
                    <div class="p-4 text-center rounded-lg bg-green-50">
                        <div class="text-3xl font-bold text-green-600">{{ $this->stats['products'] }}</div>
                        <div class="text-sm text-gray-600">銷售商品</div>
                    </div>
                @endif
                <div class="p-4 text-center rounded-lg bg-purple-50">
                    <div class="text-3xl font-bold text-purple-600">{{ $this->stats['conversations'] }}</div>
                    <div class="text-sm text-gray-600">對話記錄</div>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <div class="text-sm font-semibold text-gray-900">會員時間</div>
                    <div class="text-xs text-gray-600">{{ $this->stats['member_since'] }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <div class="sticky bg-white border border-gray-200 rounded-lg shadow-sm top-4">
                        <nav class="p-2">
                            <button
                                wire:click="setActiveTab('profile')"
                                class="flex items-center w-full gap-3 px-4 py-3 text-left transition-colors rounded-lg {{ $activeTab === 'profile' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i class="fas fa-user"></i>
                                <span class="font-medium">個人資訊</span>
                            </button>
                            <button
                                wire:click="setActiveTab('password')"
                                class="flex items-center w-full gap-3 px-4 py-3 text-left transition-colors rounded-lg {{ $activeTab === 'password' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i class="fas fa-lock"></i>
                                <span class="font-medium">密碼設定</span>
                            </button>
                            <a
                                href="{{ route('orders.index') }}"
                                class="flex items-center w-full gap-3 px-4 py-3 text-left text-gray-600 transition-colors rounded-lg hover:bg-gray-50">
                                <i class="fas fa-shopping-bag"></i>
                                <span class="font-medium">我的訂單</span>
                            </a>
                            @if(auth()->user()->is_seller)
                                <a
                                    href="{{ route('seller.products.index') }}"
                                    class="flex items-center w-full gap-3 px-4 py-3 text-left text-gray-600 transition-colors rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-box"></i>
                                    <span class="font-medium">我的商品</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                </div>
                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <!-- Profile Tab -->
                    @if($activeTab === 'profile')
                        <div class="space-y-6">
                            <!-- 照片設定 -->
                            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <h2 class="mb-6 text-xl font-semibold text-gray-900">
                                    <i class="mr-2 text-blue-500 fas fa-image"></i>
                                    照片設定
                                </h2>

                                <div class="flex flex-col items-center gap-6 sm:flex-row">
                                    <div class="relative">
                                        <img
                                            src="{{ $currentPhotoUrl }}"
                                            alt=""
                                            class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full">
                                        @if($photo)
                                            <div class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 text-white bg-green-500 border-2 border-white rounded-full">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 space-y-4">
                                        <div>
                                            <input
                                                type="file"
                                                wire:model="photo"
                                                id="photoUpload"
                                                class="hidden"
                                                accept="image/*">

                                            <label
                                                for="photoUpload"
                                                class="inline-flex items-center px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg cursor-pointer hover:bg-blue-600">
                                                <i class="mr-2 fas fa-upload"></i>
                                                選擇新照片
                                            </label>

                                            @if(auth()->user()->profile_photo_path)
                                                <button
                                                    wire:click="deletePhoto"
                                                    wire:confirm="確定要刪除照片嗎？"
                                                    class="inline-flex items-center px-4 py-2 ml-2 text-red-600 transition-colors rounded-lg bg-red-50 hover:bg-red-100">
                                                    <i class="mr-2 fas fa-trash"></i>
                                                    刪除照片
                                                </button>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-500">
                                            <i class="mr-1 fas fa-info-circle"></i>
                                            支援 JPG、PNG 格式，檔案大小不超過 2MB
                                        </p>

                                        @error('photo')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror

                                        <div wire:loading wire:target="photo" class="text-sm text-blue-600">
                                            <i class="mr-2 fas fa-spinner fa-spin"></i>
                                            上傳中...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 基本資訊 -->
                            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <h2 class="mb-6 text-xl font-semibold text-gray-900">
                                    <i class="mr-2 text-blue-500 fas fa-user-edit"></i>
                                    基本資訊
                                </h2>

                                <form wire:submit="updateProfile" class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <!-- 使用者名稱 -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                使用者名稱 <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="username"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('username') border-red-500 @enderror"
                                                placeholder="username">
                                            @error('username')
                                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- 電子郵件 -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                電子郵件 <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="email"
                                                wire:model="email"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('email') border-red-500 @enderror"
                                                placeholder="email@example.com">
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <!-- 姓 -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                姓 <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="last_name"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('last_name') border-red-500 @enderror"
                                                placeholder="姓">
                                            @error('last_name')
                                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <!-- 名字 -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                                名字 <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="first_name"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('first_name') border-red-500 @enderror"
                                                placeholder="名字">
                                            @error('first_name')
                                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>


                                    </div>

                                    <div class="flex justify-end pt-4 border-t">
                                        <button
                                            type="submit"
                                            class="px-6 py-3 font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                            <i class="mr-2 fas fa-save"></i>
                                            儲存
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Password Tab -->
                    @if($activeTab === 'password')
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-6 text-xl font-semibold text-gray-900">
                                <i class="mr-2 text-blue-500 fas fa-lock"></i>
                                變更密碼
                            </h2>

                            <form wire:submit="updatePassword" class="space-y-4">
                                <!-- 目前密碼 -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        目前密碼 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="current_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('current_password') border-red-500 @enderror"
                                        placeholder="輸入目前密碼">
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 新密碼 -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        新密碼 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="new_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('new_password') border-red-500 @enderror"
                                        placeholder="輸入新密碼（至少 8 個字元）">
                                    @error('new_password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- 確認新密碼 -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        確認新密碼 <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="new_password_confirmation"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                        placeholder="再次輸入新密碼">
                                </div>

                                <div class="flex justify-end pt-4 border-t">
                                    <button
                                        type="submit"
                                        class="px-6 py-3 font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                                        <i class="mr-2 fas fa-key"></i>
                                        更新密碼
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="updateProfile,updatePassword,updatedPhoto"
         class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center p-6 bg-white rounded-lg">
            <div class="w-12 h-12 mb-4 border-b-2 border-blue-500 rounded-full animate-spin"></div>
            <p class="text-gray-600">處理中，請稍候...</p>
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

