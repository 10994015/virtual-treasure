<div>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">編輯商品</h1>
                    <p class="text-lg text-gray-600">更新您的商品資訊</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('seller.products.index') }}"
                    style="background: #3d4045;"
                       class="inline-flex items-center px-6 py-3 text-white">
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

                        <!-- 原價 -->
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
                                wire:model.live="stock"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('stock') border-red-500 @enderror"
                                placeholder="1">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @if($showCodeInput && $stock > 0)
                    <div class="p-6 bg-white border-2 border-yellow-300 rounded-lg shadow-lg bg-gradient-to-br from-yellow-50 to-orange-50">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 bg-yellow-500 rounded-full">
                                    <i class="text-xl text-white fas fa-key"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        虛寶序號管理
                                    </h2>
                                    <p class="text-sm text-gray-600">
                                        需要總數 <span class="font-bold text-yellow-600">{{ $stock }}</span> 個
                                        <span class="ml-2">
                                            (已有 <span class="font-bold text-green-600">{{ count($existingCodes) }}</span> 個，
                                            已填 <span class="font-bold text-blue-600">{{ $this->filledNewCodesCount }}</span> 個新序號，
                                            還需 <span class="font-bold {{ max(0, $stock - count($existingCodes) - $this->filledNewCodesCount) === 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ max(0, $stock - count($existingCodes) - $this->filledNewCodesCount) }}
                                            </span> 個)
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 🔥 庫存不足警告 -->
                        @if($stock < count($existingCodes))
                            <div class="flex items-start gap-3 p-4 mb-6 text-red-700 bg-red-100 border-2 border-red-300 rounded-lg">
                                <i class="mt-1 text-xl fas fa-exclamation-triangle"></i>
                                <div class="flex-1">
                                    <p class="font-semibold">⚠️ 庫存數量警告</p>
                                    <p class="text-sm">
                                        庫存數量 ({{ $stock }}) 少於已有序號數量 ({{ count($existingCodes) }})。
                                        建議將庫存調整為至少 {{ count($existingCodes) }} 個。
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- 現有序號 -->
                        @if(!empty($existingCodes))
                            <div class="mb-6">
                                <h3 class="mb-3 text-lg font-semibold text-gray-900">
                                    <i class="mr-2 text-green-600 fas fa-check-circle"></i>
                                    現有序號 ({{ count($existingCodes) }} 個)
                                </h3>
                                <div class="p-4 space-y-2 overflow-y-auto border border-green-200 rounded-lg bg-green-50 max-h-60">
                                    @foreach($existingCodes as $index => $codeData)
                                        <div class="flex items-center gap-3 p-3 bg-white border border-green-200 rounded-lg">
                                            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm font-bold text-white bg-green-500 rounded-full">
                                                {{ $index + 1 }}
                                            </div>
                                            <code class="flex-1 font-mono text-base font-semibold text-gray-900">
                                                {{ $codeData['code'] }}
                                            </code>
                                            <div class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                                已儲存
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- 新增序號輸入 -->
                        @if($stock > count($existingCodes))
                            <div class="mb-6">
                                <h3 class="mb-3 text-lg font-semibold text-gray-900">
                                    <i class="mr-2 text-blue-600 fas fa-plus-circle"></i>
                                    新增序號 (還需 {{ max(0, $stock - count($existingCodes)) }} 個)
                                </h3>

                                <div class="p-4 space-y-3 overflow-y-auto bg-white border border-gray-200 rounded-lg max-h-96">
                                    @if(empty($newCodes))
                                        <div class="py-8 text-center text-gray-400">
                                            <i class="mb-3 text-4xl fas fa-key"></i>
                                            <p>尚未新增序號</p>
                                            <p class="text-sm">請點擊下方「新增序號」按鈕</p>
                                        </div>
                                    @else
                                        @foreach($newCodes as $index => $code)
                                            <div class="flex items-start gap-3 p-3 transition-all border border-gray-200 rounded-lg bg-gray-50 hover:shadow-md">
                                                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 font-bold text-white bg-blue-500 rounded-lg">
                                                    {{ count($existingCodes) + $index + 1 }}
                                                </div>

                                                <div class="flex-1">
                                                    <input
                                                        type="text"
                                                        wire:model.blur="newCodes.{{ $index }}"
                                                        wire:keydown.enter.prevent="addNewCode"
                                                        class="w-full px-4 py-2 text-base font-mono transition-all border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('newCodes.' . $index) border-red-500 @enderror"
                                                        placeholder="輸入新的虛寶序號 (例如: XXXX-XXXX-XXXX-XXXX)">

                                                    @error('newCodes.' . $index)
                                                        <p class="flex items-center gap-1 mt-1 text-sm text-red-500">
                                                            <i class="fas fa-exclamation-circle"></i>
                                                            {{ $message }}
                                                        </p>
                                                    @enderror

                                                    {{-- 🔥 顯示提示 --}}
                                                    @if(!empty($code) && strlen(trim($code)) >= 3)
                                                        @php
                                                            $isDuplicate = \App\Models\ProductCode::where('code', trim($code))->exists();
                                                        @endphp
                                                        @if($isDuplicate)
                                                            <p class="flex items-center gap-1 mt-1 text-sm text-orange-600">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                此序號已存在於系統中
                                                            </p>
                                                        @endif
                                                    @endif
                                                </div>

                                                <button
                                                    type="button"
                                                    wire:click="removeNewCode({{ $index }})"
                                                    class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-red-600 transition-all rounded-lg bg-red-50 hover:bg-red-100 hover:text-red-700"
                                                    title="移除此序號">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- 新增序號按鈕 -->
                                @php
                                    $needMore = max(0, $stock - count($existingCodes) - count($newCodes));
                                @endphp

                                @if($needMore > 0)
                                    <button
                                        type="button"
                                        wire:click="addNewCode"
                                        class="w-full px-6 py-3 mt-4 font-semibold text-blue-600 transition-all bg-blue-100 border-2 border-blue-300 border-dashed rounded-lg hover:bg-blue-200 hover:border-blue-400">
                                        <i class="mr-2 fas fa-plus-circle"></i>
                                        新增序號 (還需 {{ $needMore }} 個)
                                    </button>
                                @else
                                    @if($this->hasEmptyNewCodes)
                                        <div class="flex items-center justify-center gap-2 p-3 mt-4 text-red-700 bg-red-100 border-2 border-red-300 rounded-lg">
                                            <i class="text-xl fas fa-exclamation-triangle"></i>
                                            <span class="font-semibold">發現空白序號，請填寫完整！</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-2 p-3 mt-4 text-green-700 bg-green-100 border-2 border-green-300 rounded-lg">
                                            <i class="text-xl fas fa-check-circle"></i>
                                            <span class="font-semibold">序號數量已足夠！</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <!-- 統計資訊 -->
                        <div class="grid grid-cols-4 gap-4 mb-6">
                            <div class="p-4 text-center bg-white border-2 border-gray-200 rounded-lg">
                                <div class="text-2xl font-bold text-gray-900">{{ $stock }}</div>
                                <div class="text-sm text-gray-600">需要總數</div>
                            </div>
                            <div class="p-4 text-center bg-white border-2 border-green-300 rounded-lg bg-green-50">
                                <div class="text-2xl font-bold text-green-600">{{ count($existingCodes) }}</div>
                                <div class="text-sm text-gray-600">已有序號</div>
                            </div>
                            <div class="p-4 text-center bg-white border-2 border-blue-300 rounded-lg bg-blue-50">
                                <div class="text-2xl font-bold text-blue-600">{{ $this->filledNewCodesCount }}</div>
                                <div class="text-sm text-gray-600">已填新序號</div>
                            </div>
                            <div class="p-4 text-center bg-white border-2 rounded-lg {{ max(0, $stock - $this->totalCodesCount) === 0 ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }}">
                                <div class="text-2xl font-bold {{ max(0, $stock - $this->totalCodesCount) === 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ max(0, $stock - $this->totalCodesCount) }}
                                </div>
                                <div class="text-sm text-gray-600">還需要</div>
                            </div>
                        </div>

                        <!-- 注意事項 -->
                        <div class="p-4 border-2 border-yellow-300 rounded-lg bg-yellow-50">
                            <div class="flex items-start gap-3">
                                <i class="mt-1 text-xl text-yellow-600 fas fa-exclamation-triangle"></i>
                                <div class="flex-1 text-sm text-yellow-800">
                                    <p class="mb-2 font-semibold text-yellow-900">⚠️ 重要注意事項：</p>
                                    <ul class="space-y-1 list-disc list-inside">
                                        <li>已儲存的序號<strong>無法刪除或修改</strong></li>
                                        <li>只能<strong>新增序號</strong>來增加庫存</li>
                                        <li>新增的序號必須<strong>唯一且不可重複</strong></li>
                                        <li>序號總數必須<strong>符合庫存數量</strong></li>
                                        <li>調整庫存時會自動調整需要的序號數量</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Images -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                        <i class="mr-3 text-purple-600 fas fa-images"></i>
                        商品圖片
                    </h2>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            上傳圖片 <span class="text-xs text-gray-400">最多5張，目前 {{ $this->totalImagesCount }} 張</span>
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
                                <i class="mb-3 text-4xl text-gray-400 fas fa-cloud-upload-alt"></i>
                                <p class="mb-2 text-gray-600">
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
                                    wire:model="is_published"
                                    class="text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">上架販售</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col items-center justify-between gap-4 pt-8 border-t sm:flex-row">
                    <div class="text-sm text-gray-500">
                        <i class="mr-1 fas fa-info-circle"></i>
                        最後更新：{{ $product->updated_at->diffForHumans() }}
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('seller.products.index') }}"
                           class="px-6 py-3 text-center text-gray-600 transition-colors hover:text-gray-800">
                            <i class="mr-2 fas fa-times"></i>取消
                        </a>
                        <button
                            type="submit"
                            style="background-color: #3B82F6;"
                            class="px-8 py-3 font-semibold text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                            <i class="mr-2 fas fa-save"></i>儲存變更
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="save" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center p-6 bg-white rounded-lg">
            <div class="w-12 h-12 mb-4 border-b-2 border-blue-500 rounded-full animate-spin"></div>
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
