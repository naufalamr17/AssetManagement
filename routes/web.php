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
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Mail\DisposeNotification;
use App\Models\dispose;
use App\Models\inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
// Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
// Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
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

Route::get('/generate-letter', [LetterController::class, 'generate'])->middleware('auth')->name('generate-letter');
Route::post('/letters/store', [LetterController::class, 'store'])->middleware('auth')->name('letters.store');
Route::get('/letters/{id}/edit', [LetterController::class, 'edit'])->middleware('auth')->name('letters.edit');
Route::put('/letters/{id}', [LetterController::class, 'update'])->middleware('auth')->name('letters.update');
Route::delete('/letters/{id}', [LetterController::class, 'destroy'])->middleware('auth')->name('letters.destroy');
Route::get('/letters/download/{id}', [LetterController::class, 'download'])->middleware('auth')->name('letters.download');
Route::get('/form-berita-acara/{id}', [LetterController::class, 'showBeritaAcaraForm'])->name('form-berita-acara');
Route::get('/form-kerusakan/{id}', [LetterController::class, 'showKerusakanForm'])->name('form-kerusakan');
Route::get('/form-bast-general/{id}', [LetterController::class, 'showBastGeneral'])->name('form-bast-general');
Route::get('/form-bast-radio/{id}', [LetterController::class, 'showBastRadio'])->name('form-bast-radio');
Route::post('/kerusakan/store', [LetterController::class, 'storeKerusakan'])->name('kerusakan.store');
Route::post('/berita-acara/store', [LetterController::class, 'storeBeritaAcara'])->name('berita-acara.store');
Route::post('/bast/store', [LetterController::class, 'storeBast'])->name('bast.store');
Route::post('/letters/add-document', [LetterController::class, 'addDocument'])->name('letters.addDocument');

Route::get('/view-bast-it-asset', [LetterController::class, 'viewBastItAsset'])->name('view-bast-it-asset')->middleware('auth');

Route::get('/user-management', [AccessController::class, 'index'])->name('user-management')->middleware('auth');
Route::get('/add_user', [AccessController::class, 'adduser'])->name('add_user')->middleware('auth');
Route::post('/store_user', [AccessController::class, 'create'])->name('store_user')->middleware('auth');
Route::delete('/destroy_user/{id}', [AccessController::class, 'destroy'])->name('destroy_user')->middleware('auth');
Route::get('/users/{id}/edit', [AccessController::class, 'edit'])->name('edit_user')->middleware('auth');
Route::put('/users/{id}', [AccessController::class, 'update'])->name('update_user')->middleware('auth');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory')->middleware('auth');
Route::get('/add_inventory', [InventoryController::class, 'addinventory'])->name('add_inventory')->middleware('auth');
Route::post('/store_inventory', [InventoryController::class, 'store'])->name('store_inventory')->middleware('auth');
Route::delete('/destroy_inventory/{id}', [InventoryController::class, 'destroy'])->name('destroy_inventory')->middleware('auth');
Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('edit_inventory')->middleware('auth');
Route::post('/inventory/{id}', [InventoryController::class, 'update'])->name('update_inventory')->middleware('auth');
Route::get('/history_inventory', [InventoryController::class, 'history'])->name('history_inventory')->middleware('auth');
Route::get('/repair_inventory', [InventoryController::class, 'repair'])->name('repair_inventory')->middleware('auth');
Route::get('/input_repair', [InventoryController::class, 'inputrepair'])->name('input_repair')->middleware('auth');
Route::post('/store_repair', [InventoryController::class, 'storerepair'])->name('store_repair')->middleware('auth');
Route::post('/repairstatus/add-document', [InventoryController::class, 'addDocument'])->name('repairstatus.addDocument');
Route::get('/get-inventory-data', [InventoryController::class, 'getInventoryData'])->name('get.inventory.data')->middleware('auth');
Route::get('/dispose_inventory', [InventoryController::class, 'dispose'])->name('dispose_inventory')->middleware('auth');
Route::get('/input_dispose', [InventoryController::class, 'inputdispose'])->name('input_dispose')->middleware('auth');
Route::post('/store_dispose', [InventoryController::class, 'storedispose'])->name('store_dispose')->middleware('auth');

Route::get('/inputexcel', [InventoryController::class, 'inputexcel'])->name('inputexcel')->middleware('auth');
Route::post('/store_excel', [InventoryController::class, 'storeexcel'])->name('store_excel')->middleware('auth');

Route::get('/report', [InventoryController::class, 'report'])->name('report')->middleware('auth');

Route::get('/add-document/{id}', [InventoryController::class, 'document_dispose'])->name('add.document')->middleware('auth');
Route::post('/store_disposedoc', [InventoryController::class, 'storedisposedoc'])->name('store_disposedoc')->middleware('auth');

Route::post('/approval', function (Request $request) {
	// Access form data using $request object
	$itemId = $request->input('itemId');
	$itemId2 = $request->input('itemId2');
	$hirar = $request->input('hirar');
	$approvalStatus = $request->input('approval_action');
	$approval = $approvalStatus . ' by ' . $hirar;

	if ($hirar = 'Supervisor') {
		$dispose = dispose::where('id', $itemId)->get();

		// Prepare email details
		$details = [
			'asset_code' => $itemId2,
			'disposal_date' => $dispose[0]->tanggal_penghapusan,
			'remarks' => $dispose[0]->note,
		];

		// Send email notification from noreply email
		Mail::to('galuh.swasintari@mlpmining.com')  // Ganti dengan email tujuan
			->send(new DisposeNotification($details));
	} elseif ($hirar = 'Manager') {
		// $dispose = dispose::where('id', $itemId)->get();

		// // Prepare email details
		// $details = [
		// 	'asset_code' => $itemId2,
		// 	'disposal_date' =>$dispose[0]->tanggal_penghapusan,
		// 	'remarks' => $dispose[0]->note,
		// ];

		// // Send email notification from noreply email
		// Mail::to('andisari.dewi@mlpmining.com')  // Ganti dengan email tujuan
		// 	->cc(['galuh.swasintari@mlpmining.com'])   // Tambahkan email CC jika diperlukan
		// 	->send(new DisposeNotification($details));
	}

	dispose::where('id', $itemId)->update([
		'Approval' => $approval
	]);

	if (($hirar === "Deputy General Manager" || $hirar === "Manager") && $approvalStatus === "Approve") {
		inventory::where('asset_code', $itemId2)->update([
			'status' => 'Dispose'
		]);
	}

	return redirect()->route('dispose_inventory')->with('success', 'Approval processed successfully!');
})->name('approval')->middleware('auth');

Route::post('/process-qrcode/{id}', [InventoryController::class, 'processQrCode'])->name('process_qrcode')->middleware('auth');

// Route::group(['middleware' => 'auth'], function () {
// 	Route::get('billing', function () {
// 		return view('pages.billing');
// 	})->name('billing');
// 	Route::get('tables', function () {
// 		return view('pages.tables');
// 	})->name('tables');
// 	Route::get('rtl', function () {
// 		return view('pages.rtl');
// 	})->name('rtl');
// 	Route::get('virtual-reality', function () {
// 		return view('pages.virtual-reality');
// 	})->name('virtual-reality');
// 	Route::get('notifications', function () {
// 		return view('pages.notifications');
// 	})->name('notifications');
// 	Route::get('static-sign-in', function () {
// 		return view('pages.static-sign-in');
// 	})->name('static-sign-in');
// 	Route::get('static-sign-up', function () {
// 		return view('pages.static-sign-up');
// 	})->name('static-sign-up');
// 	// Route::get('user-management', function () {
// 	// 	return view('pages.laravel-examples.user-management');
// 	// })->name('user-management');
// 	Route::get('user-profile', function () {
// 		return view('pages.laravel-examples.user-profile');
// 	})->name('user-profile');
// });

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