<?php

namespace App\Http\Controllers;


use App\Models\Disposisi;
use App\Models\JenisPenandatangan;
use App\Models\PerangkatDaerah;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use DataTables;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index()
    {

        if (in_array(Auth::user()->level, ['admin', 'superadmin'])) {
            $cekSM_kosong = Disposisi::where('tgl_diterima', null)->select('id_sm_fk');
            $id_sm_fk_kosong = $cekSM_kosong->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $listSMbiasa = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'biasa')->get();
//            $listSMrahasia = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'rahasia')->get();
//            $listSMlangsung = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'langsung')->get();
            $listSK = SuratKeluar::where('status_sk', 'draft')->get();
            $data = [
                'jenisttd' => JenisPenandatangan::where('active', 1)->count(),
                'perangkatdaerah' => PerangkatDaerah::where('active', 1)->count(),
                // 'kodeqr' => KodeQR::count(),
                'suratmasuk' => SuratMasuk::where('sifat_surat', 'biasa')->count(),
//                'suratlangsung' => SuratMasuk::where('sifat_surat', 'langsung')->count(),
//                'suratrahasia' => SuratMasuk::where('sifat_surat', 'rahasia')->count(),
                'suratkeluarbasah' => SuratKeluar::where('kategori_ttd', 'basah')->count(),
                'suratkeluartte' => SuratKeluar::where('kategori_ttd', 'elektronik')->count(),
                //'signatureqr' => SignatureQR::count(),
                //'data_tahun' => KodeQR::tahun()->get()
                'listPerangkat' => PerangkatDaerah::select('id_opd', 'nama_opd')->get(),
                'listSMbiasa' => $listSMbiasa,
//                'listSMrahasia' => $listSMrahasia,
//                'listSMlangsung' => $listSMlangsung,
                'listSK' => $listSK,
            ];
            return view('dashboard_page.dashboard', $data);
        } else if (in_array(Auth::user()->level, ['adpim', 'umum'])) {
            //$cekSM_kosong = Disposisi::where('tgl_diterima', null)->select('id_sm_fk');
            $cekSM_kosong = Disposisi::where('penerima', Auth::user()->id_opd_fk)->where('tgl_diterima', null)->select('id_sm_fk');
            $id_sm_fk_kosong = $cekSM_kosong->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $listSMbiasa = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'biasa')->get();
//            $listSMrahasia = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'rahasia')->get();
//            $listSMlangsung = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'langsung')->get();
            $listSK = SuratKeluar::where('status_sk', 'draft')->get();
            $data = [
                'suratmasuk' => SuratMasuk::where('sifat_surat', 'biasa')->count(),
                'suratlangsung' => SuratMasuk::where('sifat_surat', 'langsung')->count(),
                'suratrahasia' => SuratMasuk::where('sifat_surat', 'rahasia')->count(),
                'suratkeluarbasah' => SuratKeluar::where('kategori_ttd', 'basah')->count(),
                'suratkeluartte' => SuratKeluar::where('kategori_ttd', 'elektronik')->count(),
                'listPerangkat' => PerangkatDaerah::select('id_opd', 'nama_opd')->get(),
                'listSMbiasa' => $listSMbiasa,
//                'listSMrahasia' => $listSMrahasia,
//                'listSMlangsung' => $listSMlangsung,
                'listSK' => $listSK,
            ];
            return view('dashboard_page.dashboardtu', $data);
        } else {
            $cekSM = Disposisi::where('penerima', Auth::user()->id_opd_fk)->where('tgl_diterima', '!=', null)->select('id_sm_fk');
            if ($cekSM->count() > 0):
                $id_sm_fk = $cekSM->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
                $data_surat_masuk = SuratMasuk::whereIn('id', $id_sm_fk)->where('sifat_surat', 'biasa')->count();
//                $data_surat_langsung = SuratMasuk::whereIn('id', $id_sm_fk)->where('sifat_surat', 'langsung')->count();
//                $data_surat_rahasia = SuratMasuk::whereIn('id', $id_sm_fk)->where('sifat_surat', 'rahasia')->count();
            else:
                $id_sm_fk = [];
                $data_surat_masuk = 0;
//                $data_surat_langsung = 0;
//                $data_surat_rahasia = 0;
            endif;

            if (Auth::user()->level == 'sespri') {
                $data_surat_keluar_basah = SuratKeluar::where('id_opd_fk', Auth::user()->id_opd_fk)->where('kategori_ttd', 'basah')->count();
                $data_surat_keluar_tte = SuratKeluar::where('id_opd_fk', Auth::user()->id_opd_fk)->where('kategori_ttd', 'elektronik')->count();
            } else {
                $data_surat_keluar_basah = SuratKeluar::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('kategori_ttd', 'basah')->count();
                $data_surat_keluar_tte = SuratKeluar::where('id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk)->where('kategori_ttd', 'elektronik')->count();
            }

            $cekSM_kosong = Disposisi::where('penerima', Auth::user()->id_opd_fk)->where('tgl_diterima', null)->select('id_sm_fk');
            $id_sm_fk_kosong = $cekSM_kosong->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $listSMbiasa = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'biasa')->get();
//            $listSMrahasia = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'rahasia')->get();
//            $listSMlangsung = SuratMasuk::whereIn('id', $id_sm_fk_kosong)->where('sifat_surat', 'langsung')->get();
            $listSK = SuratKeluar::where('status_sk', 'draft')->where('id_opd_fk', Auth::user()->id_opd_fk)->get();
            $nama_opd = cek_opd(Auth::user()->id_opd_fk)->nama_opd;


            $data = [
                'suratmasuk' => $data_surat_masuk,
//                'suratlangsung' => $data_surat_langsung,
//                'suratrahasia' => $data_surat_rahasia,
                'suratkeluarbasah' => $data_surat_keluar_basah,
                'suratkeluartte' => $data_surat_keluar_tte,
                'listPerangkat' => PerangkatDaerah::select('id_opd', 'nama_opd')->get(),
                'listSMbiasa' => $listSMbiasa,
//                'listSMrahasia' => $listSMrahasia,
//                                'listSMlangsung' => $listSMlangsung,
                'listSK' => $listSK,
                'nama_opd' => $nama_opd,
            ];
            return view('dashboard_page.dashboarduser', $data);
        }
    }

    public function statistik(Request $request)
    {

        $suratmasuk = SuratMasuk::where('sifat_surat', 'biasa');
//        $suratrahasia = SuratMasuk::where('sifat_surat', 'rahasia');
//        $suratlangsung = SuratMasuk::where('sifat_surat', 'langsung');
        $suratkeluar = SuratKeluar::where('status_sk', 'final');
        $tahun = $request->get('tahun');
        $namaperangkat = $request->get('namaperangkat');
        if ($tahun != '') {
            $suratmasuk->whereYear('tgl_surat', $tahun);
//            $suratrahasia->whereYear('tgl_surat', $tahun);
//            $suratlangsung->whereYear('tgl_surat', $tahun);
            $suratkeluar->whereYear('tgl_surat', $tahun);
        }


        $perangkat = $request->get('perangkat');

        if ($perangkat != '') {
            $cekDataIdSuratMasuk = Disposisi::where('penerima', $perangkat)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $suratmasuk->whereIn('id', $cekDataIdSuratMasuk);
//            $suratrahasia->whereIn('id', $cekDataIdSuratMasuk);
//            $suratlangsung->whereIn('id', $cekDataIdSuratMasuk);
            $suratkeluar->where('id_opd_fk', $perangkat);
        }

        $cloning_suratmasuk1 = clone $suratmasuk;
        $cloning_suratmasuk2 = clone $suratmasuk;
        $cloning_suratmasuk3 = clone $suratmasuk;
        $cloning_suratmasuk4 = clone $suratmasuk;
        $cloning_suratmasuk5 = clone $suratmasuk;
        $cloning_suratmasuk6 = clone $suratmasuk;
        $cloning_suratmasuk7 = clone $suratmasuk;
        $cloning_suratmasuk8 = clone $suratmasuk;
        $cloning_suratmasuk9 = clone $suratmasuk;
        $cloning_suratmasuk10 = clone $suratmasuk;
        $cloning_suratmasuk11 = clone $suratmasuk;
        $cloning_suratmasuk12 = clone $suratmasuk;

//        $cloning_suratlangsung1 = clone $suratlangsung;
//        $cloning_suratlangsung2 = clone $suratlangsung;
//        $cloning_suratlangsung3 = clone $suratlangsung;
//        $cloning_suratlangsung4 = clone $suratlangsung;
//        $cloning_suratlangsung5 = clone $suratlangsung;
//        $cloning_suratlangsung6 = clone $suratlangsung;
//        $cloning_suratlangsung7 = clone $suratlangsung;
//        $cloning_suratlangsung8 = clone $suratlangsung;
//        $cloning_suratlangsung9 = clone $suratlangsung;
//        $cloning_suratlangsung10 = clone $suratlangsung;
//        $cloning_suratlangsung11 = clone $suratlangsung;
//        $cloning_suratlangsung12 = clone $suratlangsung;
//
//        $cloning_suratrahasia1 = clone $suratrahasia;
//        $cloning_suratrahasia2 = clone $suratrahasia;
//        $cloning_suratrahasia3 = clone $suratrahasia;
//        $cloning_suratrahasia4 = clone $suratrahasia;
//        $cloning_suratrahasia5 = clone $suratrahasia;
//        $cloning_suratrahasia6 = clone $suratrahasia;
//        $cloning_suratrahasia7 = clone $suratrahasia;
//        $cloning_suratrahasia8 = clone $suratrahasia;
//        $cloning_suratrahasia9 = clone $suratrahasia;
//        $cloning_suratrahasia10 = clone $suratrahasia;
//        $cloning_suratrahasia11 = clone $suratrahasia;
//        $cloning_suratrahasia12 = clone $suratrahasia;

        $cloning_suratkeluar1 = clone $suratkeluar;
        $cloning_suratkeluar2 = clone $suratkeluar;
        $cloning_suratkeluar3 = clone $suratkeluar;
        $cloning_suratkeluar4 = clone $suratkeluar;
        $cloning_suratkeluar5 = clone $suratkeluar;
        $cloning_suratkeluar6 = clone $suratkeluar;
        $cloning_suratkeluar7 = clone $suratkeluar;
        $cloning_suratkeluar8 = clone $suratkeluar;
        $cloning_suratkeluar9 = clone $suratkeluar;
        $cloning_suratkeluar10 = clone $suratkeluar;
        $cloning_suratkeluar11 = clone $suratkeluar;
        $cloning_suratkeluar12 = clone $suratkeluar;


        $sm_01 = $cloning_suratmasuk1->whereMonth('tgl_surat', '01')->count();
        $sm_02 = $cloning_suratmasuk2->whereMonth('tgl_surat', '02')->count();
        $sm_03 = $cloning_suratmasuk3->whereMonth('tgl_surat', '03')->count();
        $sm_04 = $cloning_suratmasuk4->whereMonth('tgl_surat', '04')->count();
        $sm_05 = $cloning_suratmasuk5->whereMonth('tgl_surat', '05')->count();
        $sm_06 = $cloning_suratmasuk6->whereMonth('tgl_surat', '06')->count();
        $sm_07 = $cloning_suratmasuk7->whereMonth('tgl_surat', '07')->count();
        $sm_08 = $cloning_suratmasuk8->whereMonth('tgl_surat', '08')->count();
        $sm_09 = $cloning_suratmasuk9->whereMonth('tgl_surat', '09')->count();
        $sm_10 = $cloning_suratmasuk10->whereMonth('tgl_surat', '10')->count();
        $sm_11 = $cloning_suratmasuk11->whereMonth('tgl_surat', '11')->count();
        $sm_12 = $cloning_suratmasuk12->whereMonth('tgl_surat', '12')->count();

//        $sl_01 = $cloning_suratlangsung1->whereMonth('tgl_surat', '01')->count();
//        $sl_02 = $cloning_suratlangsung2->whereMonth('tgl_surat', '02')->count();
//        $sl_03 = $cloning_suratlangsung3->whereMonth('tgl_surat', '03')->count();
//        $sl_04 = $cloning_suratlangsung4->whereMonth('tgl_surat', '04')->count();
//        $sl_05 = $cloning_suratlangsung5->whereMonth('tgl_surat', '05')->count();
//        $sl_06 = $cloning_suratlangsung6->whereMonth('tgl_surat', '06')->count();
//        $sl_07 = $cloning_suratlangsung7->whereMonth('tgl_surat', '07')->count();
//        $sl_08 = $cloning_suratlangsung8->whereMonth('tgl_surat', '08')->count();
//        $sl_09 = $cloning_suratlangsung9->whereMonth('tgl_surat', '09')->count();
//        $sl_10 = $cloning_suratlangsung10->whereMonth('tgl_surat', '10')->count();
//        $sl_11 = $cloning_suratlangsung11->whereMonth('tgl_surat', '11')->count();
//        $sl_12 = $cloning_suratlangsung12->whereMonth('tgl_surat', '12')->count();
//
//        $sr_01 = $cloning_suratrahasia1->whereMonth('tgl_surat', '01')->count();
//        $sr_02 = $cloning_suratrahasia2->whereMonth('tgl_surat', '02')->count();
//        $sr_03 = $cloning_suratrahasia3->whereMonth('tgl_surat', '03')->count();
//        $sr_04 = $cloning_suratrahasia4->whereMonth('tgl_surat', '04')->count();
//        $sr_05 = $cloning_suratrahasia5->whereMonth('tgl_surat', '05')->count();
//        $sr_06 = $cloning_suratrahasia6->whereMonth('tgl_surat', '06')->count();
//        $sr_07 = $cloning_suratrahasia7->whereMonth('tgl_surat', '07')->count();
//        $sr_08 = $cloning_suratrahasia8->whereMonth('tgl_surat', '08')->count();
//        $sr_09 = $cloning_suratrahasia9->whereMonth('tgl_surat', '09')->count();
//        $sr_10 = $cloning_suratrahasia10->whereMonth('tgl_surat', '10')->count();
//        $sr_11 = $cloning_suratrahasia11->whereMonth('tgl_surat', '11')->count();
//        $sr_12 = $cloning_suratrahasia12->whereMonth('tgl_surat', '12')->count();

        $sk_01 = $cloning_suratkeluar1->whereMonth('tgl_surat', '01')->count();
        $sk_02 = $cloning_suratkeluar2->whereMonth('tgl_surat', '02')->count();
        $sk_03 = $cloning_suratkeluar3->whereMonth('tgl_surat', '03')->count();
        $sk_04 = $cloning_suratkeluar4->whereMonth('tgl_surat', '04')->count();
        $sk_05 = $cloning_suratkeluar5->whereMonth('tgl_surat', '05')->count();
        $sk_06 = $cloning_suratkeluar6->whereMonth('tgl_surat', '06')->count();
        $sk_07 = $cloning_suratkeluar7->whereMonth('tgl_surat', '07')->count();
        $sk_08 = $cloning_suratkeluar8->whereMonth('tgl_surat', '08')->count();
        $sk_09 = $cloning_suratkeluar9->whereMonth('tgl_surat', '09')->count();
        $sk_10 = $cloning_suratkeluar10->whereMonth('tgl_surat', '10')->count();
        $sk_11 = $cloning_suratkeluar11->whereMonth('tgl_surat', '11')->count();
        $sk_12 = $cloning_suratkeluar12->whereMonth('tgl_surat', '12')->count();

        $data = [
            'sm_01' => $sm_01,
            'sm_02' => $sm_02,
            'sm_03' => $sm_03,
            'sm_04' => $sm_04,
            'sm_05' => $sm_05,
            'sm_06' => $sm_06,
            'sm_07' => $sm_07,
            'sm_08' => $sm_08,
            'sm_09' => $sm_09,
            'sm_10' => $sm_10,
            'sm_11' => $sm_11,
            'sm_12' => $sm_12,

//            'sl_01' => $sl_01,
//            'sl_02' => $sl_02,
//            'sl_03' => $sl_03,
//            'sl_04' => $sl_04,
//            'sl_05' => $sl_05,
//            'sl_06' => $sl_06,
//            'sl_07' => $sl_07,
//            'sl_08' => $sl_08,
//            'sl_09' => $sl_09,
//            'sl_10' => $sl_10,
//            'sl_11' => $sl_11,
//            'sl_12' => $sl_12,
//
//            'sr_01' => $sr_01,
//            'sr_02' => $sr_02,
//            'sr_03' => $sr_03,
//            'sr_04' => $sr_04,
//            'sr_05' => $sr_05,
//            'sr_06' => $sr_06,
//            'sr_07' => $sr_07,
//            'sr_08' => $sr_08,
//            'sr_09' => $sr_09,
//            'sr_10' => $sr_10,
//            'sr_11' => $sr_11,
//            'sr_12' => $sr_12,

            'sk_01' => $sk_01,
            'sk_02' => $sk_02,
            'sk_03' => $sk_03,
            'sk_04' => $sk_04,
            'sk_05' => $sk_05,
            'sk_06' => $sk_06,
            'sk_07' => $sk_07,
            'sk_08' => $sk_08,
            'sk_09' => $sk_09,
            'sk_10' => $sk_10,
            'sk_11' => $sk_11,
            'sk_12' => $sk_12,

            'tahun' => $tahun,
            'namaperangkat' => $namaperangkat,

        ];

        return view('dashboard_page.statistik', $data)->render();


    }
}
