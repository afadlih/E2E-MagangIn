<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeedbackPengalamanController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\LogAktivitasMhsController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PengajuanMagangController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\PengajuanMagangMhsController;
use App\Http\Controllers\MahasiswaBimbinganController;
use App\Http\Controllers\ProfilAkademikController;
use App\Http\Controllers\DokumenMahasiswaController;
use GuzzleHttp\Psr7\Message;
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
    return redirect()->route('login');
});
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register/mahasiswa', [AuthController::class, 'registerMahasiswa'])->name('register.mahasiswa');
Route::post('/register/mahasiswa', [AuthController::class, 'storeMahasiswa'])->name('register.mahasiswa.store');
Route::get('/register/dosen', [AuthController::class, 'registerDosen'])->name('register.dosen');
Route::post('/register/dosen', [AuthController::class, 'storeDosen'])->name('register.dosen.store');

//route admin beserta auth nya
Route::get('/dashboard-admin', [WelcomeController::class, 'index_admin']);
Route::middleware(['auth', 'authorize:admin'])->group(function () {
    Route::group(['prefix' => 'mahasiswa'], function () {
        Route::get('/', [MahasiswaController::class, 'index']);
        Route::post('/list', [MahasiswaController::class, 'list']);
        Route::get('/create_ajax', [MahasiswaController::class, 'create_ajax']);
        Route::post('/ajax', [MahasiswaController::class, 'store_ajax']);
        Route::get('/{nim}/delete_ajax', [MahasiswaController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{nim}/delete_ajax', [MahasiswaController::class, 'delete_ajax']); // Eksekusi penghapusan
        Route::get('/{nim}/show_ajax', [MahasiswaController::class, 'show_ajax']);
        Route::get('/{nim}/edit_ajax', [MahasiswaController::class, 'edit_ajax']);
        Route::put('/{nim}/update_ajax', [MahasiswaController::class, 'update_ajax']);
        Route::get('/export_pdf', [MahasiswaController::class, 'export_pdf']);
        Route::get('/export_excel', [MahasiswaController::class, 'export_excel']);
        Route::get('import', [MahasiswaController::class, 'import']);
        Route::post('/import_ajax', [MahasiswaController::class, 'import_ajax']);
        
    });

    Route::group(['prefix' => 'dosen'], function () {
        Route::get('/', [DosenController::class, 'index']);
        Route::post('/list', [DosenController::class, 'list']);
        Route::get('/create_ajax', [DosenController::class, 'create_ajax']);
        Route::post('/ajax', [DosenController::class, 'store_ajax']);
        Route::get('/{dosen_id}/delete_ajax', [DosenController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{dosen_id}/delete_ajax', [DosenController::class, 'delete_ajax']);
        Route::get('/{dosen_id}/show_ajax', [DosenController::class, 'show_ajax']);
        Route::get('/{dosen_id}/edit_ajax', [DosenController::class, 'edit_ajax']);
        Route::put('/{dosen_id}/update_ajax', [DosenController::class, 'update_ajax']);
        Route::get('/export_pdf', [DosenController::class, 'export_pdf']);
        Route::get('/export_excel', [DosenController::class, 'export_excel']);
        Route::get('import', [DosenController::class, 'import']);
        Route::post('/import_ajax', [DosenController::class, 'import_ajax']);
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/', [AdminController::class, 'index']);
        Route::post('/list', [AdminController::class, 'list']);
        Route::get('/create_ajax', [AdminController::class, 'create_ajax']);
        Route::post('/ajax', [AdminController::class, 'store_ajax']);
        Route::get('/{admin_id}/delete_ajax', [AdminController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{admin_id}/delete_ajax', [AdminController::class, 'delete_ajax']);
        Route::get('/{admin_id}/show_ajax', [AdminController::class, 'show_ajax']);
        Route::get('/{admin_id}/edit_ajax', [AdminController::class, 'edit_ajax']);
        Route::put('/{admin_id}/update_ajax', [AdminController::class, 'update_ajax']);
        Route::get('/export_pdf', [AdminController::class, 'export_pdf']);
        Route::get('/export_excel', [AdminController::class, 'export_excel']);
        Route::get('import', [AdminController::class, 'import']);
        Route::post('/import_ajax', [AdminController::class, 'import_ajax']);
        Route::get('/{admin_id}/show_admin', [AdminController::class, 'show_admin']);
        Route::get('/{admin_id}/edit_admin', [AdminController::class, 'edit_admin']);
        Route::put('/{admin_id}/update_admin', [AdminController::class, 'update_admin']);
        Route::delete('/{admin_id}/hapus-foto', [AdminController::class, 'hapus_foto_profile'])->name('admin.hapus_foto');
        Route::get('/dashboard/refresh', [WelcomeController::class, 'refreshDashboard'])->name('admin.dashboard.refresh');
    });

    Route::group(['prefix' => 'periode'], function () {
        Route::get('/', [PeriodeController::class, 'index']);
        Route::post('/list', [PeriodeController::class, 'list']);
        Route::get('/create_ajax', [PeriodeController::class, 'create_ajax']);
        Route::post('/ajax', [PeriodeController::class, 'store_ajax']);
        Route::get('/{periode_id}/delete_ajax', [PeriodeController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{periode_id}/delete_ajax', [PeriodeController::class, 'delete_ajax']); // Eksekusi penghapusan
        Route::get('/{periode_id}/show_ajax', [PeriodeController::class, 'show_ajax']);
        Route::get('/{periode_id}/edit_ajax', [PeriodeController::class, 'edit_ajax']);
        Route::put('/{periode_id}/update_ajax', [PeriodeController::class, 'update_ajax']);
        Route::get('/export_pdf', [PeriodeController::class, 'export_pdf']);
        Route::get('/export_excel', [PeriodeController::class, 'export_excel']);
        Route::get('/import', [PeriodeController::class, 'import']);
        Route::post('/import_ajax', [PeriodeController::class, 'import_ajax']);
    });

    Route::group(['prefix' => 'prodi'], function () {
        Route::get('/', [ProdiController::class, 'index']);
        Route::post('/list', [ProdiController::class, 'list']);
        Route::get('/create_ajax', [ProdiController::class, 'create_ajax']);
        Route::post('/ajax', [ProdiController::class, 'store_ajax']);
        Route::get('/{prodi_id}/delete_ajax', [ProdiController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{prodi_id}/delete_ajax', [ProdiController::class, 'delete_ajax']); // Eksekusi penghapusan
        Route::get('/{prodi_id}/show_ajax', [ProdiController::class, 'show_ajax']);
        Route::get('/{prodi_id}/edit_ajax', [ProdiController::class, 'edit_ajax']);
        Route::put('/{prodi_id}/update_ajax', [ProdiController::class, 'update_ajax']);
        Route::get('import', [ProdiController::class, 'import']);
        Route::post('import_ajax', [ProdiController::class, 'import_ajax']);
        Route::get('export_excel', [ProdiController::class, 'export_excel']);
        Route::get('export_pdf', [ProdiController::class, 'export_pdf']);
    });

    Route::group(['prefix' => 'lowongan'], function () {
        Route::get('/', [LowonganController::class, 'index']);
        Route::post('/list', [LowonganController::class, 'list']);
        Route::get('/create_ajax', [LowonganController::class, 'create_ajax']);
        Route::post('/ajax', [LowonganController::class, 'store_ajax']);
        Route::get('/{lowongan_id}/delete_ajax', [LowonganController::class, 'confirm_ajax']);
        Route::delete('/{lowongan_id}/delete_ajax', [LowonganController::class, 'delete_ajax']);
        Route::get('/{lowongan_id}/show_ajax', [LowonganController::class, 'show_ajax']);
        Route::get('/{lowongan_id}/edit_ajax', [LowonganController::class, 'edit_ajax']);
        Route::put('/{lowongan_id}/update_ajax', [LowonganController::class, 'update_ajax']);
    });

    Route::group(['prefix' => 'pengajuan-magang'], function () {
        Route::get('/', [PengajuanMagangController::class, 'index']);
        Route::post('/list', [PengajuanMagangController::class, 'list']);
        Route::get('/{lamaran_id}/show_ajax', [PengajuanMagangController::class, 'show_ajax']);
        Route::post('/{lamaran_id}/update_status', [PengajuanMagangController::class, 'update_status']);
        Route::get('/{lamaran_id}/edit_ajax', [PengajuanMagangController::class, 'edit_ajax']);
        Route::post('/{lamaran_id}/update', [PengajuanMagangController::class, 'update']);
        Route::get('/{lamaran_id}/delete_ajax', [PengajuanMagangController::class, 'confirm_ajax']);
        Route::delete('/{lamaran_id}/delete_ajax', [PengajuanMagangController::class, 'delete_ajax']);
        Route::post('{lamaran_id}/restore', [PengajuanMagangController::class, 'restore'])->name('pengajuan-magang.restore');
        Route::get('/export_pdf', [PengajuanMagangController::class, 'export_pdf']);
        Route::get('/export_excel', [PengajuanMagangController::class, 'export_excel']);
    });

    Route::group(['prefix' => 'perusahaan-mitra'], function () {
        Route::get('/', [PerusahaanController::class, 'index']);
        Route::post('/list', [PerusahaanController::class, 'list']);
        Route::get('/create_ajax', [PerusahaanController::class, 'create_ajax']);
        Route::post('/ajax', [PerusahaanController::class, 'store_ajax']);
        Route::get('/{perusahaan_id}/confirm_ajax', [PerusahaanController::class, 'confirm_ajax']); // Tampilkan modal konfirmasi
        Route::delete('/{perusahaan_id}/delete_ajax', [PerusahaanController::class, 'delete_ajax']);
        Route::get('/{perusahaan_id}/show_ajax', [PerusahaanController::class, 'show_ajax']);
        Route::get('/{perusahaan_id}/edit_ajax', [PerusahaanController::class, 'edit_ajax']);
        Route::put('/{perusahaan_id}/update_ajax', [PerusahaanController::class, 'update_ajax']);
        Route::get('/export_pdf', [PerusahaanController::class, 'exportPdf']);
        Route::get('/export_excel', [PerusahaanController::class, 'export_excel']);
        Route::get('import', [PerusahaanController::class, 'import']);
        Route::post('/import_ajax', [PerusahaanController::class, 'import_ajax']);
    });
});

//route dosen beserta auth nya
Route::middleware(['auth', 'authorize:dosen'])->group(function () {
    Route::get('/dashboard-dosen', [WelcomeController::class, 'index_dosen']);

    Route::group(['prefix' => 'log-aktivitas'], function () {
        Route::get('/', [LogAktivitasMhsController::class, 'index']);
        Route::post('/list', [LogAktivitasMhsController::class, 'list']);
        Route::get('/{id}/show_ajax', [LogAktivitasMhsController::class, 'showAjax']);
        Route::post('/{id}/komentar', [LogAktivitasMhsController::class, 'storeKomentar']);
    });

    Route::group(['prefix' => 'dosen'], function () {
        Route::get('/{dosen_id}/show_dosen', [DosenController::class, 'show_dosen']);
        Route::get('/{dosen_id}/edit_dosen', [DosenController::class, 'edit_dosen']);
        Route::put('/{dosen_id}/update_dosen', [DosenController::class, 'update_dosen']);
        Route::delete('/{dosen_id}/hapus-foto', [DosenController::class, 'hapus_foto_profile'])->name('dosen.hapus_foto');
    });

  
    Route::middleware(['auth', 'authorize:dosen'])->group(function () {
        Route::get('/mahasiswa-bimbingan', [MahasiswaBimbinganController::class, 'index']);
        Route::post('/mahasiswa-bimbingan/list', [MahasiswaBimbinganController::class, 'list']);
        Route::get('/mahasiswa-bimbingan/show_ajax/{nim}', [MahasiswaBimbinganController::class, 'show_ajax'])->name('mahasiswa-bimbingan.show_ajax');

});


});

//route mahasiswa beserta auth nya
Route::middleware(['auth', 'authorize:mahasiswa'])->group(function () {
    Route::get('/dashboard-mahasiswa', [WelcomeController::class, 'index_mahasiswa']);

    Route::group(['prefix' => 'mahasiswa'], function () {
        Route::get('/{nim}/show_mhs', [MahasiswaController::class, 'show_mhs']);
        Route::get('/{nim}/edit_mhs', [MahasiswaController::class, 'edit_mhs']);
        Route::put('/{nim}/update_mhs', [MahasiswaController::class, 'update_mhs']);
        Route::delete('/{nim}/hapus-foto', [MahasiswaController::class, 'hapus_foto_profile'])->name('mhs.hapus_foto');
        Route::get('/rekomendasi-magang', [LowonganController::class, 'rekomendasi'])->name('lowongan.rekomendasi');
        Route::get('/rekomendasi/{lowongan_id}', [LowonganController::class, 'show'])
            ->name('rekomendasi.show');
    });

    Route::group(['prefix' => 'log-aktivitas-mhs'], function () {
        Route::get('/', [LogAktivitasMhsController::class, 'index_mhs']);
        Route::post('/list', [LogAktivitasMhsController::class, 'list_pov_mhs']);
        Route::get('/{id}/show_ajax', [LogAktivitasMhsController::class, 'showAjaxMhs']);
        Route::get('/{id}/create', [LogAktivitasMhsController::class, 'create_ajax']);
        Route::post('/ajax', [LogAktivitasMhsController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [LogAktivitasMhsController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LogAktivitasMhsController::class, 'update_ajax']);
    });

    Route::group(['prefix' => 'feedback-magang'], function () {
        Route::get('/', [FeedbackPengalamanController::class, 'index']);
        Route::get('/feedback/{lamaran}/create', [FeedbackPengalamanController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [FeedbackPengalamanController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/partial', [FeedbackPengalamanController::class, 'partialView'])->name('lamaran.selesai.partial');
    });

    Route::group(['prefix' => 'message'], function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/list', [MessageController::class, 'list']);
        Route::get('/{id}/show_ajax', [MessageController::class, 'show_ajax']);
        Route::post('/{id}/mark_as_read', [MessageController::class, 'markAsRead']);
    });

    Route::group(['prefix' => 'pengajuan-magang-mhs'], function () {
        Route::get('/', [PengajuanMagangMhsController::class, 'index']);
        Route::post('/list', [PengajuanMagangMhsController::class, 'list']);
        Route::get('/{lamaran_id}/show_ajax', [PengajuanMagangMhsController::class, 'show_ajax']);
        Route::post('/{lamaran_id}/update_status', [PengajuanMagangMhsController::class, 'update_status']);
        Route::get('/{lamaran_id}/edit_ajax', [PengajuanMagangMhsController::class, 'edit_ajax']);
        Route::post('/{lamaran_id}/update', [PengajuanMagangMhsController::class, 'update']);
        Route::get('/{lamaran_id}/delete_ajax', [PengajuanMagangMhsController::class, 'confirm_ajax']);
        Route::delete('/{lamaran_id}/delete_ajax', [PengajuanMagangMhsController::class, 'delete_ajax']);
    });

    Route::get('rekomendasi/check-status/{lowongan_id}', [PengajuanMagangMhsController::class, 'checkStatus'])->name('rekomendasi.checkStatus');
    Route::group(['prefix' => 'rekomendasi'], function () {

        Route::get('/{id}/create_ajax', [PengajuanMagangMhsController::class, 'create_ajax']);
        Route::post('/store', [PengajuanMagangMhsController::class, 'store']);
        
    });
});