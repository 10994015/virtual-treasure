<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 檢查管理員是否已存在
        if (User::where('email', 'admin@gmail.com')->exists()) {
            $this->command->info('管理員帳號已存在，跳過建立。');
            return;
        }

        // 建立管理員帳號
        User::create([
            'first_name' => '員',
            'last_name' => '管理',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'is_seller' => true,
        ]);

        $this->command->info('管理員帳號建立成功！');
        $this->command->info('Email: admin@gmail.com');
        $this->command->info('Password: admin123');
    }
}
