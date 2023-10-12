<?php


use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Back\DisposisiController;
use App\Http\Controllers\Back\DokumenTTEController;
use App\Http\Controllers\Back\JenisPenandatanganController;
use App\Http\Controllers\Back\LogsController;
use App\Http\Controllers\Back\VisualisasiTTEController;
use App\Http\Controllers\Back\MasterDokumenController;
use App\Http\Controllers\Back\PelaksanaController;
use App\Http\Controllers\Back\PenggunaController;
use App\Http\Controllers\Back\PerangkatDaerahController;
use App\Http\Controllers\Back\PthlController;
use App\Http\Controllers\Back\P3KController;
use App\Http\Controllers\Back\SettingsController;
use App\Http\Controllers\Back\SignatureQRController;
use App\Http\Controllers\Back\SuratKeluarController;
use App\Http\Controllers\Back\SuratKeluarTTEController;
use App\Http\Controllers\Back\SuratLangsungController;
use App\Http\Controllers\Back\SuratMasukController;
use App\Http\Controllers\Back\SuratPejabatController;
use App\Http\Controllers\Back\SuratRahasiaController;
use App\Http\Controllers\HomeController;
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

//Route::get('/', function () {
//    //return view('welcome');
//});
/*Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});*/
/*Route::get('sysmlink', function(){
    $targetFolder = $_SERVER['DOCUMENT_ROOT'].'/qrcodeLaravel/storage/app/public';
    $linkFolder = $_SERVER['DOCUMENT_ROOT'].'/qrcodeLaravel/file-storage';
//    dd($targetFolder, $linkFolder);
    symlink($targetFolder, $linkFolder);
    return 'success';
});*/
/*
 * @TODO: SEND EMAIL
 * KETIKA NAMBAH SURAT MASUK -> TUJUAN BERDASARKAN MELALUI [BAGIAN UMUM, PROTOKOL PIMPINAN]
 * KETIKA DISPOSISI
 * */
Route::get('/', [AuthenticatedSessionController::class, 'create']);
Route::get('dokumen-tte/{id}', [DokumenTTEController::class, 'show']);
Route::get('surat-keluar/{id}', [SuratKeluarController::class, 'show']);
Route::get('surat-keluar-tte/{id}', [SuratKeluarController::class, 'show']);
Route::get('surat-masuk/{id}', [SuratMasukController::class, 'show']);
Route::get('surat-masuk-pejabat/{id}', [SuratPejabatController::class, 'show']);
Route::get('surat-langsung/{id}', [SuratLangsungController::class, 'show']);
Route::get('surat-rahasia/{id}', [SuratRahasiaController::class, 'show']);
Route::get('show-disposisi/{id}', [SuratMasukController::class, 'dataDisposisiShow']);
Route::get('signature-qr/{id}', [SignatureQRController::class, 'show']);
Route::get('tanda-tangan/{id}', [SuratKeluarTTEController::class, 'tanda_tangan_front']);
Route::post('update-ttd', [SuratKeluarTTEController::class, 'update_tanda_tangan_front']);
Route::get('tanda-tangan-dokumen/{id}', [DokumenTTEController::class, 'tanda_tangan_front']);
Route::post('update-ttd-dokumen', [DokumenTTEController::class, 'update_tanda_tangan_front']);


Route::group(['prefix' => 'dashboard', 'middleware' => ['web', 'auth', 'seluruhlevel']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('statistik', [HomeController::class, 'statistik']);
    Route::get('/get-ttd-image', [SuratKeluarTTEController::class, 'getTtdImage']);
    Route::get('/get-list-ttd', [SuratKeluarController::class, 'getListTTDbyTipe']);
    Route::get('/get-list-visualisasi', [VisualisasiTTEController::class, 'getlistvisualisasi']);
    Route::get('verifikasi-tte', [DokumenTTEController::class, 'verifikasiTTE']);
    Route::post('verifikasi-tte/submit', [DokumenTTEController::class, 'ApiVerifikasi']);
    Route::group(['prefix' => 'logs', 'middleware' => ['adminsuper']], function () {
        Route::get('/', [LogsController::class, 'index'])->name('logs');
        Route::get('data', [LogsController::class, 'data'])->name('logs.data');
        Route::delete('delete/{id}', [LogsController::class, 'destroy']);
        Route::post('bulkDelete', [LogsController::class, 'bulkDelete']);
    });

    Route::group(['prefix' => 'pengguna', 'middleware' => ['adminsuper']], function () {
        Route::get('/', [PenggunaController::class, 'index'])->name('pengguna');
        Route::get('data', [PenggunaController::class, 'data']);
        Route::delete('delete/{id}', [PenggunaController::class, 'destroy']);
        Route::post('bulkDelete', [PenggunaController::class, 'bulkDelete']);
        Route::get('form', [PenggunaController::class, 'form'])->name('pengguna.form');
        Route::post('create', [PenggunaController::class, 'create']);
        Route::get('edit/{id}', [PenggunaController::class, 'edit']);
        Route::put('update/{id}', [PenggunaController::class, 'update']);
        Route::get('show/{id}', [PenggunaController::class, 'show']);
    });

    Route::group(['prefix' => 'jenis-penandatangan', 'middleware' => ['adminsuper']], function () {
        Route::get('/', [JenisPenandatanganController::class, 'index'])->name('jenis-penandatangan');
        Route::get('data', [JenisPenandatanganController::class, 'data'])->name('jenis-penandatangan.data');;
        Route::delete('delete/{id}', [JenisPenandatanganController::class, 'destroy']);
        Route::post('bulkDelete', [JenisPenandatanganController::class, 'bulkDelete']);
        Route::post('create', [JenisPenandatanganController::class, 'create']);
        Route::get('edit/{id}', [JenisPenandatanganController::class, 'edit']);
        Route::put('update/{id}', [JenisPenandatanganController::class, 'update']);
        Route::get('visualisasi/{id}', [JenisPenandatanganController::class, 'visualisasi']);
    });

    Route::group(['prefix' => 'visualisasi-tte', 'middleware' => ['adminsuper']], function () {
        Route::get('/', [VisualisasiTTEController::class, 'index']);
        Route::get('data', [VisualisasiTTEController::class, 'data']);
        Route::delete('delete/{id}', [VisualisasiTTEController::class, 'destroy']);
        Route::post('bulkDelete', [VisualisasiTTEController::class, 'bulkDelete']);
        Route::post('create', [VisualisasiTTEController::class, 'create']);
    });


    Route::group(['prefix' => 'perangkat-daerah', 'middleware' => ['adminsuper']], function () {
        Route::get('/', [PerangkatDaerahController::class, 'index'])->name('perangkat-daerah');
        Route::get('data', [PerangkatDaerahController::class, 'data'])->name('perangkat-daerah.data');;
        Route::get('form', [PerangkatDaerahController::class, 'form'])->name('perangkat-daerah.form');;
        Route::delete('delete/{id}', [PerangkatDaerahController::class, 'destroy']);
        Route::post('bulkDelete', [PerangkatDaerahController::class, 'bulkDelete']);
        Route::post('create', [PerangkatDaerahController::class, 'create']);
        Route::get('edit/{id}', [PerangkatDaerahController::class, 'edit']);
        Route::get('show/{id}', [PerangkatDaerahController::class, 'show']);
        Route::put('update/{id}', [PerangkatDaerahController::class, 'update']);
        Route::get('sync-simpedu', [PerangkatDaerahController::class, 'sinkronisasi_id_simpedu']);
        Route::get('sync-unker', [PerangkatDaerahController::class, 'sinkronisasi_unker']);
    });


    Route::group(['prefix' => 'statistik', 'middleware' => ['seluruhlevel']], function () {
        Route::get('tanda-tangan', [SuratKeluarController::class, 'statistik_tanda_tangan'])->middleware('suratkeluar');
        Route::get('grafik-tanda-tangan', [SuratKeluarController::class, 'tampil_grafik_ttd'])->middleware('suratkeluar');
        Route::get('perangkat-daerah', [SuratKeluarController::class, 'statistik_perangkat_daerah'])->middleware('suratkeluar');
        Route::get('grafik-perangkat-daerah', [SuratKeluarController::class, 'tampil_grafik_pd'])->middleware('suratkeluar');
//        Route::get('signature-opd', [SignatureQRController::class, 'statistik_perangkat_daerah']);
//        Route::get('grafik-signature-opd', [SignatureQRController::class, 'tampil_grafik_pd']);
    });

    Route::group(['prefix' => 'surat-masuk', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratMasukController::class, 'index'])->name('surat-masuk');
        Route::get('data', [SuratMasukController::class, 'data'])->name('surat-masuk.data');
        Route::delete('delete/{id}', [SuratMasukController::class, 'destroy'])->middleware('superumum');
        Route::post('bulkDelete', [SuratMasukController::class, 'bulkDelete'])->middleware('superumum');
        Route::get('form', [SuratMasukController::class, 'form'])->name('surat-masuk.form')->middleware('superumum');
        Route::post('create', [SuratMasukController::class, 'create'])->middleware('superumum');
        Route::get('edit/{id}', [SuratMasukController::class, 'edit'])->middleware('superumum');
        Route::get('print/{id}', [SuratMasukController::class, 'print']);
        Route::put('update/{id}', [SuratMasukController::class, 'update'])->middleware('superumum');
    });


    Route::group(['prefix' => 'disposisi', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/{id}', [SuratMasukController::class, 'disposisi']);
        Route::get('data/{id}', [SuratMasukController::class, 'dataDisposisi']);
        Route::get('data-get/{id}', [DisposisiController::class, 'data']);

        Route::delete('delete/{id}', [DisposisiController::class, 'destroy']);
        Route::post('create', [DisposisiController::class, 'create']);
        Route::post('update-disposisi', [DisposisiController::class, 'update']);
        Route::get('edit/{id}', [DisposisiController::class, 'edit']);
        //Route::put('update/{id}', [DisposisiController::class, 'update']);
        Route::get('get-dari/{id}', [DisposisiController::class, 'getdari']);
    });

    Route::group(['prefix' => 'surat-langsung', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratLangsungController::class, 'index'])->name('surat-langsung');
        Route::get('data', [SuratLangsungController::class, 'data'])->name('surat-langsung.data');
        Route::delete('delete/{id}', [SuratMasukController::class, 'destroy'])->middleware('superadpim');
        Route::post('bulkDelete', [SuratMasukController::class, 'bulkDelete'])->middleware('superadpim');
        Route::get('form', [SuratLangsungController::class, 'form'])->name('surat-langsung.form')->middleware('superadpim');
        Route::post('create', [SuratLangsungController::class, 'create'])->middleware('superadpim');
        Route::get('edit/{id}', [SuratLangsungController::class, 'edit'])->middleware('adpimumum');
        Route::get('print/{id}', [SuratMasukController::class, 'print']);
        Route::put('update/{id}', [SuratLangsungController::class, 'update'])->middleware('adpimumum');
    });

    Route::group(['prefix' => 'surat-rahasia', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratRahasiaController::class, 'index'])->name('surat-rahasia');
        Route::get('data', [SuratRahasiaController::class, 'data'])->name('surat-rahasia.data');
        Route::delete('delete/{id}', [SuratMasukController::class, 'destroy'])->middleware('superumum');
        Route::post('bulkDelete', [SuratMasukController::class, 'bulkDelete'])->middleware('superumum');
        Route::get('form', [SuratRahasiaController::class, 'form'])->name('surat-rahasia.form')->middleware('superumum');
        Route::post('create', [SuratRahasiaController::class, 'create'])->middleware('superumum');
        Route::get('edit/{id}', [SuratRahasiaController::class, 'edit'])->middleware('superumum');
        Route::get('print/{id}', [SuratMasukController::class, 'print']);
        Route::put('update/{id}', [SuratRahasiaController::class, 'update'])->middleware('superumum');
    });

    Route::group(['prefix' => 'surat-keluar', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratKeluarController::class, 'index'])->name('surat-keluar');
        Route::get('data', [SuratKeluarController::class, 'data'])->name('surat-keluar.data');
        Route::delete('delete/{id}', [SuratKeluarController::class, 'destroy'])->middleware('suratkeluar');
        Route::post('bulkDelete', [SuratKeluarController::class, 'bulkDelete'])->middleware('suratkeluar');
        Route::get('form', [SuratKeluarController::class, 'form'])->name('surat-keluar.form')->middleware('suratkeluar');
        Route::post('create', [SuratKeluarController::class, 'create'])->middleware('suratkeluar');
        Route::post('create-from-opd', [SuratKeluarController::class, 'create_from_opd'])->middleware('suratkeluar');
        Route::get('edit/{id}', [SuratKeluarController::class, 'edit'])->middleware('suratkeluar');
        Route::get('print/{id}', [SuratKeluarController::class, 'print']);
        Route::get('testing/{id}', [SuratKeluarController::class, 'testing']);
        Route::put('update/{id}', [SuratKeluarController::class, 'update'])->middleware('suratkeluar');
        Route::put('update-from-opd/{id}', [SuratKeluarController::class, 'update_from_opd'])->middleware('suratkeluar');
        Route::get('statistik-tanda-tangan', [SuratKeluarController::class, 'statistik_tanda_tangan']);
        Route::get('grafik-statistik-tanda-tangan', [SuratKeluarController::class, 'tampil_grafik_ttd']);
        //Route::get('tte', [SuratKeluarController::class, 'indextte']);
    });

    Route::group(['prefix' => 'surat-masuk-instansi', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratKeluarController::class, 'index_surat_masuk_instansi'])->name('surat-masuk-instansi');
        Route::get('data', [SuratKeluarController::class, 'data_surat_masuk_instansi'])->name('surat-masuk-instansi.data');
        //Route::get('tte', [SuratKeluarController::class, 'indextte']);
    });


    Route::group(['prefix' => 'surat-keluar-tte', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [SuratKeluarTTEController::class, 'index'])->name('surat-keluar-tte');
        Route::get('data', [SuratKeluarTTEController::class, 'data'])->name('surat-keluar-tte.data');
        Route::get('form', [SuratKeluarTTEController::class, 'form'])->name('surat-keluar-tte.form')->middleware('suratkeluar');
        Route::get('form_testing', [SuratKeluarTTEController::class, 'form_testing'])->name('surat-keluar-tte.form_testing')->middleware('suratkeluar');
        Route::post('create', [SuratKeluarTTEController::class, 'create'])->middleware('suratkeluar');
        Route::post('create-from-opd', [SuratKeluarTTEController::class, 'create_from_opd'])->middleware('suratkeluar');
        Route::post('create-from-opd-testing', [SuratKeluarTTEController::class, 'create_from_opd_testing'])->middleware('suratkeluar');
        Route::get('edit/{id}', [SuratKeluarTTEController::class, 'edit'])->middleware('suratkeluar');
        Route::put('update/{id}', [SuratKeluarTTEController::class, 'update'])->middleware('suratkeluar');
        Route::put('update-from-opd/{id}', [SuratKeluarTTEController::class, 'update_from_opd'])->middleware('suratkeluar');
        Route::get('tanda-tangan/{id}', [SuratKeluarTTEController::class, 'tanda_tangan'])->middleware('penandatangan');
        Route::post('update-tanda-tangan', [SuratKeluarTTEController::class, 'update_tanda_tangan'])->middleware('penandatangan');
    });

    Route::group(['prefix' => 'dokumen-tte', 'middleware' => ['seluruhlevel']], function () {
        Route::get('/', [DokumenTTEController::class, 'index'])->name('dokumen-tte');
        Route::get('data', [DokumenTTEController::class, 'data'])->name('dokumen-tte.data');
        Route::delete('delete/{id}', [DokumenTTEController::class, 'destroy'])->middleware('dokumentte');
        Route::post('bulkDelete', [DokumenTTEController::class, 'bulkDelete'])->middleware('dokumentte');
        Route::get('form', [DokumenTTEController::class, 'form'])->name('dokumen-tte.form')->middleware('dokumentte');
        Route::post('create', [DokumenTTEController::class, 'create'])->middleware('dokumentte');
        Route::get('edit/{id}', [DokumenTTEController::class, 'edit'])->middleware('dokumentte');
        Route::put('update/{id}', [DokumenTTEController::class, 'update'])->middleware('dokumentte');
        Route::get('tanda-tangan/{id}', [DokumenTTEController::class, 'tanda_tangan'])->middleware('penandatangan');
        Route::post('update-tanda-tangan', [DokumenTTEController::class, 'update_tanda_tangan'])->middleware('penandatangan');

    });

    Route::group(['prefix' => 'surat-masuk-pejabat', 'middleware' => ['suratpejabat']], function () {
        Route::get('/', [SuratPejabatController::class, 'index'])->name('surat-masuk-pejabat');
        Route::get('data', [SuratPejabatController::class, 'data'])->name('surat-masuk-pejabat.data');
        Route::delete('delete/{id}', [SuratPejabatController::class, 'destroy'])->middleware('suratpejabat');
        Route::post('bulkDelete', [SuratPejabatController::class, 'bulkDelete'])->middleware('suratpejabat');
        Route::get('form', [SuratPejabatController::class, 'form'])->name('surat-masuk-pejabat.form')->middleware('suratpejabat');
        Route::post('create', [SuratPejabatController::class, 'create'])->middleware('suratpejabat');
        Route::get('edit/{id}', [SuratPejabatController::class, 'edit'])->middleware('suratpejabat');
        Route::put('update/{id}', [SuratPejabatController::class, 'update'])->middleware('suratpejabat');
    });


    Route::get('profil', [PenggunaController::class, 'profil'])->name('profil');
    Route::get('side-profil', [PenggunaController::class, 'sideProfil']);
    Route::put('update-profil', [PenggunaController::class, 'updateProfil']);
    Route::get('ubah-password', [PenggunaController::class, 'ubahPassword'])->name('ubah-password');
    Route::put('update-password', [PenggunaController::class, 'updatePassword']);
    Route::get('settings', [SettingsController::class, 'index'])->name('settings')->middleware('adminsuper');
    Route::put('update-settings', [SettingsController::class, 'updateAll'])->middleware('adminsuper');
    Route::get('my-logs', [LogsController::class, 'logsPengguna'])->name('my-logs');
    Route::get('data-logs', [LogsController::class, 'logsData']);
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth', 'adminsuper']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    Route::get('push-notification', [NotificationController::class, 'index']);
    Route::post('sendNotification', [NotificationController::class, 'sendNotification'])->name('send.notification');
});


//Route::get('/jenisbantuan', [JenisBantuanController::class, 'index'])
//    ->middleware(['auth'])->name('jenisbantuan');

require __DIR__ . '/auth.php';
