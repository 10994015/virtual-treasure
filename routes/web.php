<?php

use App\Livewire\CartComponent;
use App\Livewire\CheckoutComponent;
use App\Livewire\CheckoutSuccessComponent;
use App\Livewire\HomeComponent;
use App\Livewire\MarketComponent;
use App\Livewire\MessagingComponent;
use App\Livewire\MyOrdersComponent;
use App\Livewire\OrderDetailComponent;
use App\Livewire\ProductDetailComponent;
use App\Livewire\AIChatComponent;
use App\Livewire\ProfileComponent;
use App\Livewire\Seller\CreateProductManagement;
use App\Livewire\Seller\DashboardComponent;
use App\Livewire\Seller\EditProductManagement;
use App\Livewire\Seller\OrderDetailManagement;
use App\Livewire\Seller\OrderManagement;
use App\Livewire\Seller\ProductManagement;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['web', 'auth']]);
// 公開頁面
Route::get('/', HomeComponent::class)->name('home');
Route::get('/products', MarketComponent::class)->name('products.index');
Route::get('/product-detail/{slug}', ProductDetailComponent::class)->name('products.show');
Route::get('/cart', CartComponent::class)->name('cart');

// 需要登入的買家功能 - 使用完整的 Jetstream middleware
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/ai-chat', AIChatComponent::class)->name('ai-chat');
    Route::get('/orders', MyOrdersComponent::class)->name('orders.index');
    Route::get('/order-detail/{order}', OrderDetailComponent::class)->name('orders.show');
    Route::get('/checkout', CheckoutComponent::class)->name('checkout');
    Route::get('/checkout/success/{order}', CheckoutSuccessComponent::class)->name('checkout.success');
    Route::get('/messages/{conversationId?}', MessagingComponent::class)->name('messages');
    Route::get('/profile', ProfileComponent::class)->name('profile');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 需要登入的賣家功能
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'can:seller'
])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', DashboardComponent::class)->name('dashboard');
    Route::get('/products', ProductManagement::class)->name('products.index');
    Route::get('/create-product', CreateProductManagement::class)->name('products.create');
    Route::get('/products/{product}/edit', EditProductManagement::class)->name('products.edit');
    Route::get('/orders', OrderManagement::class)->name('orders.index');
    Route::get('/orders-detail/{order}', OrderDetailManagement::class)->name('orders.show');
});
