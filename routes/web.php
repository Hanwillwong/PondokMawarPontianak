<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\EcommerceController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

// Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');

// Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/dashboard', [DashboardController::class, 'index']);

// Route::get('/', [EcommerceController::class, 'index']);

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('pages.index');

Route::get('/product/{id}', [ProductsController::class, 'show'])->name('product.show');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[AccountController::class,'index'])->name('pages.account');
    Route::get('/account-address',[AccountController::class,'index_address'])->name('pages.account-address');
    Route::get('/account-address/add',[AccountController::class,'create_address'])->name('pages.account-address.add');
    Route::post('/account-address/store',[AccountController::class,'store_address'])->name('pages.account-address.store');
    Route::get('/account/address/{id}/edit', [AccountController::class, 'edit_address'])->name('pages.account-address.edit');
    Route::put('/account/address/{id}', [AccountController::class, 'update_address'])->name('pages.account-address.update');
    Route::post('/midtrans/token', [CartController::class, 'createSnapToken'])->name('midtrans.token');

});
Route::middleware([AuthAdmin::class])->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    
    Route::get('/admin/brands',[BrandsController::class,'index'])->name('admin.brands');
    Route::get('/admin/brand/add',[BrandsController::class,'create'])->name('admin.brand.add');
    Route::post('/admin/brand/store',[BrandsController::class,'store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}',[BrandsController::class,'edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update',[BrandsController::class,'update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete',[BrandsController::class,'destroy'])->name('admin.brand.delete');
   
    Route::get('/admin/categories',[CategoriesController::class,'index'])->name('admin.categories');
    Route::get('/admin/categories/add',[CategoriesController::class,'create'])->name('admin.categories.add');
    Route::post('/admin/categories/store',[CategoriesController::class,'store'])->name('admin.categories.store');
    Route::get('/admin/categories/edit/{id}',[CategoriesController::class,'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/update',[CategoriesController::class,'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}/delete',[CategoriesController::class,'destroy'])->name('admin.categories.delete');

    Route::get('/admin/suppliers',[SuppliersController::class,'index'])->name('admin.suppliers');
    Route::get('/admin/suppliers/add',[SuppliersController::class,'create'])->name('admin.supplier.add');
    Route::post('/admin/suppliers/store',[SuppliersController::class,'store'])->name('admin.supplier.store');
    Route::get('/admin/suppliers/edit/{id}',[SuppliersController::class,'edit'])->name('admin.supplier.edit');
    Route::put('/admin/suppliers/update',[SuppliersController::class,'update'])->name('admin.supplier.update');
    Route::delete('/admin/suppliers/{id}/delete',[SuppliersController::class,'destroy'])->name('admin.supplier.delete');

    Route::get('/admin/products',[ProductsController::class,'index'])->name('admin.products');
    Route::get('/admin/product/add',[ProductsController::class,'create'])->name('admin.product.add');
    Route::post('/admin/product/store',[ProductsController::class,'store'])->name('admin.product.store');
    Route::get('/admin/product/edit/{id}',[ProductsController::class,'edit'])->name('admin.product.edit');
    Route::put('/admin/product/update/{product}',[ProductsController::class,'update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete',[ProductsController::class,'destroy'])->name('admin.product.delete');

    Route::get('/cart',[CartController::class,'index'])->name('cart');
    Route::post('/cart/add',[CartController::class,'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/delete',[CartController::class,'destroy'])->name('cart.delete');

    Route::get('/checkout',[CartController::class,'index_checkout'])->name('checkout');
    
    Route::get('/shop',[ShopController::class,'index'])->name('shop');
    // Route::get('/shop/add',[ShopController::class,'create'])->name('shop.add');
    // Route::post('/shop/store',[ShopController::class,'store'])->name('shop.store');
    // Route::get('/shop/edit/{id}',[ShopController::class,'edit'])->name('shop.edit');
    // Route::put('/shop/update',[ShopController::class,'update'])->name('shop.update');
    // Route::delete('/shop/{id}/delete',[ShopController::class,'destroy'])->name('shop.delete');
});