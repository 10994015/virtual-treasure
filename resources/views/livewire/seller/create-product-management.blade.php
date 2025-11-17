<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">新增商品</h1>
                    <p class="text-lg text-gray-600">填寫商品資訊，讓您的商品更容易被買家發現</p>
                </div>
                <div class="flex gap-3">
                    <button
                        wire:click="saveAsDraft"
                        type="button"
                        class="px-6 py-3 text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="mr-2 fas fa-save"></i>儲存草稿
                    </button>
                    <a href="{{ route('seller.products.index') }}"
                        style="background: #3d4045;"
                        class="inline-flex items-center px-6 py-3 text-white hover:text-gray-800">
                        <i class="mr-2 fas fa-times"></i>取消
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Form -->
    <section class="py-12 bg-white">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <form wire:submit="save" class="space-y-8">
                <!-- Basic Information -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-blue-500 fas fa-info-circle"></i>
                        基本資訊
                    </h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- 商品名稱 -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                商品名稱 <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('name') border-red-500 @enderror"
                                placeholder="輸入商品名稱">
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 商品類別 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                商品類別 <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="category"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('category') border-red-500 @enderror">
                                <option value="">選擇類別</option>
                                @foreach($categories as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 遊戲類型 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                遊戲類型 <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="game_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('game_type') border-red-500 @enderror">
                                <option value="">選擇遊戲</option>
                                @foreach($games as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('game_type')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 稀有度 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                稀有度 <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="rarity"
                                class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                @foreach($rarities as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('rarity')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 商品描述 -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                商品描述 <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="description"
                                rows="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('description') border-red-500 @enderror"
                                placeholder="詳細描述您的商品特點、用途和注意事項..."></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing and Inventory -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-green-600 fas fa-dollar-sign"></i>
                        價格與庫存
                    </h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- 售價 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                售價 (NT$) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="price"
                                    step="0.01"
                                    min="1"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('price') border-red-500 @enderror"
                                    placeholder="0">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 原價（選填） -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                原價 (NT$) <span class="text-xs text-gray-400">選填</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="original_price"
                                    step="0.01"
                                    min="1"
                                    class="w-full px-4 py-2 pr-16 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                    placeholder="0">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">用於顯示折扣標籤</p>
                        </div>

                        <!-- 庫存數量 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                庫存數量 <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="stock"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('stock') border-red-500 @enderror"
                                placeholder="1">
                            <p class="mt-1 text-xs text-gray-500">設定為 0 表示無限庫存</p>
                            @error('stock')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-purple-600 fas fa-images"></i>
                        商品圖片
                    </h2>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            上傳圖片 <span class="text-xs text-gray-400">最多5張，目前已選 {{ count($images) }} 張</span>
                        </label>

                        <!-- 上傳區域 -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors {{ count($images) >= 5 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input
                                type="file"
                                wire:model="newImages"
                                multiple
                                accept="image/*"
                                class="hidden"
                                id="imageUpload"
                                {{ count($images) >= 5 ? 'disabled' : '' }}>

                            <label for="imageUpload" class="cursor-pointer {{ count($images) >= 5 ? 'pointer-events-none' : '' }}">
                                <i class="mb-3 text-4xl text-gray-400 fas fa-cloud-upload-alt"></i>
                                <p class="mb-2 text-gray-600">
                                    {{ count($images) >= 5 ? '已達圖片上限' : '拖曳圖片到此處，或點擊選擇檔案' }}
                                </p>
                                <p class="text-sm text-gray-500">支援 JPG、PNG 格式，單張最大 5MB</p>
                            </label>
                        </div>

                        @error('newImages.*')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @error('images.max')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- 圖片預覽 -->
                        @if(!empty($images))
                            <div class="image-preview-container">
                                <div class="image-preview-header">
                                    <p class="image-count">已選擇 {{ count($images) }} 張圖片</p>
                                    <p class="image-hint">點擊圖片右上角 ✕ 移除</p>
                                </div>
                                <div class="image-preview-grid">
                                    @foreach($images as $index => $image)
                                        <div class="image-preview-item">
                                            <div class="image-preview-wrapper">
                                                <!-- 圖片 -->
                                                <img
                                                    src="{{ $image->temporaryUrl() }}"
                                                    alt="預覽圖 {{ $index + 1 }}"
                                                    class="preview-image">

                                                <!-- 刪除按鈕 -->
                                                <button
                                                    type="button"
                                                    wire:click="removeImage({{ $index }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeImage"
                                                    class="image-remove-btn"
                                                    title="移除圖片">
                                                    <svg class="remove-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>

                                                <!-- 主圖標籤 -->
                                                @if($index === 0)
                                                    <div class="primary-badge">
                                                        <svg class="star-icon" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        主圖
                                                    </div>
                                                @endif

                                                <!-- 圖片順序 -->
                                                <div class="image-order-badge">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="image-info-box">
                                    <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="info-text">第一張圖片將作為商品主圖顯示。點擊圖片右上角的紅色 ✕ 按鈕可移除圖片。</p>
                                </div>
                            </div>
                        @endif

                        <div wire:loading wire:target="newImages" class="mt-4 text-center">
                            <div class="inline-flex items-center px-4 py-2 text-blue-600 rounded-lg bg-blue-50">
                                <i class="mr-2 fas fa-spinner fa-spin"></i>
                                上傳中...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-gray-600 fas fa-cog"></i>
                        其他設定
                    </h2>

                    <div class="space-y-6">
                        <!-- 交易方式 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                交付方式
                            </label>
                            <select
                                wire:model="delivery_method"
                                class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                @foreach($deliveryMethods as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 交易條件 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                交易說明
                            </label>
                            <textarea
                                wire:model="delivery_instructions"
                                rows="3"
                                class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="描述交易條件、交付方式等資訊..."></textarea>
                        </div>

                        <!-- 關鍵字標籤 -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                關鍵字標籤
                            </label>
                            <input
                                type="text"
                                wire:model="tags"
                                class="w-full px-4 py-2 transition-all border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="輸入關鍵字，用逗號分隔 (例如: 武器,史詩,攻擊力)">
                            <p class="mt-1 text-xs text-gray-500">幫助買家更容易找到您的商品</p>
                        </div>

                        <!-- 選項 -->
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="is_negotiable"
                                    class="text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">允許議價</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="auto_publish"
                                    class="text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">立即上架</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col justify-end gap-4 pt-8 border-t sm:flex-row">
                    <a href="{{ route('seller.products.index') }}"
                        style="background: #3d4045"
                       class="px-6 py-3 text-center text-white transition-colors">
                        <i class="mr-2 fas fa-times"></i>取消
                    </a>
                    <button
                        type="button"
                        wire:click="saveAsDraft"
                        class="px-6 py-3 text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="mr-2 fas fa-save"></i>儲存草稿
                    </button>
                    <button
                        type="submit"
                        style="background-color: #3B82F6;"
                        class="px-8 py-3 font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                        <i class="mr-2 fas fa-plus"></i>{{ $auto_publish ? '上架商品' : '儲存商品' }}
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="save,saveAsDraft" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
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
        // 使用你的通知系統
        alert(data.message);
    });
</script>
@endscript

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/image-preview.css') }}">
@endpush
