<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\StockInController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\PushNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==================== AUTH & REGISTER ROUTES ====================
Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'register']);

Auth::routes(); // login, logout, etc.


// ==================== AUTH USER ROUTES ====================
Route::middleware(['auth'])->group(function () {
    // Account Routes
    Route::get('/account-dashboard', [AccountController::class, 'index'])->name('pages.account');
    Route::get('/account-address', [AccountController::class, 'index_address'])->name('pages.account-address');
    Route::get('/account-address/add', [AccountController::class, 'create_address'])->name('pages.account-address.add');
    Route::post('/account-address/store', [AccountController::class, 'store_address'])->name('pages.account-address.store');
    Route::get('/account/address/{id}/edit', [AccountController::class, 'edit_address'])->name('pages.account-address.edit');
    Route::put('/account/address/{id}', [AccountController::class, 'update_address'])->name('pages.account-address.update');

    Route::get('/account-orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account-orders/{id}', [AccountController::class, 'orders_details'])->name('account.order.details');



    // Checkout & Midtrans
    Route::get('/checkout', [CartController::class, 'index_checkout'])->name('checkout');
    Route::post('/checkout/store', [CartController::class, 'store'])->name('checkout.store');

    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/delete', [CartController::class, 'destroy'])->name('cart.delete');

    // Confirmation
    Route::get('/confirmation', [CartController::class, 'index_confirmation'])->name('order.confirmation');

    Route::post('/midtrans/token/regenerate', [MidtransController::class, 'regenerateSnapToken'])->name('midtrans.token.regenerate');

});

// ==================== MIDTRANS NOTIFICATION (NO AUTH) ====================
Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification']);
Route::post('/checkout/notification', [CartController::class, 'handleNotification']);


// ==================== ADMIN ROUTES ====================
Route::middleware([AuthAdmin::class])->group(function () {
    
    Route::post('/admin/save-subscription', [PushNotificationController::class, 'saveSubscription']);
    
    // Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/completedorder', [AdminController::class, 'index_completed'])->name('order.completed');
    Route::get('/readyorder', [AdminController::class, 'index_ready'])->name('order.ready');
    Route::get('/unprocessedorder', [AdminController::class, 'index_unprocessed'])->name('order.unprocessed');
    Route::get('/orders/{order}', [AdminController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{order}/update-status', [AdminController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    // Brands
    Route::prefix('admin')->group(function () {
    Route::get('/brands', [BrandsController::class, 'index'])->name('admin.brands');
    Route::get('/brand/add', [BrandsController::class, 'create'])->name('admin.brand.add');
    Route::post('/brand/store', [BrandsController::class, 'store'])->name('admin.brand.store');
    Route::get('/brand/edit/{id}', [BrandsController::class, 'edit'])->name('admin.brand.edit');
    Route::put('/brand/update', [BrandsController::class, 'update'])->name('admin.brand.update');
    Route::delete('/brand/{id}/delete', [BrandsController::class, 'destroy'])->name('admin.brand.delete');

    // Categories
    Route::get('/categories', [CategoriesController::class, 'index'])->name('admin.categories');
    Route::get('/categories/add', [CategoriesController::class, 'create'])->name('admin.categories.add');
    Route::post('/categories/store', [CategoriesController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/edit/{id}', [CategoriesController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/update', [CategoriesController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}/delete', [CategoriesController::class, 'destroy'])->name('admin.categories.delete');

    // Suppliers
    Route::get('/suppliers', [SuppliersController::class, 'index'])->name('admin.suppliers');
    Route::get('/suppliers/add', [SuppliersController::class, 'create'])->name('admin.supplier.add');
    Route::post('/suppliers/store', [SuppliersController::class, 'store'])->name('admin.supplier.store');
    Route::get('/suppliers/edit/{id}', [SuppliersController::class, 'edit'])->name('admin.supplier.edit');
    Route::put('/suppliers/update', [SuppliersController::class, 'update'])->name('admin.supplier.update');
    Route::delete('/suppliers/{id}/delete', [SuppliersController::class, 'destroy'])->name('admin.supplier.delete');

    // Products
    Route::get('/products', [ProductsController::class, 'index'])->name('admin.products');
    Route::get('/product/add', [ProductsController::class, 'create'])->name('admin.product.add');
    Route::post('/product/store', [ProductsController::class, 'store'])->name('admin.product.store');
    Route::get('/product/edit/{id}', [ProductsController::class, 'edit'])->name('admin.product.edit');
    Route::put('/product/update/{product}', [ProductsController::class, 'update'])->name('admin.product.update');
    Route::delete('/product/{id}/delete', [ProductsController::class, 'destroy'])->name('admin.product.delete');

    // stock In
    Route::get('/stockin', [StockInController::class, 'index'])->name('admin.stockin');
    Route::get('/stockin/add', [StockInController::class, 'create'])->name('admin.stockin.add');
    Route::post('/stockin/store', [StockInController::class, 'store'])->name('admin.stockin.store');
    Route::post('/stockin/edit/{id}', [StockInController::class, 'edit'])->name('admin.stockin.edit');
    Route::post('/stockin/update', [StockInController::class, 'update'])->name('admin.stockin.update');
    Route::post('/stockin/{id}/delete', [StockInController::class, 'destroy'])->name('admin.stockin.delete');
    });
});

// ==================== PUBLIC ROUTES ====================
// Route::get('/', [HomeController::class, 'index'])->name('pages.index');
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/product/{id}', [ProductsController::class, 'show'])->name('product.show');
Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::post('/midtrans/token', [CartController::class, 'createSnapToken'])->name('midtrans.token');

Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

Route::get('/ajax/search-suggestion', [ShopController::class, 'ajaxSearchSuggestion'])->name('ajax.search.suggestion');




