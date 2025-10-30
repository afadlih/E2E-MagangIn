<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/provinsi', [App\Http\Controllers\Api\ProvinsiController::class, 'getAllProvinsi'])->name('provinsi');
    Route::get('/kabupaten/{id}', [App\Http\Controllers\Api\KebupatenController::class, 'getListKabupatenByProvinsiId'])->name('kabupaten');
    Route::get('/kecamatan/{id}', [App\Http\Controllers\Api\KecamatanController::class, 'getListKecamatanByKabupatenId'])->name('kecamatan');
    Route::get('/desa/{id}', [App\Http\Controllers\Api\DesaController::class, 'getListDesaByKecamatanId'])->name('desa');
Route::get('/wilayah/search', [\App\Http\Controllers\Api\WilayahController::class, 'searchLocations'])->name('wilayah.search');
//});