<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisPenandatangan;
use App\Models\Visualisasi;
use App\Models\SuratKeluar;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Vinkla\Hashids\Facades\Hashids;


class SuratKeluarController extends Controller
{

    public function data_sk_basah(Request $request)
    {

        $data = SuratKeluar::select('id', 'no_surat', 'id_opd_fk', 'tgl_surat', 'tujuan', 'kepada', 'kepada_opd', 'lampiran', 'perihal', 'id_jenis_ttd_fk', 'berkas', 'qrcode','kategori_ttd','status_sk')->where('kategori_ttd', 'basah')->where('status_sk', 'final');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));

        if (Auth::user()->level == 'sespri') {
            $data = $data->where('tbl_surat_keluar.id_opd_fk', Auth::user()->id_opd_fk);
        }

        if (Auth::user()->level == 'penandatangan') {
            $data = $data->where('tbl_surat_keluar.id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk);
        }

        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Keluar Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Keluar Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_suratkeluar = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_suratkeluar;

        $list_suratkeluar->map(function ($row) {
            $row->idhash = Hashids::encode($row->id);
            $row->dari = cek_opd($row->id_opd_fk)->nama_opd;
            $row->tgl_surat = TanggalIndo2($row->tgl_surat);
            $kepada = $row->kepada;
            if ($row->tujuan == 'dalam' || $row->tujuan == 'keduanya') {
                $kepada = $row->kepada_opd . ';';
                $kepada .= $row->kepada;
            }

            $row->kepada_seluruh = $kepada;
            $row->penandatangan = cek_ttd($row->id_jenis_ttd_fk)->jenis_ttd . ' - ' . cek_opd(cek_ttd($row->id_jenis_ttd_fk)->id_opd_fk)->nama_opd;
            $row->berkas = url('berkas/' . $row->berkas);
            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->detail = url('api/detail-sk/' . Hashids::encode($row->id));


            return $row;
        });


        return response()->json($res['data'], $httpStatus);
    }

    public function data_sk_elektronik(Request $request)
    {

        $data = SuratKeluar::select('id', 'no_surat', 'id_opd_fk', 'tgl_surat', 'tujuan', 'kepada', 'kepada_opd', 'lampiran', 'perihal', 'id_jenis_ttd_fk', 'berkas', 'qrcode','kategori_ttd','status_sk')->where('kategori_ttd', 'elektronik');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        $status_sk = ($request->get('status_sk'));

        $data = $data->where('tbl_surat_keluar.status_sk', $status_sk);

        if (Auth::user()->level == 'sespri') {
            $data = $data->where('tbl_surat_keluar.id_opd_fk', Auth::user()->id_opd_fk);
        }

        if (Auth::user()->level == 'penandatangan') {
            $data = $data->where('tbl_surat_keluar.id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk);
        }

        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Surat Keluar Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Keluar Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_suratkeluar = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_suratkeluar;

        $list_suratkeluar->map(function ($row) {
            $row->idhash = Hashids::encode($row->id);
            $row->dari = cek_opd($row->id_opd_fk)->nama_opd;
            $row->tgl_surat = TanggalIndo2($row->tgl_surat);
            $kepada = $row->kepada;
            if ($row->tujuan == 'dalam' || $row->tujuan == 'keduanya') {
                $kepada = $row->kepada_opd . ';';
                $kepada .= $row->kepada;
            }

            $row->kepada_seluruh = $kepada;
            $row->penandatangan = cek_ttd($row->id_jenis_ttd_fk)->jenis_ttd . ' - ' . cek_opd(cek_ttd($row->id_jenis_ttd_fk)->id_opd_fk)->nama_opd;
            if($row->status_sk=='final')
            {
                $row->berkas = url('berkas/' . $row->berkas);
            }
            else
            {
                $row->berkas = url('berkas/temp/' . $row->berkas);
            }

            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->detail = url('api/detail-sk/' . Hashids::encode($row->id));
            //$row->aksitandatangan = url('api/tanda-tangani/' . Hashids::encode($row->id));


            return $row;
        });


        return response()->json($res['data'], $httpStatus);
    }

    public function detail($id, Request $request)
    {
        $dataSuratKeluar = SuratKeluar::where('id', Hashids::decode($id))->first();
        if (!$dataSuratKeluar) {
            $httpStatus = 204;
            $res['message'] = 'Data Surat Keluar Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataSuratKeluar;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Surat Keluar Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;

        $dari = cek_opd($dataSuratKeluar->id_opd_fk)->nama_opd;
        $tgl_surat = TanggalIndo2($dataSuratKeluar->tgl_surat);


        $kepada = $dataSuratKeluar->kepada;
        if ($dataSuratKeluar->tujuan == 'dalam' || $dataSuratKeluar->tujuan == 'keduanya') {
            $kepada = $dataSuratKeluar->kepada_opd . ';';
            $kepada .= $dataSuratKeluar->kepada;
        }

        $kepada_seluruh = $kepada;

        $status_sk = $dataSuratKeluar->status_sk;
        $berkas = url('berkas/' . $dataSuratKeluar->berkas);

        if ($dataSuratKeluar->kategori_ttd == 'elektronik') {
            if ($dataSuratKeluar->status_sk == 'final') {
                //$status_sk=='sudah di tandangani secara elektronik';
                $berkas = url('berkas/' . $dataSuratKeluar->berkas);
            } else {
                //$status_sk=='belum di tandangani secara elektronik';
                $berkas = url('berkas/temp/' . $dataSuratKeluar->berkas);
            }
        }

        $hasil = [
            'id' => Hashids::decode($id)[0],
            'idhash' => $id,
            'dari' => $dari,
            'kepada' => $kepada_seluruh,
            'perihal' => $dataSuratKeluar->perihal,
            'no_surat' => $dataSuratKeluar->no_surat,
            'tgl_surat' => $tgl_surat,
            'lampiran' => $dataSuratKeluar->lampiran,
            'status' => $status_sk,
            'kategori_ttd' =>$dataSuratKeluar->kategori_ttd,
            'qrcode' => url('kodeqr/' . $dataSuratKeluar->qrcode),
            'berkas' => $berkas,
        ];

        $res['data'] = $hasil;


        return response()->json($res, $httpStatus);

    }

    public function update_tanda_tangan($id, Request $request)
    {
        $validationRule = [
            'nik' => 'required',
            'passphrase' => 'required',
        ];
        $rule = $validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratKeluar::find($idDecode);

        if ($dataMaster->id_jenis_ttd_fk != Auth::user()->id_jenis_ttd_fk) {
            $errorString = 'anda tidak memiliki izin untuk menandatangani dokumen ini secara elektronik';
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 401;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        $validator = Validator::make($request->all(),
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 401;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }


        try {

            DB::beginTransaction();
            $filePDF = Storage::disk('berkas')->path('temp/' . $dataMaster['berkas']);

            $dataJenisTTD = JenisPenandatangan::where('id_jenis_ttd', $dataMaster['id_jenis_ttd_fk'])->first();

$dataVisualisasi = Visualisasi::where('id', $dataMaster['id_visualisasi'])->first();

            if ($dataVisualisasi != null) {
                $fileImage = Storage::disk('uploads')->path($dataVisualisasi->img_visualisasi);
$namattd = $dataJenisTTD->jenis_ttd;

            } else {

                           $fileImage = Storage::disk('uploads')->path('lampung_gray.png');
                $namattd = null;
            }
            $response = Http::attach('imageTTD', fopen($fileImage, 'r'))
                ->attach('file', fopen($filePDF, 'r'))
                ->withBasicAuth(env('BASIC_AUTH_USER'), env('BASIC_AUTH_PASSWORD'))->post(env('URLESIGN') . '/api/sign/pdf', [
                    [
                        'name' => 'nik',
                        'contents' => $request->input('nik')
                    ],
                    [
                        'name' => 'passphrase',
                        'contents' => $request->input('passphrase')
                    ],
                    [
                        'name' => 'tampilan',
                        'contents' => 'visible'
                    ],
                    [
                        'name' => 'page',
                        'contents' => $dataMaster['halaman']
                    ],
                    [
                        'name' => 'image',
                        'contents' => true
                    ],
                    [
                        'name' => 'xAxis',
                        'contents' => $dataMaster['x']
                    ],
                    [
                        'name' => 'yAxis',
                        'contents' => $dataMaster['y']
                    ],
                    [
                        'name' => 'width',
                        'contents' => $dataMaster['width']
                    ],
                    [
                        'name' => 'height',
                        'contents' => $dataMaster['height']
                    ],
                ]);

            if ($response->status() == 400) {
                DB::rollback();
                $responseBodyAsString = json_decode($response->getBody()->getContents())->error;
                $pesan = $responseBodyAsString;
                $status = 'Failed';
                $http = 400;
                $res['message'] = $pesan;
                $res['status'] = $status;
                $res['http_status'] = $http;

                return response()->json($res, $http);
            }

            $pesan = $namattd . ' berhasil tanda tangan elektronik surat keluar dengan nomor : ' . $dataMaster->no_surat;
            $status = 'OK';
            $http = 200;
            $nameFile = 'signed_' . $dataMaster['berkas'];
            Storage::disk('berkas')->put('/' . $nameFile, $response->body());
            $dataUpdate = [
                'berkas' => $nameFile,
                'status_sk' => 'final',
            ];


            $dataMaster->update($dataUpdate);
            DB::commit();
        } catch (BadRequestException $e) {
            DB::rollback();
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $pesan = $responseBodyAsString;
            $status = 'Failed';
            $http = 500;
        }

        $res['message'] = $pesan;
        $res['status'] = $status;
        $res['http_status'] = $http;

        return response()->json($res, $http);


    }

}
