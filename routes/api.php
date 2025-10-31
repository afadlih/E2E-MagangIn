<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControllerApi;

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

// auth API 
Route::post('/login', [AuthControllerApi::class, 'login']);
Route::post('/register/mahasiswa', [AuthControllerApi::class, 'registerMahasiswa']);
Route::post('/register/dosen', [AuthControllerApi::class, 'registerDosen']);
Route::middleware('auth:sanctum')->post('/logout', [AuthControllerApi::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthControllerApi::class, 'getUser']);
Route::get('/user', [AuthControllerApi::class, 'getUser'])->middleware('auth:sanctum');
Route::post('/logout', [AuthControllerApi::class, 'logout'])->middleware('auth:sanctum');