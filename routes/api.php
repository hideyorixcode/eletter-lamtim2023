<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SuratKeluarController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DokumenTTEController;
use App\Http\Controllers\API\SuratMasukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//API route for register new user
//Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::get('/', function (Request $request) {
    return 'API E-LETTER';
});
Route::post('/login', [AuthController::class, 'login']);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum', 'cekExpireToken']], function () {
    Route::get('/profil', [AuthController::class, 'getProfil']);
    Route::post('/ubah-profil', [AuthController::class, 'ubahProfil']);
    Route::post('/ubah-password', [AuthController::class, 'updatePassword']);
    Route::post('/store-fcm', [AuthController::class, 'storeFcmToken']);

    // API route for logout user
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/data-sm-pimpinan', [SuratMasukController::class, 'data_sm_pimpinan']);
    Route::get('/detail-sm-pimpinan/{id}', [SuratMasukController::class, 'detail_sm_pimpinan']);

    Route::get('/data-sm-intern', [SuratMasukController::class, 'data_sm_intern']);
    Route::get('/detail-sm-intern/{id}', [SuratMasukController::class, 'detail_sm_intern']);

    Route::get('/data-sm-instansi', [SuratMasukController::class, 'data_sm_instansi']);
    Route::get('/detail-sm-instansi/{id}', [SuratMasukController::class, 'detail_sm_instansi']);

    Route::get('/data-sk-basah', [SuratKeluarController::class, 'data_sk_basah']);
    Route::get('/data-sk-elektronik', [SuratKeluarController::class, 'data_sk_elektronik']);
    Route::get('/detail-sk/{id}', [SuratKeluarController::class, 'detail']);
    Route::post('/tte-sk/{id}', [SuratKeluarController::class, 'update_tanda_tangan']);

    Route::get('/data-dokumen-tte', [DokumenTTEController::class, 'data_dokumen_tte']);
    Route::get('/detail-dokumen-tte/{id}', [DokumenTTEController::class, 'detail']);
    Route::post('/tte-dokumen/{id}', [DokumenTTEController::class, 'update_tanda_tangan']);

    Route::get('/dashboard', [DashboardController::class, 'jumlahData']);
});
