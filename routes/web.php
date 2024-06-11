<?php

use App\Http\Controllers\AccessController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
	return view('welcome');
});

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;


Route::get('/', function () {
	return redirect('sign-in');
})->middleware('guest');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
	return view('sessions.password.verify');
})->middleware('guest')->name('verify');
Route::get('/reset-password/{token}', function ($token) {
	return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('profile', [ProfileController::class, 'create'])->middleware('auth')->name('profile');
Route::post('user-profile', [ProfileController::class, 'update'])->middleware('auth');

Route::get('/user-management', [AccessController::class, 'index'])->name('user-management')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::get('billing', function () {
		return view('pages.billing');
	})->name('billing');
	Route::get('tables', function () {
		return view('pages.tables');
	})->name('tables');
	Route::get('rtl', function () {
		return view('pages.rtl');
	})->name('rtl');
	Route::get('virtual-reality', function () {
		return view('pages.virtual-reality');
	})->name('virtual-reality');
	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');
	Route::get('static-sign-in', function () {
		return view('pages.static-sign-in');
	})->name('static-sign-in');
	Route::get('static-sign-up', function () {
		return view('pages.static-sign-up');
	})->name('static-sign-up');
	// Route::get('user-management', function () {
	// 	return view('pages.laravel-examples.user-management');
	// })->name('user-management');
	Route::get('user-profile', function () {
		return view('pages.laravel-examples.user-profile');
	})->name('user-profile');
});

// Route::middleware('auth')->group(function () {
	// 	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	// 	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	// 	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	// 	Route::get('/user-management', [AccessController::class, 'index'])->name('user-management');
	// 	Route::get('/add_user', [AccessController::class, 'adduser'])->name('add_user');
	// 	Route::post('/store_user', [AccessController::class, 'create'])->name('store_user');
	// 	Route::delete('/destroy_user/{id}', [AccessController::class, 'destroy'])->name('destroy_user');
	// 	Route::get('/users/{id}/edit', [AccessController::class, 'edit'])->name('edit_user');
	// 	Route::put('/users/{id}', [AccessController::class, 'update'])->name('update_user');
	
	// 	Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
	// 	Route::get('/add_inventory', [InventoryController::class, 'addinventory'])->name('add_inventory');
	// 	Route::post('/store_inventory', [InventoryController::class, 'store'])->name('store_inventory');
	// 	Route::delete('/destroy_inventory/{id}', [InventoryController::class, 'destroy'])->name('destroy_inventory');
	// 	Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('edit_inventory');
	// 	Route::post('/inventory/{id}', [InventoryController::class, 'update'])->name('update_inventory');
	// 	Route::get('/history_inventory', [InventoryController::class, 'history'])->name('history_inventory');
	// 	Route::get('/repair_inventory', [InventoryController::class, 'repair'])->name('repair_inventory');
	// 	Route::get('/input_repair', [InventoryController::class, 'inputrepair'])->name('input_repair');
	// 	Route::get('/get-inventory-data', [InventoryController::class, 'getInventoryData'])->name('get.inventory.data');
	// });