<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationDokumenTTE;
use App\Models\DokumenTTE;
use App\Models\JenisPenandatangan;
use App\Models\Visualisasi;
use App\Models\Opd;
use App\Models\PerangkatDaerah;
use App\Models\User;
use App\Services\DokumenTTEServices;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use QrCode;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Vinkla\Hashids\Facades\Hashids;


class DokumenTTEController extends Controller
{
    public function index()
    {
        $data = [
            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get()
        ];
        return view('dashboard_page.dokumentte.index', $data);
    }

    public function data(Request $request)
    {
        $data = DokumenTTE::opd();
        $id_jenis_ttd = $request->get('id_jenis_ttd');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        $tampilkan = $request->get('tampilkan');


        $id_opd_fk = $request->get('id_opd_fk');
        if ($id_opd_fk) :
            $data = $data->where('tbl_dokumen_tte.id_opd_fk', $id_opd_fk);
        endif;

        if ($tampilkan) :
            $data = $data->where('tbl_dokumen_tte.status_dokumen', $tampilkan);
        endif;


        if ($id_jenis_ttd) :
            $data = $data->where('tbl_dokumen_tte.id_jenis_ttd_fk', $id_jenis_ttd);
        endif;


        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_dokumen', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                return $checkbox;
            })
            ->editColumn('id', function ($row) {
                return Hashids::encode($row->id);
            })
            ->editColumn('no_dokumen', function ($row) {
                $no_dokumen = '<label class="font-weight-bolder">' . $row->no_dokumen . '</label>';
                return $no_dokumen;
            })
            ->editColumn('tgl_dokumen', function ($row) {
                return $row->tgl_dokumen ? tanggalIndo($row->tgl_dokumen) : '';
            })
            ->editColumn('jenis_ttd', function ($row) {
                return $row->jenis_ttd;
            })
            ->editColumn('status_dokumen', function ($row) {

                if ($row->status_dokumen == 'draft') {

                    $statusnya = '<div class="p-1 color bg-danger text-white"> BELUM DITANDATANGANI SECARA ELEKTRONIK</div>';

                } else {

                    $statusnya = '<div class="p-1 color bg-success text-white"> SUDAH DITANDATANGANI SECARA ELEKTRONIK</div>';

                }

                return $statusnya;

            })
            ->editColumn('qrcode', function ($row) {
                $qrcode = $row->qrcode ? url('kodeqr/' . $row->qrcode) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $qrcode . '"><img src="' . $qrcode . '" height="25px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="First group">';

                $btndetail = '<a href="' . url('dokumen-tte/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="Detail"><i class="fa fa-eye"></i></a>';
                $btnedit = '<a href="' . url('dashboard/dokumen-tte/edit/' . Hashids::encode($row->id)) . '" class="btn btn-sm btn-icon btn-success waves-effect" title="Edit"><i class="fa fa-edit"></i></a>';
                $btnhapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></a>';

                if (in_array(Auth::user()->level, ['admin', 'superadmin'])) {
                    $btn .= $btndetail;

                    $btn .= $btnedit . $btnhapus;

                } else if (in_array(Auth::user()->level, ['sespri', 'adpim', 'umum'])) {
                    $btn .= $btndetail;
                    if ($row->status_dokumen == 'draft') {
                        $btn .= $btnedit . $btnhapus;
                    }
                } else {
                    if (Auth::user()->level == 'penandatangan') {
                        $btnView = '<a href="' . url('dokumen-tte/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="View"><i class="fa fa-check-double"></i> Lihat Dokumen</a>';
                        if ($row->status_dokumen == 'draft') {

                            $btn .= '<a href="' . url('dashboard/dokumen-tte/tanda-tangan/' . Hashids::encode($row->id)) . '" class="btn btn-sm btn-icon btn-danger waves-effect" title="TTanda Tangani"><i class="fa fa-signature"></i> Tanda Tangani</a>';

                        } else {
                            $btn .= $btnView;
                        }
                    } else {
                        $btn .= $btndetail;
                    }
                }
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }


    public function destroy($id)
    {
        $this->destroyBerkas($id, DokumenTTE::class, 'berkas', 'berkas', 'berkas/temp');
        $this->destroyFunction($id, DokumenTTE::class, 'qrcode', 'no_dokumen', 'Dokumen TTE', 'kodeqr', '');
        if (true):
            return Respon('', true, 'Berhasil menghapus data', 200);
        else:
            return Respon('', false, 'Gagal menghapus data', 500);
        endif;
    }

    public function bulkDelete(Request $request)
    {
        $list_id = $request->input('id');
        foreach ($list_id as $id) {
            $this->destroyBerkas($id, DokumenTTE::class, 'berkas', 'berkas', 'berkas/temp');
            $this->destroyFunction($id, DokumenTTE::class, 'qrcode', 'no_dokumen', 'Dokumen TTE', 'kodeqr', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/dokumen-tte/create'),
                'no_dokumen' => old('no_dokumen') ? old('no_dokumen') : '',
                'tgl_dokumen' => old('tgl_dokumen') ? old('tgl_dokumen') : date('d/m/Y'),
                'perihal' => old('perihal') ? old('perihal') : '',
                'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : '',
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : '',
                'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                'qrcode' => '',
                'berkas' => '',
                'status_dokumen' => 'draft',

            ];
        return view('dashboard_page.dokumentte.form', $data);
    }

    public function create(Request $request, DokumenTTEServices $dokumenTTEServices)
    {

        $makeHash = random_strings(10);
        $hash = $makeHash;
        $rule = DokumenTTE::$validationRule;
        //$rule['no_dokumen'] = 'required|unique:tbl_dokumen_tte,no_dokumen';
        $rule['no_dokumen'] = 'required';

        $rule['berkas'] = 'required|max:2048|mimes:pdf';


        $this->validate($request,
            $rule,
            [],
            DokumenTTE::$attributeRule,
        );


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');

            $this->validate($request, [
                'x' => 'required',
                'y' => 'required',
                'width' => 'required',
                'height' => 'required',
                'halaman' => 'required',
            ], [], [
                'x' => 'Koordinat x',
                'y' => 'Koordinat y',
                'width' => 'Lebar TTE',
                'height' => 'Tinggi TTE',
                'halaman' => 'Halaman TTE',
            ]);
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));

        } else {
            $berkas = null;
        }


        $status_dokumen = 'draft';


        $dataCreate = array(
            'no_dokumen' => $request->input('no_dokumen'),
            'tgl_dokumen' => ubahformatTgl($request->input('tgl_dokumen')),

            'perihal' => $request->input('perihal'),

            'id_jenis_ttd_fk' => $request->input('id_jenis_ttd_fk'),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'id_visualisasi' => $request->input('visualisasi_tte'),

            'status_dokumen' => $status_dokumen,
            'berkas' => $berkas,
            'hash' => $hash,
        );

        $dataCreate['x'] = $request->x;
        $dataCreate['y'] = $request->y;
        $dataCreate['halaman'] = $request->halaman;
        $dataCreate['width'] = $request->width;
        $dataCreate['height'] = $request->height;


        $insert = DokumenTTE::create($dataCreate);

        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('dokumen-tte/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = DokumenTTE::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);

        $dokumenTTEServices->placeQRtoPDF($berkas, $nameFile, $insert);
        //Bg Service send emailnya

        SendNotificationDokumenTTE::dispatchAfterResponse($insert);


        if ($insert) :
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            if ($id_jenis_ttd_fk) {
                $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
                $titlefb = 'Dokumen TTE dengan No ' . $request->input('no_dokumen');
                $bodyfb = 'Perlu ditandatangi secara elektronik';
                sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
            }
            saveLogs('menambahkan data ' . $request->input('no_dokumen') . ' pada fitur dokumen TTE');
            return redirect(route('dokumen-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'dokumen TTE berhasil ditambahkan',
                    'judul' => 'data dokumen TTE'
                ]);
        else :
            return redirect(route('dokumen-tte.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'dokumen TTE gagal ditambahkan',
                    'judul' => 'Data dokumen TTE'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = DokumenTTE::find(Hashids::decode($id)[0]);
        if ($checkData) :
            $dataMaster = $checkData;
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/dokumen-tte/update/' . $id),
                    'no_dokumen' => old('no_dokumen') ? old('no_dokumen') : $dataMaster->no_dokumen,
                    'tgl_dokumen' => old('tgl_dokumen') ? old('tgl_dokumen') : TanggalIndo2($dataMaster->tgl_dokumen),
                    'perihal' => old('perihal') ? old('perihal') : $dataMaster->perihal,
                    'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : $dataMaster->id_jenis_ttd_fk,
                    'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : $dataMaster->id_opd_fk,
                    'visualisasi_tte' => old('visualisasi_tte') ? old('visualisasi_tte') : $dataMaster->id_visualisasi,

                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                    'status_dokumen' => $dataMaster->status_dokumen,
                    'dataMaster' => $dataMaster
                ];
            return view('dashboard_page.dokumentte.form', $data);
        else :
            return redirect(route('dokumen-tte'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Dokumen TTE tidak ditemukan',
                    'judul' => 'Halaman Dokumen TTE'
                ]);
        endif;
    }

    public function update($id, Request $request, DokumenTTEServices $dokumenTTEServices)
    {
        $rule = DokumenTTE::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = DokumenTTE::find($idDecode);

        //$rule['no_dokumen'] = 'required|unique:tbl_dokumen_tte,no_dokumen,' . $idDecode . ',id';
        $rule['no_dokumen'] = 'required';

        if ($dataMaster['status_dokumen'] == 'draft') {
            $rule['id_jenis_ttd_fk'] = 'required';
            $rule['visualisasi_tte'] = 'required';

        }


        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:2048|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            DokumenTTE::$attributeRule,
        );
        $status_dokumen = $dataMaster['status_dokumen'];
        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');

            if ($status_dokumen == 'draft') {
                $this->validate($request, [
                    'x' => 'required',
                    'y' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'halaman' => 'required',
                ], [], [
                    'x' => 'Koordinat x',
                    'y' => 'Koordinat y',
                    'width' => 'Lebar TTE',
                    'height' => 'Tinggi TTE',
                    'halaman' => 'Halaman TTE',
                ]);
                $koor = [
                    'x' => $request->x,
                    'y' => $request->y,
                    'halaman' => $request->halaman,
                    'width' => $request->width,
                    'height' => $request->height
                ];

                $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));

                $this->deleteFile('berkas/temp', $dataMaster['berkas']);

                $dokumenTTEServices->placeQRtoPDF($berkas, $dataMaster->qrcode, $dataMaster);
            } else {
                $berkas = $dataMaster->berkas;
            }


        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :
                if ($status_dokumen == 'draft') {


                    $this->deleteFile('berkas/temp', $dataMaster['berkas']);

                    $berkas = '';
                } else {
                    $berkas = $dataMaster->berkas;
                }
            else :
                $berkas = $dataMaster->berkas;
            endif;
        }

        $dataUpdate = [
            'no_dokumen' => $request->input('no_dokumen'),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'tgl_dokumen' => ubahformatTgl($request->input('tgl_dokumen')),
            'perihal' => $request->input('perihal'),
            'berkas' => $berkas,
            'status_dokumen' => $status_dokumen,
        ];
        if (isset($koor))
            $dataUpdate = array_merge($dataUpdate, $koor);

        if ($dataMaster['status_dokumen'] == 'draft') {
            $dataUpdate['id_jenis_ttd_fk'] = $request->input('id_jenis_ttd_fk');
            $dataUpdate['id_visualisasi'] = $request->input('visualisasi_tte');

        }
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $request->input('no_dokumen') . ' pada fitur dokumen TTE');
            return redirect(route('dokumen-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Dokumen TTE berhasil diupdate',
                    'judul' => 'Data Dokumen TTE'
                ]);
        else :
            return redirect(url('dashboard/dokumen-tte/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Dokumen TTE gagal diupdate',
                    'judul' => 'Data Dokumen TTE'
                ]);
        endif;
    }

    public function show($id)
    {
        $checkData = DokumenTTE::where('hash', $id)->first();
        if ($checkData) :
            $dataMaster = $checkData;
            $data =
                [
                    'no_dokumen' => $dataMaster->no_dokumen,
                    'tgl_dokumen' => TanggalIndo2($dataMaster->tgl_dokumen),
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => $dataMaster->opdrelasi->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'status_dokumen' => $dataMaster->status_dokumen,
                ];
            return view('dashboard_page.dokumentte.show', $data);
        else :
            return redirect(route('dokumen-tte'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Dokumen TTE tidak ditemukan',
                    'judul' => 'Halaman Dokumen TTE'
                ]);
        endif;
    }


    public function tanda_tangan($id)
    {
        $checkData = DokumenTTE::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/dokumen-tte/update-tanda-tangan'),
                    'id' => $id,
                    'no_dokumen' => $dataMaster->no_dokumen,
                    'tgl_dokumen' => TanggalIndo2($dataMaster->tgl_dokumen),
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => $dataMaster->opdrelasi->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                ];
            return view('dashboard_page.dokumentte.formpenandatangan', $data);
        else :
            return redirect(route('dokumen-tte'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Dokumen TTE tidak ditemukan',
                    'judul' => 'Halaman Dokumen TTE'
                ]);
        endif;
    }

    public function tanda_tangan_front($id)
    {
        $checkData = DokumenTTE::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('update-ttd-dokumen'),
                    'id' => $id,
                    'no_dokumen' => $dataMaster->no_dokumen,
                    'tgl_dokumen' => TanggalIndo2($dataMaster->tgl_dokumen),
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => $dataMaster->opdrelasi->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                ];
            return view('dashboard_page.dokumentte.formpenandatanganfront', $data);
        else :
            return Redirect::back()
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Dokumen TTE tidak ditemukan',
                    'judul' => 'Halaman Dokumen TTE'
                ]);
        endif;
    }

    public function update_tanda_tangan(Request $request)
    {
        $id = $request->input('id');
        $validationRule = [
            'nik' => 'required',
            'passphrase' => 'required',
        ];
        $rule = $validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = DokumenTTE::find($idDecode);

        $this->validate($request,
            $rule,
            [],
            DokumenTTE::$attributeRule,
        );


//        if (file_exists('berkas/temp/' . $dataMaster['berkas']) && $dataMaster['berkas']) :
//            move_uploaded_file('berkas/temp/' . $dataMaster['berkas'], 'berkas/' . $dataMaster['berkas']);
//        endif;

        //$filePDF = url('berkas/temp/' . $dataMaster['berkas']);


        try {
            $filePDF = Storage::disk('berkas')->path('temp/' . $dataMaster['berkas']);

            $dataJenisTTD = JenisPenandatangan::where('id_jenis_ttd', $dataMaster['id_jenis_ttd_fk'])->first();
            $dataVisualisasi = Visualisasi::where('id', $dataMaster['id_visualisasi'])->first();

            if ($dataVisualisasi != null) {
                $fileImage = Storage::disk('uploads')->path($dataVisualisasi->img_visualisasi);
            } else {
                $fileImage = Storage::disk('uploads')->path('lampung_gray.png');
            }
            $response = Http::attach('imageTTD', fopen($fileImage, 'r'))
                ->attach('file', fopen($filePDF, 'r'))
//                ->withBasicAuth(env('BASIC_AUTH_USER'), env('BASIC_AUTH_PASSWORD'))->post('http://esign-srv.lampungprov.go.id/api/sign/pdf', [
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
                $responseBodyAsString = json_decode($response->getBody()->getContents());
                DB::table('error_log')->insert([
                    'status_code' => $responseBodyAsString->status_code,
                    'description_log' => $responseBodyAsString->error,
                    'time_log' => date('Y-m-d H-i-s'),
                    'caused_by' => Auth::user()->id,
                ]);

                // dd(json_decode($responseBodyAsString)->status_code);

                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => $responseBodyAsString->error,
                        'judul' => 'Tanda Tangan Digital Gagal'
                    ]);
            }

            $nameFile = 'signed_' . $dataMaster['berkas'];
            Storage::disk('berkas')->put('/' . $nameFile, $response->body());
            $dataUpdate = [
                'berkas' => $nameFile,
                'status_dokumen' => 'final',
            ];


            $update = $dataMaster->update($dataUpdate);

            if ($update) :
                saveLogs('menandatangani data ' . $request->input('no_dokumen') . ' pada fitur dokumen TTE');
                return redirect(route('dokumen-tte'))
                    ->with('pesan_status', [
                        'tipe' => 'success',
                        'desc' => 'Dokumen berhasil di tandatangani secara elektronik',
                        'judul' => 'Data Dokumen TTE'
                    ]);
            else :
                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => 'Dokumen gagal di tandatangani secara elektronik',
                        'judul' => 'Data Dokumen TTE'
                    ]);
            endif;
        } catch (BadRequestException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            return Redirect::back()
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => $responseBodyAsString,
                    'judul' => 'Tanda Tangan Digital Gagal'
                ]);
        }


        //dd($response->body());


//

    }

    public function update_tanda_tangan_front(Request $request)
    {
        $id = $request->input('id');
        $validationRule = [
            'nik' => 'required',
            'passphrase' => 'required',
        ];
        $rule = $validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = DokumenTTE::find($idDecode);

        $this->validate($request,
            $rule,
            [],
            DokumenTTE::$attributeRule,
        );


//        if (file_exists('berkas/temp/' . $dataMaster['berkas']) && $dataMaster['berkas']) :
//            move_uploaded_file('berkas/temp/' . $dataMaster['berkas'], 'berkas/' . $dataMaster['berkas']);
//        endif;

        //$filePDF = url('berkas/temp/' . $dataMaster['berkas']);


        try {
            $filePDF = Storage::disk('berkas')->path('temp/' . $dataMaster['berkas']);

            $dataJenisTTD = JenisPenandatangan::where('id_jenis_ttd', $dataMaster['id_jenis_ttd_fk'])->first();
            $dataVisualisasi = Visualisasi::where('id', $dataMaster['id_visualisasi'])->first();

            if ($dataVisualisasi != null) {
                $fileImage = Storage::disk('uploads')->path($dataVisualisasi->img_visualisasi);
            } else {
                $fileImage = Storage::disk('uploads')->path('lampung_gray.png');
            }
            $response = Http::attach('imageTTD', fopen($fileImage, 'r'))
                ->attach('file', fopen($filePDF, 'r'))
//                ->withBasicAuth(env('BASIC_AUTH_USER'), env('BASIC_AUTH_PASSWORD'))->post('http://esign-srv.lampungprov.go.id/api/sign/pdf', [
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
                $responseBodyAsString = json_decode($response->getBody()->getContents())->error;
                // dd(json_decode($responseBodyAsString)->error);
                DB::table('error_log')->insert([
                    'status_code' => $responseBodyAsString->status_code,
                    'description_log' => $responseBodyAsString->error,
                    'time_log' => date('Y-m-d H-i-s'),
//                    'caused_by' => Auth::user()->id,
                ]);
                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => $responseBodyAsString,
                        'judul' => 'Tanda Tangan Digital Gagal'
                    ]);
            }

            $nameFile = 'signed_' . $dataMaster['berkas'];
            Storage::disk('berkas')->put('/' . $nameFile, $response->body());
            $dataUpdate = [
                'berkas' => $nameFile,
                'status_dokumen' => 'final',
            ];


            $update = $dataMaster->update($dataUpdate);

            if ($update) :
                saveLogs('menandatangani data ' . $request->input('no_dokumen') . ' pada fitur dokumen TTE');
                return redirect(url('dokumen-tte/' . $id))
                    ->with('pesan_status', [
                        'tipe' => 'success',
                        'desc' => 'Dokumen berhasil ditandatangani secara elektronik',
                        'judul' => 'Data Dokumen TTE'
                    ]);
            else :
                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => 'Dokumen gagal ditandatangani secara elektronik',
                        'judul' => 'Data Dokumen TTE'
                    ]);
            endif;
        } catch (BadRequestException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return Redirect::back()
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => $responseBodyAsString,
                    'judul' => 'Tanda Tangan Digital Gagal'
                ]);
        }


        //dd($response->body());


//

    }

    public function getTtdImage(Request $request)
    {
        $id = $request->id;

        $ttd = JenisPenandatangan::find($id);
        return response()->json(url('uploads/' . $ttd->img_ttd), 200);
    }


    public function verifikasiTTE()
    {
        return view('dashboard_page.dokumentte.indexverifikasi');
    }

    public function ApiVerifikasi(Request $request)
    {

        //validasi
        $data = [];
        $data['error_string'] = [];
        $data['inputerror'] = [];

        $rule['signed_file'] = 'mimes:pdf';

        $validator = Validator::make($request->all(),
            $rule,
            [],
        );


        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->messages() as $key => $value) {
                $data['inputerror'][] = $key;
                $data['error_string'][] = $value[0];
            }

            $data['status'] = false;
            if ($data['status'] === false) {
                echo json_encode($data);
                exit();
            }
        } else {
            if ($request->hasFile('signed_file')) {
                $berkasFile = $request->file('signed_file');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp');

            } else {
                $berkas = null;
            }
            $filePDF = Storage::disk('berkas')->path('temp/' . $berkas);
            try {
                $response = Http::attach('signed_file', fopen($filePDF, 'r'))->withBasicAuth(env('BASIC_AUTH_USER'), env('BASIC_AUTH_PASSWORD'))->post(env('URLESIGN') . '/api/sign/verify', [

                    [
                        'name' => 'signed_file',
                        'contents' => $request->file('signed_file'),
                    ],

                ]);
                $this->deleteFile('berkas/temp', $berkas);

            } catch (BadRequestException $e) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();

            }

//            if ($response->status() == 400) {
//                $responseBodyAsString = json_decode($response->getBody()->getContents())->error;
//                // dd(json_decode($responseBodyAsString)->error);
//                return $response;
//            }

            return $response;


        }
    }

}
