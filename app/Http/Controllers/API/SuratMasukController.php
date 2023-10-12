<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Auth;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;


class SuratMasukController extends Controller
{


    public function data_sm_pimpinan(Request $request)
    {

//        $data = SuratMasuk::select('*')->where('sifat_surat', 'biasa');
//        if (in_array(Auth::user()->level, ['sespri', 'penandatangan'])) {
//            $data = SuratMasuk::select('id', 'no_surat', 'tgl_surat', 'dari', 'perihal', 'kepada', 'berkas', 'qrcode');
//        }
//
        $data = SuratMasuk::select('id', 'no_surat', 'tgl_surat', 'dari', 'perihal', 'kepada', 'lampiran','berkas', 'qrcode')->whereIn('sifat_surat', ['biasa', 'langsung', 'rahasia']);

        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (in_array(Auth::user()->level, ['penandatangan', 'sespri'])) {
            $cekDataIdSuratMasuk = Disposisi::where('penerima', Auth::user()->id_opd_fk)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $data = $data->whereIn('id', $cekDataIdSuratMasuk);
        }

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Pimpinan Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Pimpinan Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_suratmasuk = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_suratmasuk;
        //$res['data'] = $listpcr;
        $list_suratmasuk->map(function ($row) {
            //$row->qrcode = url('kodeqr/' . Hashids::encode($row->id));
            $row->idhash = Hashids::encode($row->id);
            $row->berkas = url('berkas/' . $row->berkas);
            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->kepada = cek_opd($row->kepada)->nama_opd;
            $row->tgl_surat = TanggalIndo2($row->tgl_surat);
            //$row->link_disposisi = url('surat-masuk/' . Hashids::encode($row->id));
            $row->detail = url('api/detail-sm-pimpinan/' . Hashids::encode($row->id));
            return $row;
        });

        //$combineRes = array($res['message'], $res['data']);
        //$combineRes = $res['data'];
//        $res['data']['message'] = 'Data Surat Masuk Ditemukan';
//        $res['data']['status'] = 'OK';
//        $res['data']['http_status'] = $httpStatus;


        return response()->json($res['data'], $httpStatus);

    }

    public function detail_sm_pimpinan($id, Request $request)
    {
        $dataSuratMasuk = SuratMasuk::where('id', Hashids::decode($id))->first();
        if (!$dataSuratMasuk) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Pimpinan Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataSuratMasuk;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Pimpinan Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $hasil = [
            'id' => Hashids::decode($id)[0],
            'idhash' => $id,
            'kode' => $dataSuratMasuk->kode,
            'indek' => $dataSuratMasuk->indek,
            'dari' => $dataSuratMasuk->dari,
            'kepada' => $dataSuratMasuk->kepada ? cek_opd($dataSuratMasuk->kepada)->nama_opd : '',
            'perihal' => $dataSuratMasuk->perihal,
            'no_surat' => $dataSuratMasuk->no_surat,
            'tgl_surat' => TanggalIndo2($dataSuratMasuk->tgl_surat),
            'lampiran' => $dataSuratMasuk->lampiran,
            'qrcode' => url('kodeqr/' . $dataSuratMasuk->qrcode),
            'berkas' => url('berkas/' . $dataSuratMasuk->berkas),
            'sifat_surat' => $dataSuratMasuk->sifat_surat,
        ];

        $res['data'] = $hasil;
        $res['disposisi'] = array();
        $cekdisposisi = Disposisi::where('id_sm_fk', Hashids::decode($id))->orderBy('id', 'DESC');
        $clonecekjumlah = clone $cekdisposisi;
        if ($clonecekjumlah->count() > 0) {
            $clonedisposisi = clone $cekdisposisi;
            $datadisposisi = $clonedisposisi->get();
            foreach ($datadisposisi as $disposisi) {
                $no = 1;
                $status = 'BELUM DITERIMA';
                $string_status = null;
                $melalui = null;
                $catatan = null;

                if ($disposisi->status == 'diteruskan') {

                    if ($disposisi->kepada != '') {
                        $arr_kepada = explode(",", $disposisi->kepada);
                        $kepadaItem = '';
                        foreach ($arr_kepada as $x) {
                            $kepadaItem .= cek_opd($x)->nama_opd . '; ';
                        }
                        $string_status = 'Diteruskan kepada : ' . rtrim($kepadaItem, '; ');
                        $status = 'DITERUSKAN';
                    }


                    if ($disposisi->melalui_id_opd != '') {
                        $melalui = cek_opd($disposisi->melalui_id_opd)->nama_opd;
                    }

                    if ($disposisi->catatan_disposisi != '') {
                        $catatan = $disposisi->catatan_disposisi;
                    }
                } else {
                    $string_status = 'DIOLAH';
                    $status = 'DIOLAH';
                }


                if ($disposisi->tgl_diterima != null) {
                    $tracking[] = [
                        'tgl_diterima' => TanggalIndowaktu($disposisi->tgl_diterima),
                        'diterima_oleh' => cek_opd($disposisi->penerima)->nama_opd . ' a.n. ' . $disposisi->nama_penerima,
                        'status' => $status,
                        'keterangan' => $string_status,
                        'melalui' => $melalui,
                        'catatan' => $catatan,
                    ];
                } else {
                    $tracking[] = [
                        'tgl_diterima' => null,
                        'diterima_oleh' => null,
                        'status' => $status,
                        'keterangan' => 'Surat Dalam Perjalanan ke : ' . cek_opd($disposisi->penerima)->nama_opd,
                        'melalui' => $melalui,
                        'catatan' => $catatan,
                    ];
                }


            }

            $res['disposisi'] = $tracking;
        }

        return response()->json($res, $httpStatus);

    }

    public function data_sm_intern(Request $request)
    {

        $data = SuratMasuk::select('id', 'no_surat', 'tgl_surat', 'dari', 'perihal', 'kepada', 'lampiran','berkas', 'qrcode')->where('sifat_surat', 'pejabat');

        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (in_array(Auth::user()->level, ['penandatangan', 'sespri'])) {
            $data = $data->where('kepada', Auth::user()->id_opd_fk);
        }

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Intern Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Intern Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_suratmasuk = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_suratmasuk;
        //$res['data'] = $listpcr;
        $list_suratmasuk->map(function ($row) {
            //$row->qrcode = url('kodeqr/' . Hashids::encode($row->id));
            $row->idhash = Hashids::encode($row->id);
            $row->berkas = url('berkas/' . $row->berkas);
            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->kepada = cek_opd($row->kepada)->nama_opd;
            $row->tgl_surat = TanggalIndo2($row->tgl_surat);
            $row->detail = url('api/detail-sm-intern/' . Hashids::encode($row->id));
            return $row;
        });


        return response()->json($res['data'], $httpStatus);

    }

    public function detail_sm_intern($id, Request $request)
    {
        $dataSuratMasuk = SuratMasuk::where('sifat_surat', 'pejabat')->where('id', Hashids::decode($id))->first();
        if (!$dataSuratMasuk) {
            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Intern Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataSuratMasuk;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Intern Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $hasil = [
            'id' => Hashids::decode($id)[0],
            'idhash' => $id,
//            'kode' => $dataSuratMasuk->kode,
//            'indek' => $dataSuratMasuk->indek,
            'dari' => $dataSuratMasuk->dari,
            'kepada' => $dataSuratMasuk->kepada ? cek_opd($dataSuratMasuk->kepada)->nama_opd : '',
            'perihal' => $dataSuratMasuk->perihal,
            'no_surat' => $dataSuratMasuk->no_surat,
            'tgl_surat' => TanggalIndo2($dataSuratMasuk->tgl_surat),
            'lampiran' => $dataSuratMasuk->lampiran,
            'qrcode' => url('kodeqr/' . $dataSuratMasuk->qrcode),
            'berkas' => url('berkas/' . $dataSuratMasuk->berkas),
            //'sifat_surat' => $dataSuratMasuk->sifat_surat,
        ];

        $res['data'] = $hasil;


        return response()->json($res, $httpStatus);

    }

    public function data_sm_instansi(Request $request)
    {

        $data = SuratKeluar::select('id', 'no_surat', 'tgl_surat', 'kepada', 'kepada_opd', 'lampiran', 'perihal','berkas','qrcode')->where('status_sk', 'final');
        if (in_array(Auth::user()->level, ['adpim', 'umum', 'sespri', 'penandatangan'])) {
            $search = Auth::user()->id_opd_fk;
            $data = SuratKeluar::select('id', 'no_surat', 'id_opd_fk', 'tgl_surat', 'tujuan', 'kepada', 'kepada_opd', 'lampiran', 'perihal','berkas','qrcode')->where('status_sk', 'final')->whereRaw("find_in_set('" . $search . "',tbl_surat_keluar.kepada_id_opd)");
        }

        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));


        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Instansi Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Instansi Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_suratmasuk = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_suratmasuk;
        //$res['data'] = $listpcr;
        $list_suratmasuk->map(function ($row) {
            //$row->qrcode = url('kodeqr/' . Hashids::encode($row->id));
            $row->idhash = Hashids::encode($row->id);
            $row->dari = cek_opd($row->id_opd_fk)->nama_opd;
            $row->tgl_surat = TanggalIndo2($row->tgl_surat);
            $kepada = $row->kepada;
            if ($row->tujuan == 'dalam' || $row->tujuan == 'keduanya') {
                $kepada = $row->kepada_opd . ';';
                $kepada .= $row->kepada;
            }

            $row->kepada_seluruh = $kepada;
            $row->berkas = url('berkas/' . $row->berkas);
            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->detail = url('api/detail-sm-instansi/' . Hashids::encode($row->id));


            return $row;
        });


        return response()->json($res['data'], $httpStatus);

    }

    public function detail_sm_instansi($id, Request $request)
    {
        $dataSuratMasuk = SuratKeluar::where('status_sk', 'final')->where('id', Hashids::decode($id))->first();
        if (!$dataSuratMasuk) {
            $httpStatus = 204;
            $res['message'] = 'Data Surat Masuk Instansi Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataSuratMasuk;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Masuk Instansi Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;

        $dari = cek_opd($dataSuratMasuk->id_opd_fk)->nama_opd;
        $tgl_surat = TanggalIndo2($dataSuratMasuk->tgl_surat);


        $kepada = $dataSuratMasuk->kepada;
        if ($dataSuratMasuk->tujuan == 'dalam' || $dataSuratMasuk->tujuan == 'keduanya') {
            $kepada = $dataSuratMasuk->kepada_opd . ';';
            $kepada .= $dataSuratMasuk->kepada;
        }

        $kepada_seluruh = $kepada;

        $hasil = [
            'id' => Hashids::decode($id)[0],
            'idhash' => $id,
            'dari' => $dari,
            'kepada' => $kepada_seluruh,
            'perihal' => $dataSuratMasuk->perihal,
            'no_surat' => $dataSuratMasuk->no_surat,
            'tgl_surat' => $tgl_surat,
            'lampiran' => $dataSuratMasuk->lampiran,
            'qrcode' => url('kodeqr/' . $dataSuratMasuk->qrcode),
            'berkas' => url('berkas/' . $dataSuratMasuk->berkas),
        ];

        $res['data'] = $hasil;


        return response()->json($res, $httpStatus);

    }


}
