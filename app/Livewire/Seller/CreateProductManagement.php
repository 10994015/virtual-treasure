<?php

namespace App\Livewire\Seller;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    // 圖片
    public $images = [];
    public $newImages = []; // 新增：暫存新選擇的圖片

    // 其他設定
    public $delivery_instructions = '';
    public $tags = '';
    public $is_negotiable = false;
    public $delivery_method = 'manual';
    public $auto_publish = true;

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

    // 驗證規則
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
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|max:5120', // 5MB
            'delivery_instructions' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_negotiable' => 'boolean',
            'delivery_method' => 'required|in:instant,manual,both',
            'auto_publish' => 'boolean',
        ];
    }

    // 自訂錯誤訊息
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
            'images.array' => '圖片格式錯誤',
            'images.max' => '最多只能上傳 5 張圖片',
            'images.*.image' => '檔案必須為圖片格式',
            'images.*.max' => '圖片大小不可超過 5MB',
            'delivery_method.required' => '請選擇交付方式',
            'delivery_method.in' => '交付方式選項無效',
        ];
    }

    // 新增：處理新圖片上傳
    public function updatedNewImages()
    {
        // 驗證新圖片
        $this->validate([
            'newImages' => 'nullable|array',
            'newImages.*' => 'image|max:5120',
        ]);

        // 檢查總數量限制
        $totalImages = count($this->images) + count($this->newImages);
        if ($totalImages > 5) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '最多只能上傳 5 張圖片'
            ]);
            $this->newImages = [];
            return;
        }

        // 將新圖片追加到現有圖片陣列
        foreach ($this->newImages as $newImage) {
            $this->images[] = $newImage;
        }

        // 清空暫存
        $this->newImages = [];

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '圖片已新增'
        ]);
    }

    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => '圖片已移除'
        ]);
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
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '儲存失敗：' . $e->getMessage()
            ]);
        }
    }

    public function save()
    {
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

        // 上傳圖片
        if (!empty($this->images)) {
            $this->uploadImages($product);
        }

        return $product;
    }

    protected function uploadImages($product)
    {
        foreach ($this->images as $index => $image) {
            // 生成唯一檔名
            $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();

            // 儲存圖片
            $path = $image->storeAs('products/' . $product->id, $filename, 'public');

            // 建立圖片記錄
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'thumbnail_path' => null,
                'order' => $index,
                'is_primary' => $index === 0,
                'alt_text' => $product->name,
            ]);
        }
    }

    #[Layout('livewire.layouts.seller')]
    public function render()
    {
        return view('livewire.seller.create-product-management');
    }
}
