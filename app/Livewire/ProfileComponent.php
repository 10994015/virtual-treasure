<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileComponent extends Component
{
    use WithFileUploads;

    // 基本資訊
    public $username = '';
    public $first_name = '';
    public $last_name = '';
    public $email = '';

    // 照片
    public $photo;
    public $currentPhotoUrl = '';

    // 密碼變更
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // 顯示區塊
    public $activeTab = 'profile'; // profile, password, orders, products

    public function mount()
    {
        $user = Auth::user();

        $this->username = $user->username;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->currentPhotoUrl = $user->profile_photo_url;
    }

    // 🔥 切換頁籤
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        // 清除密碼欄位
        if ($tab !== 'password') {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        }
    }

    // 🔥 更新基本資訊
    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'username.required' => '使用者名稱為必填項目',
            'username.unique' => '此使用者名稱已被使用',
            'first_name.required' => '名字為必填項目',
            'last_name.required' => '姓氏為必填項目',
            'email.required' => '電子郵件為必填項目',
            'email.email' => '電子郵件格式不正確',
            'email.unique' => '此電子郵件已被使用',
        ]);

        try {
            $user->update([
                'username' => $this->username,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '個人資訊已更新'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '更新失敗：' . $e->getMessage()
            ]);
        }
    }

    // 🔥 更新照片
    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB Max
        ], [
            'photo.image' => '檔案必須為圖片格式',
            'photo.max' => '圖片大小不可超過 2MB',
        ]);

        try {
            $user = Auth::user();

            // 刪除舊照片
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // 上傳新照片
            $path = $this->photo->store('profile-photos', 'public');

            $user->update([
                'profile_photo_path' => $path,
            ]);

            $this->currentPhotoUrl = $user->profile_photo_url;
            $this->photo = null;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '照片已更新'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '上傳失敗：' . $e->getMessage()
            ]);
        }
    }

    // 🔥 刪除照片
    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);

                $user->update([
                    'profile_photo_path' => null,
                ]);

                $this->currentPhotoUrl = $user->profile_photo_url;

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => '照片已刪除'
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '刪除失敗：' . $e->getMessage()
            ]);
        }
    }

    // 🔥 更新密碼
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => '請輸入目前密碼',
            'new_password.required' => '請輸入新密碼',
            'new_password.min' => '新密碼至少需要 8 個字元',
            'new_password.confirmed' => '新密碼確認不相符',
        ]);

        $user = Auth::user();

        // 驗證目前密碼
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', '目前密碼不正確');
            return;
        }

        try {
            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => '密碼已更新'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => '更新失敗：' . $e->getMessage()
            ]);
        }
    }

    // 🔥 獲取統計數據
    public function getStatsProperty()
    {
        $user = Auth::user();

        return [
            'orders' => \App\Models\Order::where('user_id', $user->id)->count(),
            'products' => \App\Models\Product::where('user_id', $user->id)->count(),
            'conversations' => $user->conversations()->count(),
            'member_since' => $user->created_at->format('Y/m/d'),
        ];
    }

    #[Layout('livewire.layouts.app')]
    public function render()
    {
        return view('livewire.profile-component');
    }
}
