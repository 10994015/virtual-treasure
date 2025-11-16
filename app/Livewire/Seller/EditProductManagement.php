<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use App\Models\ProductImage;
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
        'manual' => '手動交付',
        'both' => '兩者皆可',
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
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'category' => 'required',
            'game_type' => 'required',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary,mythic',
            'description' => 'required',
            'price' => 'required|numeric|min:1',
            'original_price' => 'nullable|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'newImages' => 'nullable|array',
            'newImages.*' => 'nullable|image|max:5120',
            'delivery_instructions' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_negotiable' => 'boolean',
            'delivery_method' => 'required|in:instant,manual,both',
            'is_published' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => '商品名稱為必填項目',
            'name.min' => '商品名稱至少需要 3 個字元',
            'name.max' => '商品名稱最多 255 個字元',
            'category.required' => '請選擇商品類別',
            'game_type.required' => '請選擇遊戲類型',
            'rarity.required' => '請選擇稀有度',
            'rarity.in' => '稀有度選項無效',
            'description.required' => '商品描述為必填項目',
            'description.min' => '商品描述至少需要 10 個字元',
            'price.required' => '售價為必填項目',
            'price.numeric' => '售價必須為數字',
            'price.min' => '售價至少為 1',
            'original_price.numeric' => '原價必須為數字',
            'original_price.min' => '原價至少為 1',
            'stock.required' => '庫存數量為必填項目',
            'stock.integer' => '庫存數量必須為整數',
            'stock.min' => '庫存數量不可為負數',
            'newImages.*.image' => '檔案必須為圖片格式',
            'newImages.*.max' => '圖片大小不可超過 5MB',
            'delivery_method.required' => '請選擇交付方式',
            'delivery_method.in' => '交付方式選項無效',
        ];
    }

    public function updatedNewImages()
    {
        $this->validate([
            'newImages' => 'nullable|array',
            'newImages.*' => 'image|max:5120',
        ]);

        // 檢查總數量限制
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
        // 將圖片 ID 加入待刪除列表
        if (!in_array($imageId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $imageId;
        }

        // 從顯示列表中移除
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

            // 刪除標記的圖片
            if (!empty($this->imagesToDelete)) {
                $imagesToDelete = ProductImage::whereIn('id', $this->imagesToDelete)->get();
                foreach ($imagesToDelete as $image) {
                    // 刪除實體檔案
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    // 刪除資料庫記錄
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

            // 確保有主圖
            $this->ensurePrimaryImage();

            DB::commit();

            session()->flash('success', '商品已成功更新');
            return redirect()->route('seller.products.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '更新失敗：' . $e->getMessage()
            ]);
        }
    }

    protected function ensurePrimaryImage()
    {
        // 檢查是否有主圖
        $hasPrimary = ProductImage::where('product_id', $this->product->id)
            ->where('is_primary', true)
            ->exists();

        // 如果沒有主圖，將第一張圖片設為主圖
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
