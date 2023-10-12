<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\DokumenTTE;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Auth;


class DashboardController extends Controller
{

    public function jumlahData()
    {
        $cekSM = Disposisi::where('penerima', Auth::user()->id_opd_fk)->where('tgl_diterima', '!=', null)->select('id_sm_fk');
        if ($cekSM->count() > 0):
            $id_sm_fk = $cekSM->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $data_surat_masuk = SuratMasuk::whereIn('id', $id_sm_fk)->whereIn('sifat_surat', ['biasa', 'langsung', 'rahasia'])->count();
        endif;

        $basah_surat_keluar = SuratKeluar::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('kategori_ttd', 'basah')->count();

        $belumtte_surat_keluar = SuratKeluar::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('kategori_ttd', 'elektronik')->whereIn('status_sk', ['draft','revisi'])->count();
        $sudahtte_surat_keluar = SuratKeluar::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('kategori_ttd', 'elektronik')->where('status_sk', 'final')->count();

        $belumtte_dokumen= DokumenTTE::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('status_dokumen', 'draft')->count();
        $sudahtte_dokumen = DokumenTTE::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('status_dokumen', 'final')->count();


        $nama_opd = cek_opd(Auth::user()->id_opd_fk)->nama_opd;


        $data = [
            'suratmasuk' => $data_surat_masuk,
            'basah_surat_keluar' => $basah_surat_keluar,
            'belumtte_surat_keluar' => $belumtte_surat_keluar,
            'sudahtte_surat_keluar' => $sudahtte_surat_keluar,
            'belumtte_dokumen' => $belumtte_dokumen,
            'sudahtte_dokumen' => $sudahtte_dokumen,
        ];

        $httpStatus = 200;
        $res['message'] = 'Jumlah Data Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $res['data'] = $data;
        return response()->json($res, $httpStatus);
    }

}
