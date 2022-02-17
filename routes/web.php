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

Route::get('/home', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/hom', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::get('addUser', [App\Http\Controllers\AuthController::class, 'addUser']);
Route::get('users', [App\Http\Controllers\AuthController::class, 'users']);
Route::post('storeUsers', [App\Http\Controllers\AuthController::class, 'storeUsers']);
Route::post('storeUsers', [\App\Http\Controllers\AuthController::class, 'storeUsers']);
Route::get('admin', [App\Http\Controllers\AdminController::class, 'index']);
Route::get('expense', [App\Http\Controllers\AdminController::class, 'expense']);
Route::get('addExpense', [App\Http\Controllers\AdminController::class, 'addExpense']);
Route::post('storeExpense', [App\Http\Controllers\AdminController::class, 'storeExpense']);
Route::get('sell', [App\Http\Controllers\SellController::class, 'index']);
Route::get('displayQuantity', [App\Http\Controllers\SellController::class, 'displayQuantity']);
Route::post('sellP', [App\Http\Controllers\SellController::class, 'sellP']);
Route::get('delProduct', [App\Http\Controllers\SellController::class, 'delProduct']);
Route::get('sale', [App\Http\Controllers\SellController::class, 'sale']);
Route::get('addProduct', [App\Http\Controllers\StockController::class, 'addStock']);
Route::get('stock', [App\Http\Controllers\StockController::class, 'index']);
Route::post('storeStock', [App\Http\Controllers\StockController::class, 'storeStock']);
Route::get('viewStock', [App\Http\Controllers\StockController::class, 'viewStock']);
Route::post('eStock', [App\Http\Controllers\StockController::class, 'eStock']);
Route::get('stockEdit/{id}', [App\Http\Controllers\StockController::class, 'stockEdit']);
Route::get('expenseEdit/{id}', [App\Http\Controllers\AdminController::class, 'expenseEdit']);
Route::post('eExpense', [App\Http\Controllers\AdminController::class, 'eExpense']);
Route::get('viewSale', [App\Http\Controllers\AdminController::class, 'viewSale']);
Route::get('viewSaleHeader', [App\Http\Controllers\AdminController::class, 'viewSaleHeader']);
Route::post('hardwareFilter', [App\Http\Controllers\AdminController::class, 'filterMpesa']);
Route::get ('quote', [App\Http\Controllers\AdminController::class, 'quote']);
Route::get('CalTotal', [App\Http\Controllers\AdminController::class, 'CalTotal']);
Route::get('receiptFooter', [App\Http\Controllers\AdminController::class, 'receiptFooter']);
Route::get('deleteStock', [App\Http\Controllers\AdminController::class, 'deleteStock']);
Route::post('dStock', [App\Http\Controllers\AdminController::class, 'dStock']);
