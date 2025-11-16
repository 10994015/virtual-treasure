<div>
    <!-- Header -->
    <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">編輯商品</h1>
                    <p class="text-lg text-gray-600">更新您的商品資訊</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('seller.products.index') }}"
                    style="background: #3d4045;"
                       class="px-6 py-3 text-white inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>取消
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Form -->
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save" class="space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        基本資訊
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 商品名稱 -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                稀有度 <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="rarity"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-dollar-sign text-green-600 mr-3"></i>
                        價格與庫存
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 售價 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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

                        <!-- 原價 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                原價 (NT$) <span class="text-gray-400 text-xs">選填</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="original_price"
                                    step="0.01"
                                    min="1"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    placeholder="0">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">用於顯示折扣標籤</p>
                        </div>

                        <!-- 庫存數量 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-images text-purple-600 mr-3"></i>
                        商品圖片
                    </h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            上傳圖片 <span class="text-gray-400 text-xs">最多5張，目前 {{ $this->totalImagesCount }} 張</span>
                        </label>

                        <!-- 上傳區域 -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors {{ $this->totalImagesCount >= 5 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input
                                type="file"
                                wire:model="newImages"
                                multiple
                                accept="image/*"
                                class="hidden"
                                id="imageUpload"
                                {{ $this->totalImagesCount >= 5 ? 'disabled' : '' }}>

                            <label for="imageUpload" class="cursor-pointer {{ $this->totalImagesCount >= 5 ? 'pointer-events-none' : '' }}">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600 mb-2">
                                    {{ $this->totalImagesCount >= 5 ? '已達圖片上限' : '拖曳圖片到此處，或點擊選擇檔案' }}
                                </p>
                                <p class="text-sm text-gray-500">支援 JPG、PNG 格式，單張最大 5MB</p>
                            </label>
                        </div>

                        @error('newImages.*')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <!-- 現有圖片預覽 -->
                        @if(!empty($existingImages))
                            <div class="image-preview-container">
                                <div class="image-preview-header">
                                    <p class="image-count">現有圖片 ({{ count($existingImages) }} 張)</p>
                                </div>
                                <div class="image-preview-grid">
                                    @foreach($existingImages as $index => $image)
                                        @if(!in_array($image['id'], $imagesToDelete))
                                            <div class="image-preview-item">
                                                <div class="image-preview-wrapper">
                                                    <img
                                                        src="{{ Storage::url($image['image_path']) }}"
                                                        alt="商品圖片 {{ $index + 1 }}"
                                                        class="preview-image">

                                                    <button
                                                        type="button"
                                                        wire:click="removeExistingImage({{ $image['id'] }})"
                                                        class="image-remove-btn"
                                                        title="移除圖片">
                                                        <svg class="remove-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>

                                                    @if($image['is_primary'])
                                                        <div class="primary-badge">
                                                            <svg class="star-icon" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                            主圖
                                                        </div>
                                                    @endif

                                                    <div class="image-order-badge">
                                                        {{ $index + 1 }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- 新上傳圖片預覽 -->
                        @if(!empty($newImages))
                            <div class="image-preview-container">
                                <div class="image-preview-header">
                                    <p class="image-count">新增圖片 ({{ count($newImages) }} 張)</p>
                                </div>
                                <div class="image-preview-grid">
                                    @foreach($newImages as $index => $image)
                                        <div class="image-preview-item">
                                            <div class="image-preview-wrapper">
                                                <img
                                                    src="{{ $image->temporaryUrl() }}"
                                                    alt="新增圖片 {{ $index + 1 }}"
                                                    class="preview-image">

                                                <button
                                                    type="button"
                                                    wire:click="removeNewImage({{ $index }})"
                                                    class="image-remove-btn"
                                                    title="移除圖片">
                                                    <svg class="remove-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>

                                                <div class="image-order-badge">
                                                    新增
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div wire:loading wire:target="newImages" class="mt-4 text-center">
                            <div class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                上傳中...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-3"></i>
                        其他設定
                    </h2>

                    <div class="space-y-6">
                        <!-- 交易方式 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                交付方式
                            </label>
                            <select
                                wire:model="delivery_method"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                @foreach($deliveryMethods as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 交易條件 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                交易說明
                            </label>
                            <textarea
                                wire:model="delivery_instructions"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="描述交易條件、交付方式等資訊..."></textarea>
                        </div>

                        <!-- 關鍵字標籤 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                關鍵字標籤
                            </label>
                            <input
                                type="text"
                                wire:model="tags"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="輸入關鍵字，用逗號分隔 (例如: 武器,史詩,攻擊力)">
                            <p class="mt-1 text-xs text-gray-500">幫助買家更容易找到您的商品</p>
                        </div>

                        <!-- 選項 -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="is_negotiable"
                                    class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">允許議價</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="is_published"
                                    class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">上架販售</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        最後更新：{{ $product->updated_at->diffForHumans() }}
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('seller.products.index') }}"
                           class="px-6 py-3 text-center text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fas fa-times mr-2"></i>取消
                        </a>
                        <button
                            type="submit"
                            style="background-color: #3B82F6;"
                            class="px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                            <i class="fas fa-save mr-2"></i>儲存變更
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="save" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
            <p class="text-gray-600">更新中，請稍候...</p>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/image-preview.css') }}">
@endpush

@script
<script>
    $wire.on('notify', (event) => {
        const data = event[0];
        alert(data.message);
    });
</script>
@endscript
