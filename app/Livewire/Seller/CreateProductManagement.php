<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateProductManagement extends Component
{
    use WithFileUploads;

    // 基本資訊
    public $name = '';
    public $category = '';
    public $game_type = '';
    public $rarity = 'common';
    public $description = '';

    // 價格與庫存
    public $price = '';
    public $original_price = '';
    public $stock = 1;

    // 🔥 新增：虛寶序號
    public $productCodes = ['']; // 序號陣列，預設一個空值
    public $showCodeInput = true; // 是否顯示序號輸入區

    // 圖片
    public $images = [];
    public $newImages = [];
    public $imagePreviewUrls = [];

    // 其他設定
    public $delivery_instructions = '';
    public $tags = '';
    public $is_negotiable = false;
    public $delivery_method = 'manual';
    public $auto_publish = true;

    // 選項數據保持不變...
    public $categories = [
        '武器' => '武器',
        '防具' => '防具',
        '消耗品' => '消耗品',
        '材料' => '材料',
        '皮膚' => '皮膚',
        '坐騎' => '坐騎',
        '點數卡' => '點數卡',
        '其他' => '其他',
    ];

    public $games = [
        'World of Warcraft' => 'World of Warcraft',
        'League of Legends' => 'League of Legends',
        'Dota 2' => 'Dota 2',
        'CS:GO' => 'CS:GO',
        'Minecraft' => 'Minecraft',
        'Genshin Impact' => '原神',
        '其他' => '其他',
    ];

    public $rarities = [
        'common' => '普通',
        'uncommon' => '優秀',
        'rare' => '精良',
        'epic' => '史詩',
        'legendary' => '傳說',
        'mythic' => '神話',
    ];

    public $deliveryMethods = [
        'instant' => '立即交付',
    ];

    // 🔥 監聽庫存變化，動態調整序號輸入框數量
    public function updatedStock($value)
    {
        $stock = (int)$value;

        if ($stock <= 0) {
            // 無限庫存，不需要序號
            $this->showCodeInput = false;
            $this->productCodes = [];
            return;
        }

        $this->showCodeInput = true;
        $currentCount = count($this->productCodes);

        if ($stock > $currentCount) {
            // 增加序號輸入框
            for ($i = $currentCount; $i < $stock; $i++) {
                $this->productCodes[] = '';
            }
        } elseif ($stock < $currentCount) {
            // 減少序號輸入框
            $this->productCodes = array_slice($this->productCodes, 0, $stock);
        }
    }

    // 🔥 添加序號
    public function addCode()
    {
        if (count($this->productCodes) < $this->stock) {
            $this->productCodes[] = '';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已新增序號輸入框'
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '序號數量已達上限'
            ]);
        }
    }


    // 🔥 移除序號
    public function removeCode($index)
    {
        if (count($this->productCodes) > 0) {
            array_splice($this->productCodes, $index, 1);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已移除序號'
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '至少需要保留一個序號'
            ]);
        }
    }


    // 驗證規則
    protected function rules()
    {
        $rules = [
            'name' => 'required|max:255',
            'category' => 'required',
            'game_type' => 'required',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary,mythic',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|max:5120',
            'delivery_instructions' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_negotiable' => 'boolean',
            'delivery_method' => 'required|in:instant,manual,both',
            'auto_publish' => 'boolean',
        ];

        // 🔥 如果需要序號，添加序號驗證
        if ($this->showCodeInput && $this->stock > 0) {
            $rules['productCodes'] = 'required|array';
            $rules['productCodes.*'] = [
                'required',
                'string',
                'min:3',
                'max:255',
                'distinct', // 🔥 確保陣列內不重複
                function ($attribute, $value, $fail) {
                    // 🔥 檢查資料庫中是否已存在
                    if (ProductCode::where('code', trim($value))->exists()) {
                        $fail("序號 {$value} 已存在於系統中");
                    }
                },
            ];
        }

        return $rules;
    }


    protected function messages()
    {
        return [
            'name.required' => '商品名稱為必填項目',
            'name.max' => '商品名稱最多 255 個字元',
            'category.required' => '請選擇商品類別',
            'game_type.required' => '請選擇遊戲類型',
            'rarity.required' => '請選擇稀有度',
            'description.required' => '商品描述為必填項目',
            'price.required' => '售價為必填項目',
            'price.numeric' => '售價必須為數字',
            'price.min' => '售價至少為 0',
            'stock.required' => '庫存數量為必填項目',
            'stock.integer' => '庫存數量必須為整數',
            'stock.min' => '庫存數量不可為負數',

            // 🔥 序號驗證訊息
            'productCodes.required' => '請輸入虛寶序號',
            'productCodes.array' => '序號格式錯誤',
            'productCodes.*.required' => '序號 :position 不能為空',
            'productCodes.*.string' => '序號 :position 必須為文字',
            'productCodes.*.distinct' => '序號 :position 重複，每個序號必須唯一',
            'productCodes.*.min' => '序號 :position 至少需要 3 個字元',
            'productCodes.*.max' => '序號 :position 最多 255 個字元',

            'images.max' => '最多只能上傳 5 張圖片',
            'delivery_method.required' => '請選擇交付方式',
        ];
    }

    // 圖片相關方法保持不變...
    public function updatedNewImages()
    {
        $this->validate([
            'newImages' => 'nullable|array',
            'newImages.*' => 'image|max:5120',
        ]);

        $totalImages = count($this->images) + count($this->newImages);
        if ($totalImages > 5) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '最多只能上傳 5 張圖片'
            ]);
            $this->newImages = [];
            return;
        }

        foreach ($this->newImages as $newImage) {
            try {
                $tempPath = $newImage->store('temp-products', 'public');
                $this->images[] = $newImage;
                $this->imagePreviewUrls[] = $tempPath;
            } catch (\Exception $e) {
                Log::error('圖片上傳失敗', ['error' => $e->getMessage()]);
            }
        }

        $this->newImages = [];
    }

    public function removeImage($index)
    {
        try {
            if (isset($this->imagePreviewUrls[$index])) {
                Storage::disk('public')->delete($this->imagePreviewUrls[$index]);
                array_splice($this->imagePreviewUrls, $index, 1);
            }
            array_splice($this->images, $index, 1);
        } catch (\Exception $e) {
            Log::error('移除圖片失敗', ['error' => $e->getMessage()]);
        }
    }

    public function saveAsDraft()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            $product = $this->createProduct('draft', false);
            DB::commit();

            session()->flash('success', '商品已儲存為草稿');
            return redirect()->route('seller.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('儲存草稿失敗', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '儲存失敗：' . $e->getMessage()
            ]);
        }
    }
    // 🔥 新增：檢查是否有空序號
    public function getHasEmptyCodesProperty()
    {
        foreach ($this->productCodes as $code) {
            if (empty(trim($code))) {
                return true;
            }
        }
        return false;
    }

    // 🔥 新增：獲取已填寫的序號數量
    public function getFilledCodesCountProperty()
    {
        $count = 0;
        foreach ($this->productCodes as $code) {
            if (!empty(trim($code))) {
                $count++;
            }
        }
        return $count;
    }
    public function save()
    {
        // 🔥 先檢查序號數量是否正確
        if ($this->showCodeInput && $this->stock > 0) {
            if (count($this->productCodes) !== (int)$this->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "序號數量不符！需要 {$this->stock} 個，目前只有 " . count($this->productCodes) . " 個"
                ]);
                return;
            }

            // 檢查是否有空序號
            $emptyCount = 0;
            foreach ($this->productCodes as $code) {
                if (empty(trim($code))) {
                    $emptyCount++;
                }
            }

            if ($emptyCount > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "發現 {$emptyCount} 個空白序號，請填寫完整"
                ]);
                return;
            }

            // 🔥 檢查序號是否與資料庫中的序號重複
            $codesArray = array_filter(array_map('trim', $this->productCodes));
            if (!empty($codesArray)) {
                $duplicateCodes = ProductCode::whereIn('code', $codesArray)->pluck('code')->toArray();

                if (!empty($duplicateCodes)) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => '以下序號已存在：' . implode(', ', $duplicateCodes)
                    ]);
                    return;
                }
            }
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $status = $this->auto_publish ? 'active' : 'draft';
            $isPublished = $this->auto_publish;

            $product = $this->createProduct($status, $isPublished);

            DB::commit();

            $message = $this->auto_publish ? '商品已成功上架' : '商品已儲存';
            session()->flash('success', $message);
            return redirect()->route('seller.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('儲存商品失敗', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '儲存失敗：' . $e->getMessage()
            ]);
        }
    }

    protected function createProduct($status, $isPublished)
    {
        // 建立商品
        $product = Product::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name) . '-' . Str::random(6),
            'user_id' => auth()->id(),
            'category' => $this->category,
            'game_type' => $this->game_type,
            'rarity' => $this->rarity,
            'description' => $this->description,
            'specifications' => $this->tags ? ['tags' => explode(',', $this->tags)] : null,
            'price' => $this->price,
            'original_price' => $this->original_price ?: null,
            'stock' => $this->stock,
            'status' => $status,
            'is_published' => $isPublished,
            'is_negotiable' => $this->is_negotiable,
            'delivery_method' => $this->delivery_method,
            'delivery_instructions' => $this->delivery_instructions,
            'published_at' => $isPublished ? now() : null,
            'verification_status' => 'pending',
        ]);

        Log::info('商品已建立', ['product_id' => $product->id]);

        // 🔥 儲存虛寶序號
        if ($this->showCodeInput && !empty($this->productCodes)) {
            $this->saveProductCodes($product);
        }

        // 上傳圖片
        if (!empty($this->images)) {
            $this->uploadImages($product);
        }

        $this->cleanupTempFiles();

        return $product;
    }

    // 🔥 新增：儲存虛寶序號
    // 🔥 新增：儲存虛寶序號（加強版）
    protected function saveProductCodes($product)
    {
        $savedCount = 0;
        $failedCodes = [];

        foreach ($this->productCodes as $code) {
            $trimmedCode = trim($code);
            if (!empty($trimmedCode)) {
                try {
                    // 🔥 再次檢查是否已存在（雙重保險）
                    $exists = ProductCode::where('code', $trimmedCode)->exists();
                    if (!$exists) {
                        ProductCode::create([
                            'product_id' => $product->id,
                            'code' => $trimmedCode,
                            'status' => 'available',
                        ]);
                        $savedCount++;

                        Log::info('虛寶序號已保存', [
                            'product_id' => $product->id,
                            'code' => substr($trimmedCode, 0, 5) . '***'
                        ]);
                    } else {
                        $failedCodes[] = $trimmedCode;
                    }
                } catch (\Exception $e) {
                    Log::error('保存虛寶序號失敗', [
                        'product_id' => $product->id,
                        'code' => substr($trimmedCode, 0, 5) . '***',
                        'error' => $e->getMessage()
                    ]);
                    $failedCodes[] = $trimmedCode;
                }
            }
        }

        // 🔥 如果有序號保存失敗，拋出異常
        if (!empty($failedCodes)) {
            throw new \Exception('以下序號保存失敗或已存在：' . implode(', ', $failedCodes));
        }

        Log::info('所有虛寶序號已保存', [
            'product_id' => $product->id,
            'count' => $savedCount
        ]);
    }
    public function checkCodeDuplicate($index)
    {
        if (isset($this->productCodes[$index])) {
            $code = trim($this->productCodes[$index]);

            if (!empty($code) && strlen($code) >= 3) {
                $exists = ProductCode::where('code', $code)->exists();

                if ($exists) {
                    $this->addError("productCodes.{$index}", "此序號已存在於系統中");
                }
            }
        }
    }
    protected function uploadImages($product)
    {
        foreach ($this->images as $index => $image) {
            try {
                $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products/' . $product->id, $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'thumbnail_path' => null,
                    'order' => $index,
                    'is_primary' => $index === 0,
                    'alt_text' => $product->name,
                ]);
            } catch (\Exception $e) {
                Log::error('上傳產品圖片失敗', ['error' => $e->getMessage()]);
            }
        }
    }

    protected function cleanupTempFiles()
    {
        foreach ($this->imagePreviewUrls as $path) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Exception $e) {
                Log::warning('清理臨時文件失敗', ['error' => $e->getMessage()]);
            }
        }
        $this->imagePreviewUrls = [];
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.create-product-management');
    }
}
