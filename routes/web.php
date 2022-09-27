<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/auth/signin', 'auth.signin')->middleware('guest');
Route::post('/auth/signin', [\App\Http\Controllers\AuthController::class, 'signin'])->middleware('guest');
Route::post('/auth/signout', [\App\Http\Controllers\AuthController::class, 'signout'])->middleware('auth');

Route::group([
    'middleware' => ['auth']
], function () {
    Route::view('/', 'welcome');
    Route::view('/dashboard', 'welcome');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->can(\App\Enums\PermissionEnum::view_users());
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->can(\App\Enums\PermissionEnum::create_user());
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->can(\App\Enums\PermissionEnum::create_user());
    Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->can(\App\Enums\PermissionEnum::update_user());
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->can(\App\Enums\PermissionEnum::update_user());
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->can(\App\Enums\PermissionEnum::delete_user());

    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->can(\App\Enums\PermissionEnum::view_roles());
    Route::get('/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->can(\App\Enums\PermissionEnum::create_role());
    Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->can(\App\Enums\PermissionEnum::create_role());
    Route::get('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'show'])->can(\App\Enums\PermissionEnum::update_role());
    Route::put('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->can(\App\Enums\PermissionEnum::update_role());
    Route::delete('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->can(\App\Enums\PermissionEnum::delete_role());

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index']);
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::get('/profile/password', [\App\Http\Controllers\ProfilePasswordController::class, 'index']);
    Route::put('/profile/password', [\App\Http\Controllers\ProfilePasswordController::class, 'update']);

    Route::get('/products/import', [\App\Http\Controllers\ProductImportController::class, 'index'])->can(\App\Enums\PermissionEnum::create_product());
    Route::post('/products/import', [\App\Http\Controllers\ProductImportController::class, 'store'])->can(\App\Enums\PermissionEnum::create_product());

    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->can(\App\Enums\PermissionEnum::view_products());
    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->can(\App\Enums\PermissionEnum::create_product());
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->can(\App\Enums\PermissionEnum::create_product());
    Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->can(\App\Enums\PermissionEnum::update_product());
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->can(\App\Enums\PermissionEnum::update_product());
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->can(\App\Enums\PermissionEnum::delete_product());

    Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index'])->can(\App\Enums\PermissionEnum::view_branches());
    Route::get('/branches/create', [\App\Http\Controllers\BranchController::class, 'create'])->can(\App\Enums\PermissionEnum::create_branch());
    Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store'])->can(\App\Enums\PermissionEnum::create_branch());
    Route::get('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'show'])->can(\App\Enums\PermissionEnum::update_branch());
    Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])->can(\App\Enums\PermissionEnum::update_branch());
    Route::delete('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'destroy'])->can(\App\Enums\PermissionEnum::delete_branch());

    Route::get('/product-categories', [\App\Http\Controllers\ProductCategoryController::class, 'index'])->can(\App\Enums\PermissionEnum::view_product_categories());
    Route::get('/product-categories/create', [\App\Http\Controllers\ProductCategoryController::class, 'create']);
    Route::post('/product-categories', [\App\Http\Controllers\ProductCategoryController::class, 'store']);
    Route::get('/product-categories/{productCategory}', [\App\Http\Controllers\ProductCategoryController::class, 'show']);
    Route::put('/product-categories/{productCategory}', [\App\Http\Controllers\ProductCategoryController::class, 'update']);
    Route::delete('/product-categories/{productCategory}', [\App\Http\Controllers\ProductCategoryController::class, 'destroy']);

    Route::get('/order-sources', [\App\Http\Controllers\OrderSourceController::class, 'index'])->can(\App\Enums\PermissionEnum::view_order_sources());
    Route::get('/order-sources/create', [\App\Http\Controllers\OrderSourceController::class, 'create']);
    Route::post('/order-sources', [\App\Http\Controllers\OrderSourceController::class, 'store']);
    Route::get('/order-sources/{orderSource}', [\App\Http\Controllers\OrderSourceController::class, 'show']);
    Route::put('/order-sources/{orderSource}', [\App\Http\Controllers\OrderSourceController::class, 'update']);
    Route::delete('/order-sources/{orderSource}', [\App\Http\Controllers\OrderSourceController::class, 'destroy']);

    Route::get('/resellers', [\App\Http\Controllers\ResellerController::class, 'index'])->can(\App\Enums\PermissionEnum::view_resellers());
    Route::get('/resellers/create', [\App\Http\Controllers\ResellerController::class, 'create']);
    Route::post('/resellers', [\App\Http\Controllers\ResellerController::class, 'store']);
    Route::get('/resellers/{reseller}', [\App\Http\Controllers\ResellerController::class, 'show']);
    Route::put('/resellers/{reseller}', [\App\Http\Controllers\ResellerController::class, 'update']);
    Route::delete('/resellers/{reseller}', [\App\Http\Controllers\ResellerController::class, 'destroy']);

    Route::get('/inventories', [\App\Http\Controllers\InventoryController::class, 'index'])->can(\App\Enums\PermissionEnum::view_inventories());
    Route::get('/inventories/create', [\App\Http\Controllers\InventoryController::class, 'create']);
    Route::post('/inventories', [\App\Http\Controllers\InventoryController::class, 'store']);

    Route::get('/product-inventories', [\App\Http\Controllers\ProductInventoryController::class, 'index'])->can(\App\Enums\PermissionEnum::view_product_inventories());

    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->can(\App\Enums\PermissionEnum::view_orders());
    Route::get('/orders/create', [\App\Http\Controllers\OrderController::class, 'create']);
    Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store']);
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show']);
    Route::delete('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'destroy']);

    Route::get('/item-categories', [\App\Http\Controllers\ItemCategoryController::class, 'index'])->can(\App\Enums\PermissionEnum::view_item_categories());
    Route::get('/item-categories/create', [\App\Http\Controllers\ItemCategoryController::class, 'create']);
    Route::post('/item-categories', [\App\Http\Controllers\ItemCategoryController::class, 'store']);
    Route::get('/item-categories/{itemCategory}', [\App\Http\Controllers\ItemCategoryController::class, 'show']);
    Route::put('/item-categories/{itemCategory}', [\App\Http\Controllers\ItemCategoryController::class, 'update']);
    Route::delete('/item-categories/{itemCategory}', [\App\Http\Controllers\ItemCategoryController::class, 'destroy']);

    Route::get('/items/import', [\App\Http\Controllers\ItemImportController::class, 'index']);
    Route::post('/items/import', [\App\Http\Controllers\ItemImportController::class, 'store']);

    Route::get('/items', [\App\Http\Controllers\ItemController::class, 'index'])->can(\App\Enums\PermissionEnum::view_items());
    Route::get('/items/create', [\App\Http\Controllers\ItemController::class, 'create']);
    Route::post('/items', [\App\Http\Controllers\ItemController::class, 'store']);
    Route::get('/items/{item}', [\App\Http\Controllers\ItemController::class, 'show']);
    Route::put('/items/{item}', [\App\Http\Controllers\ItemController::class, 'update']);
    Route::delete('/items/{item}', [\App\Http\Controllers\ItemController::class, 'destroy']);

    Route::get('/item-inventories', [\App\Http\Controllers\ItemInventoryController::class, 'index'])->can(\App\Enums\PermissionEnum::view_item_inventories());

    Route::get('/purchases', [\App\Http\Controllers\PurchaseController::class, 'index'])->can(\App\Enums\PermissionEnum::view_purchases());
    Route::get('/purchases/create', [\App\Http\Controllers\PurchaseController::class, 'create']);
    Route::post('/purchases', [\App\Http\Controllers\PurchaseController::class, 'store']);
    Route::get('/purchases/{purchase}', [\App\Http\Controllers\PurchaseController::class, 'show']);
    Route::delete('/purchases/{purchase}', [\App\Http\Controllers\PurchaseController::class, 'destroy']);
});
