<?php
use App\Http\Controllers\DataController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\StatisticsController;
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


Route::get('/form', [DataController::class, 'showForm'])->name('form.show');
Route::post('/wilayah-submit', [WilayahController::class, 'submitWilayah'])->name('wilayah.submit');
Route::get('/photo-selection', function() {
    return view('photo');
})->name('photo.selection');
Route::post('/data-submit', [DataController::class, 'submitData'])->name('data.submit');
Route::get('/fetch-kecamatan', [DataController::class, 'fetchKecamatan']);
Route::get('/fetch-nagari/{kecamatanId}', [DataController::class, 'fetchNagari']);

Route::get('/statistik', [StatisticsController::class, 'showStatistics'])->name('statistics.show');
Route::get('/statistik-kecamatan', [StatisticsController::class, 'fetchKecamatan']);
Route::get('/statistik-nageri/{kecamatanId}', [StatisticsController::class, 'fetchNagari']);
Route::get('/statistik-data', [StatisticsController::class, 'fetchStatistics']);

Route::get('/fetch-datatabel', [StatisticsController::class, 'fetchTabel']);
Route::get('/fetch-statistics', [StatisticsController::class, 'fetchStatistics']);
Route::get('/fetch-kecamatan', [StatisticsController::class, 'fetchKecamatan']);
Route::get('/fetch-nagari/{kecamatanId}', [StatisticsController::class, 'fetchNagari']);

Route::get('/fetch-paslon', [StatisticsController::class, 'fetchPaslon']);
Route::get('/fetch-kategori', [StatisticsController::class, 'fetchKategori']);




