<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DokumenTTE;
use App\Models\JenisPenandatangan;
use App\Models\Visualisasi;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Vinkla\Hashids\Facades\Hashids;


class DokumenTTEController extends Controller
{

    public function data_dokumen_tte(Request $request)
    {

        //$data = DokumenTTE::select('id', 'no_dokumen', 'id_opd_fk', 'tgl_dokumen', 'tujuan', 'kepada', 'kepada_opd', 'lampiran', 'perihal', 'id_jenis_ttd_fk', 'berkas', 'qrcode')->where('kategori_ttd', 'elektronik');
        $data = DokumenTTE::opd();
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        $status_dokumen = ($request->get('status_dokumen'));

        $data = $data->where('tbl_dokumen_tte.status_dokumen', $status_dokumen);

        if (Auth::user()->level == 'sespri') {
            $data = $data->where('tbl_dokumen_tte.id_opd_fk', Auth::user()->id_opd_fk);
        }

        if (Auth::user()->level == 'penandatangan') {
            $data = $data->where('tbl_dokumen_tte.id_jenis_ttd_fk', Auth::user()->id_jenis_ttd_fk);
        }

        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_dokumen', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (!$data) {

            $httpStatus = 204;
            $res['message'] = 'Data Dokumen TTE Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $data;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Dokumen TTE Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $list_dokumenkeluar = $data->orderBy('id', 'DESC')->paginate(10);
        $res['data'] = $list_dokumenkeluar;

        $list_dokumenkeluar->map(function ($row) {
            $row->idhash = Hashids::encode($row->id);
            if ($row->id_opd_fk) {
                $row->dari = cek_opd($row->id_opd_fk)->nama_opd;
            } else {
                $row->dari = null;
            }

            $row->tgl_dokumen = TanggalIndo2($row->tgl_dokumen);
            $row->penandatangan = cek_ttd($row->id_jenis_ttd_fk)->jenis_ttd . ' - ' . cek_opd(cek_ttd($row->id_jenis_ttd_fk)->id_opd_fk)->nama_opd;
            if ($row->status_dokumen == 'final') {
                $row->berkas = url('berkas/' . $row->berkas);
            } else {
                $row->berkas = url('berkas/temp/' . $row->berkas);
            }
            $row->qrcode = url('kodeqr/' . $row->qrcode);
            $row->detail = url('api/detail-dokumen-tte/' . Hashids::encode($row->id));
            //$row->aksitandatangan = url('api/tanda-tangani/' . Hashids::encode($row->id));


            return $row;
        });


        return response()->json($res['data'], $httpStatus);
    }

    public function detail($id, Request $request)
    {
        $dataDokumenTTE = DokumenTTE::where('id', Hashids::decode($id))->first();
        if (!$dataDokumenTTE) {
            $httpStatus = 204;
            $res['message'] = 'Data Dokumen TTE Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataDokumenTTE;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Dokumen TTE Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;

        $dari = null;
        if($dataDokumenTTE->id_opd_fk)
        {
            $dari = cek_opd($dataDokumenTTE->id_opd_fk)->nama_opd;
        }

        $tgl_dokumen = TanggalIndo2($dataDokumenTTE->tgl_dokumen);


        $status_dokumen = $dataDokumenTTE->status_dokumen;
        //$berkas = url('berkas/' . $dataDokumenTTE->berkas);

        $berkas = url('berkas/temp/' . $dataDokumenTTE->berkas);
        if ($dataDokumenTTE->status_dokumen == 'final') {
            //$status_dokumen=='sudah di tandangani secara elektronik';
            $berkas = url('berkas/' . $dataDokumenTTE->berkas);
        }


        $hasil = [
            'id' => Hashids::decode($id)[0],
            'idhash' => $id,
            'dari' => $dari,
            'perihal' => $dataDokumenTTE->perihal,
            'no_dokumen' => $dataDokumenTTE->no_dokumen,
            'tgl_dokumen' => $tgl_dokumen,
            'status' => $status_dokumen,
            'qrcode' => url('kodeqr/' . $dataDokumenTTE->qrcode),
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
        $dataMaster = DokumenTTE::find($idDecode);

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
            DokumenTTE::$attributeRule,
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

            $pesan = $namattd . ' berhasil tanda tangan elektronik surat keluar dengan nomor : ' . $dataMaster->no_dokumen;
            $status = 'OK';
            $http = 200;
            $nameFile = 'signed_' . $dataMaster['berkas'];
            Storage::disk('berkas')->put('/' . $nameFile, $response->body());
            $dataUpdate = [
                'berkas' => $nameFile,
                'status_dokumen' => 'final',
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
