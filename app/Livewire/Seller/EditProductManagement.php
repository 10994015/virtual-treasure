<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProductManagement extends Component
{
    use WithFileUploads;

    public Product $product;

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
    public $originalStock = 0; // 🔥 新增：記錄原始庫存

    // 🔥 新增：虛寶序號管理
    public $productCodes = [];
    public $existingCodes = [];
    public $newCodes = [];
    public $showCodeInput = true;

    // 圖片
    public $images = [];
    public $newImages = [];
    public $existingImages = [];
    public $imagesToDelete = [];

    // 其他設定
    public $delivery_instructions = '';
    public $tags = '';
    public $is_negotiable = false;
    public $delivery_method = 'manual';
    public $is_published = false;

    // 選項數據
    public $categories = [
        '武器' => '武器',
        '防具' => '防具',
        '消耗品' => '消耗品',
        '材料' => '材料',
        '皮膚' => '皮膚',
        '坐騎' => '坐騎',
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

    public function mount(Product $product)
    {
        // 權限檢查
        if (!auth()->user()->is_admin && $product->user_id !== auth()->id()) {
            abort(403, '您沒有權限編輯此商品');
        }

        $this->product = $product;

        // 載入商品資料
        $this->name = $product->name;
        $this->category = $product->category;
        $this->game_type = $product->game_type;
        $this->rarity = $product->rarity;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->original_price = $product->original_price;
        $this->stock = $product->stock;
        $this->originalStock = $product->stock; // 🔥 記錄原始庫存
        $this->delivery_instructions = $product->delivery_instructions;
        $this->is_negotiable = $product->is_negotiable;
        $this->delivery_method = $product->delivery_method;
        $this->is_published = $product->is_published;

        // 載入標籤
        if ($product->specifications && isset($product->specifications['tags'])) {
            $this->tags = is_array($product->specifications['tags'])
                ? implode(',', $product->specifications['tags'])
                : $product->specifications['tags'];
        }

        // 載入現有圖片
        $this->existingImages = $product->images()->orderBy('order')->get()->toArray();

        // 🔥 載入現有序號（只載入可用的序號）
        $this->existingCodes = $product->availableCodes()->get()->map(function($code) {
            return [
                'id' => $code->id,
                'code' => $code->code,
            ];
        })->toArray();

        // 🔥 如果庫存 > 0，顯示序號輸入區
        $this->showCodeInput = $this->stock > 0;
    }

    // 🔥 監聽庫存變化
    // 🔥 監聽庫存變化
    public function updatedStock($value)
    {
        $stock = (int)$value;

        if ($stock <= 0) {
            $this->showCodeInput = false;
            $this->newCodes = [];
            return;
        }

        $this->showCodeInput = true;

        // 計算需要的新序號數量
        $existingCount = count($this->existingCodes);
        $neededCount = $stock - $existingCount;

        if ($neededCount > 0) {
            // 需要新增序號
            $currentNewCount = count($this->newCodes);
            if ($neededCount > $currentNewCount) {
                // 增加序號輸入框
                for ($i = $currentNewCount; $i < $neededCount; $i++) {
                    $this->newCodes[] = '';
                }
            } elseif ($neededCount < $currentNewCount) {
                // 減少序號輸入框
                $this->newCodes = array_slice($this->newCodes, 0, $neededCount);
            }
        } else {
            // 不需要新序號（庫存減少到等於或小於現有序號數量）
            $this->newCodes = [];

            // 如果庫存小於現有序號，顯示警告
            if ($neededCount < 0) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => '警告：庫存數量少於現有序號數量，某些序號將無法使用'
                ]);
            }
        }
    }

    // 🔥 添加新序號
    public function addNewCode()
    {
        $existingCount = count($this->existingCodes);
        $newCount = count($this->newCodes);
        $totalCount = $existingCount + $newCount;

        if ($totalCount < $this->stock) {
            $this->newCodes[] = '';

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

    // 🔥 移除新序號
    public function removeNewCode($index)
    {
        if (count($this->newCodes) > 0) {
            array_splice($this->newCodes, $index, 1);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '已移除序號'
            ]);
        }
    }

    // 🔥 計算屬性
    public function getHasEmptyNewCodesProperty()
    {
        foreach ($this->newCodes as $code) {
            if (empty(trim($code))) {
                return true;
            }
        }
        return false;
    }

    public function getFilledNewCodesCountProperty()
    {
        $count = 0;
        foreach ($this->newCodes as $code) {
            if (!empty(trim($code))) {
                $count++;
            }
        }
        return $count;
    }

    public function getTotalCodesCountProperty()
    {
        return count($this->existingCodes) + $this->filledNewCodesCount;
    }

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
            'newImages' => 'nullable|array',
            'newImages.*' => 'nullable|image|max:5120',
            'delivery_instructions' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_negotiable' => 'boolean',
            'delivery_method' => 'required|in:instant,manual,both',
            'is_published' => 'boolean',
        ];

        // 🔥 如果需要新序號，添加序號驗證
        if ($this->showCodeInput && !empty($this->newCodes)) {
            $rules['newCodes'] = 'array';
            $rules['newCodes.*'] = [
                'required',
                'string',
                'min:3',
                'max:255',
                'distinct', // 確保陣列內不重複
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
            'newCodes.*.required' => '新序號不能為空',
            'newCodes.*.string' => '新序號必須為文字',
            'newCodes.*.distinct' => '新序號重複，每個序號必須唯一',
            'newCodes.*.min' => '新序號至少需要 3 個字元',
            'newCodes.*.max' => '新序號最多 255 個字元',

            'newImages.*.image' => '檔案必須為圖片格式',
            'newImages.*.max' => '圖片大小不可超過 5MB',
            'delivery_method.required' => '請選擇交付方式',
        ];
    }

    public function updatedNewImages()
    {
        $this->validate([
            'newImages' => 'nullable|array',
            'newImages.*' => 'image|max:5120',
        ]);

        $totalImages = count($this->existingImages) - count($this->imagesToDelete) + count($this->newImages);
        if ($totalImages > 5) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '最多只能上傳 5 張圖片'
            ]);
            $this->newImages = [];
            return;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '圖片已新增'
        ]);
    }

    public function removeExistingImage($imageId)
    {
        if (!in_array($imageId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $imageId;
        }

        $this->existingImages = array_filter($this->existingImages, function($img) use ($imageId) {
            return $img['id'] !== $imageId;
        });

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '圖片已標記為刪除'
        ]);
    }

    public function removeNewImage($index)
    {
        array_splice($this->newImages, $index, 1);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '圖片已移除'
        ]);
    }

    public function save()
    {
        // 🔥 驗證序號數量
        if ($this->showCodeInput && $this->stock > 0) {
            $totalCodes = $this->totalCodesCount;

            if ($totalCodes !== (int)$this->stock) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "序號數量不符！需要 {$this->stock} 個，目前只有 {$totalCodes} 個"
                ]);
                return;
            }

            // 檢查新序號是否有空值
            if ($this->hasEmptyNewCodes) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => '發現空白序號，請填寫完整'
                ]);
                return;
            }

            // 🔥 檢查新序號是否與資料庫中的序號重複
            $newCodesArray = array_filter(array_map('trim', $this->newCodes));
            if (!empty($newCodesArray)) {
                $duplicateCodes = ProductCode::whereIn('code', $newCodesArray)->pluck('code')->toArray();

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

            // 更新商品基本資料
            $this->product->update([
                'name' => $this->name,
                'category' => $this->category,
                'game_type' => $this->game_type,
                'rarity' => $this->rarity,
                'description' => $this->description,
                'specifications' => $this->tags ? ['tags' => explode(',', $this->tags)] : null,
                'price' => $this->price,
                'original_price' => $this->original_price ?: null,
                'stock' => $this->stock,
                'is_negotiable' => $this->is_negotiable,
                'delivery_method' => $this->delivery_method,
                'delivery_instructions' => $this->delivery_instructions,
                'is_published' => $this->is_published,
                'status' => $this->is_published ? 'active' : 'inactive',
                'published_at' => $this->is_published && !$this->product->published_at ? now() : $this->product->published_at,
            ]);

            // 🔥 保存新序號（加強版）
            if ($this->showCodeInput && !empty($this->newCodes)) {
                $savedCount = 0;
                foreach ($this->newCodes as $code) {
                    $trimmedCode = trim($code);
                    if (!empty($trimmedCode)) {
                        // 再次檢查是否已存在
                        $exists = ProductCode::where('code', $trimmedCode)->exists();
                        if (!$exists) {
                            ProductCode::create([
                                'product_id' => $this->product->id,
                                'code' => $trimmedCode,
                                'status' => 'available',
                            ]);
                            $savedCount++;
                        }
                    }
                }

                if ($savedCount > 0) {
                    \Illuminate\Support\Facades\Log::info('新增虛寶序號', [
                        'product_id' => $this->product->id,
                        'count' => $savedCount
                    ]);
                }
            }

            // 刪除標記的圖片
            if (!empty($this->imagesToDelete)) {
                $imagesToDelete = ProductImage::whereIn('id', $this->imagesToDelete)->get();
                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
            }

            // 上傳新圖片
            if (!empty($this->newImages)) {
                $currentMaxOrder = ProductImage::where('product_id', $this->product->id)->max('order') ?? -1;

                foreach ($this->newImages as $index => $image) {
                    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('products/' . $this->product->id, $filename, 'public');

                    ProductImage::create([
                        'product_id' => $this->product->id,
                        'image_path' => $path,
                        'thumbnail_path' => null,
                        'order' => $currentMaxOrder + $index + 1,
                        'is_primary' => false,
                        'alt_text' => $this->product->name,
                    ]);
                }
            }

            $this->ensurePrimaryImage();

            DB::commit();

            session()->flash('success', '商品已成功更新');
            return redirect()->route('seller.products.index');

        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('商品更新失敗', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '更新失敗：' . $e->getMessage()
            ]);
        }
    }

    protected function ensurePrimaryImage()
    {
        $hasPrimary = ProductImage::where('product_id', $this->product->id)
            ->where('is_primary', true)
            ->exists();

        if (!$hasPrimary) {
            $firstImage = ProductImage::where('product_id', $this->product->id)
                ->orderBy('order')
                ->first();

            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    public function getTotalImagesCountProperty()
    {
        return count($this->existingImages) - count($this->imagesToDelete) + count($this->newImages);
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.edit-product-management');
    }
}
