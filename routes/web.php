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
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AccountController;
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

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard',[AccountController::class,'index'])->name('pages.account');
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
});