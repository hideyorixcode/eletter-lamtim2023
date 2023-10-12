<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationSuratKeluar;
use App\Models\JenisPenandatangan;
use App\Models\Opd;
use App\Models\PerangkatDaerah;
use App\Models\SuratKeluar;
use App\Models\User;
use App\Models\Visualisasi;
use App\Services\SuratKeluarServices;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use QrCode;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Vinkla\Hashids\Facades\Hashids;


class SuratKeluarTTEController extends Controller
{
    public function index()
    {
        $data = [
            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('img_ttd', '!=', null)->get()
        ];
        return view('dashboard_page.suratkeluartte.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratKeluar::opd()->where('kategori_ttd', 'elektronik');
        $id_opd_fk = $request->get('id_opd_fk');
        $id_jenis_ttd = $request->get('id_jenis_ttd');
        $tgl_mulai = $request->get('tgl_mulai');
        $tgl_akhir = $request->get('tgl_akhir');
        $tampilkan = $request->get('tampilkan');

        if (in_array(Auth::user()->level, ['adpim'])) {
            $data = $data->where('tbl_surat_keluar.bagikan_tu', 'ya');
        }

        if ($tampilkan) :
            $data = $data->where('tbl_surat_keluar.status_sk', $tampilkan);
        endif;

        if ($id_opd_fk) :
            $data = $data->where('tbl_surat_keluar.id_opd_fk', $id_opd_fk);
        endif;

        if ($id_jenis_ttd) :
            $data = $data->where('tbl_surat_keluar.id_jenis_ttd_fk', $id_jenis_ttd);
        endif;


        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $aksicheckbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                if (in_array(Auth::user()->level, ['adpim', 'admin', 'superadmin'])) {

                    if ($row->status_sk == 'draft') {
                        $checkbox = $aksicheckbox;
                    } else {
                        $checkbox = '';
                    }


                } else {
                    if ($row->status_sk == 'draft') {
                        $checkbox = $aksicheckbox;
                    } else {
                        $checkbox = '';
                    }
                }
                return $checkbox;
            })
            ->editColumn('id', function ($row) {
                return Hashids::encode($row->id);
            })
            ->editColumn('kepada', function ($row) {
                $kepada = $row->kepada;
                if ($row->tujuan == 'dalam' || $row->tujuan == 'keduanya') {
                    $kepada = $row->kepada_opd . ';';
                    $kepada .= $row->kepada;
                    $explode_kepada = explode(';', rtrim($kepada, ';'));
                    $kepada = '';
                    foreach ($explode_kepada as $value) {
                        $kepada .= '- ' . $value . ' <br/>';
                    }
                }
                return $kepada;

            })
            ->editColumn('no_surat', function ($row) {
                if ($row->no_surat) {
                    $no_surat = '<label class="font-weight-bolder">' . $row->no_surat . '</label>';
                } else {
                    $no_surat = '<div class="p-1 color bg-danger text-white"> Belum Diberikan Nomor </div>';
                }
                return $no_surat;
            })
            ->editColumn('tgl_surat', function ($row) {
                return $row->tgl_surat ? tanggalIndo($row->tgl_surat) : '';
            })
            ->editColumn('jenis_ttd', function ($row) {
                if ($row->jenis_ttd) {
                    $ttd = $row->jenis_ttd . ' - ' . $row->nama_opd_penandatangan;
                } else {
                    $ttd = '<div class="p-1 color bg-danger text-white"> Belum Diberikan Penandatangan </div>';
                }
                return $ttd;

            })
            ->editColumn('status_sk', function ($row) {

                if ($row->status_sk == 'draft') {

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
                $btnprint = '<a href="' . url('dashboard/surat-keluar/print/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-sm btn-icon btn-dark waves-effect" title="Print"><i class="fa fa-print"></i></a>';
                $btndetail = '<a href="' . url('surat-keluar/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="Detail"><i class="fa fa-eye"></i></a>';
                $btnedit = '<a href="' . url('dashboard/surat-keluar-tte/edit/' . Hashids::encode($row->id)) . '" class="btn btn-sm btn-icon btn-success waves-effect" title="Edit"><i class="fa fa-edit"></i></a>';
                $btnhapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></a>';

                if (in_array(Auth::user()->level, ['adpim', 'admin', 'superadmin'])) {
                    $btn .= $btndetail;
                    $btn .= $btnprint . $btnedit . $btnhapus;
//                    if ($row->status_sk == 'draft') {
//                        //$btn .= $btnedit . $btnhapus;
//                        $btn .= $btnprint . $btnedit . $btnhapus;
//                    }

                } else {
                    if (Auth::user()->level == 'penandatangan') {
                        $btnView = '<a href="' . url('surat-keluar-tte/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="View"><i class="fa fa-check-double"></i> Lihat Surat</a>';
                        if ($row->status_sk == 'draft') {
                            $btn .= '<a href="' . url('dashboard/surat-keluar-tte/tanda-tangan/' . Hashids::encode($row->id)) . '" class="btn btn-sm btn-icon btn-danger waves-effect" title="Tanda Tangani"><i class="fa fa-signature"></i> Tanda Tangani</a>';

                        } else {
                            $btn .= $btnView;
                        }
                    } else if (Auth::user()->level == 'sespri') {
                        $btn .= $btndetail;
                        if ($row->bagikan_tu == 'ya') {
                            if ($row->status_sk == 'draft' && $row->diisi_oleh == 'perangkat_daerah' && $row->id_opd_fk == Auth::user()->id_opd_fk) {
                                $btn .= $btnedit . $btnhapus;
                            }
                        } else {
                            if ($row->status_sk == 'draft' && $row->diisi_oleh == 'perangkat_daerah' && $row->id_opd_fk == Auth::user()->id_opd_fk) {
                                $btn .= $btnedit . $btnhapus;
                            }
                            //$btn .= $btnedit . $btnhapus;
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

    public function form()
    {
        //dd('hei');
        $id_opd_fix = '';
        $kepada_id_opd = old('kepada_id_opd');
        if (isset($kepada_id_opd)) {
            $id_opd = '';
            for ($x = 0; $x < count($kepada_id_opd); $x++) {
                $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                //$id_opd = $explode_kepada_id_opd[0];
                $id_opd .= $explode_kepada_id_opd[0] . ',';

            }
            $id_opd_fix = rtrim($id_opd, ',');
        }

        if (Auth::user()->level == 'sespri') {
            $data =
                [
                    'mode' => 'tambah',
                    'action' => url('dashboard/surat-keluar-tte/create-from-opd'),
                    'no_surat' => old('no_surat') ? old('no_surat') : '',
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                    'kepada' => old('kepada') ? old('kepada') : '',
                    'lampiran' => old('lampiran') ? old('lampiran') : '',
                    'perihal' => old('perihal') ? old('perihal') : '',
                    'id_opd_fk' => Auth::user()->id_opd_fk,
                    'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : '',
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->where('id_opd', '!=', Auth::user()->id_opd_fk)->all(),
                    'kepada_id_opd' => $id_opd_fix,
                    'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('id_opd_fk', Auth::user()->id_opd_fk)->get(),
                    'tujuan' => old('tujuan') ? old('tujuan') : 'luar',
                    'qrcode' => '',
                    'berkas' => '',
                    'kategori_ttd' => 'elektronik',
                    'status_sk' => 'draft',
                    'diisi_oleh' => 'perangkat_daerah',
                    'bagikan_tu' => old('bagikan_tu') ? old('bagikan_tu') : 'tidak',
                    'is_download' => 1,
                ];
            $view = 'dashboard_page.suratkeluartte.form_opd';
        } else {
            $data =
                [
                    'mode' => 'tambah',
                    'action' => url('dashboard/surat-keluar-tte/create'),
                    'no_surat' => old('no_surat') ? old('no_surat') : '',
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                    'kepada' => old('kepada') ? old('kepada') : '',
                    'lampiran' => old('lampiran') ? old('lampiran') : '',
                    'perihal' => old('perihal') ? old('perihal') : '',
                    'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : cekIdPemprov(),
                    'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : '',
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                    'kepada_id_opd' => $id_opd_fix,
                    'tujuan' => old('tujuan') ? old('tujuan') : 'luar',
                    'qrcode' => '',
                    'berkas' => '',
                    'kategori_ttd' => 'elektronik',
                    'status_sk' => 'draft',
                    'diisi_oleh' => 'tu',
                    'bagikan_tu' => 'ya',
                    'is_download' => 1,
                ];
            $view = 'dashboard_page.suratkeluartte.form';
        }

        return view($view, $data);
    }


    public function create(Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $makeHash = random_strings(10);
        $hash = $makeHash;
        $rule = SuratKeluar::$validationRule;
        $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat';
        //$rule['berkas'] = 'required|max:7500|mimes:pdf';
        $rule['berkas'] = 'required|max:7500|mimes:pdf';
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
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
                'x' => 'Koordinat TTE',
                'y' => 'Koordinat TTE',
                'width' => 'Koordinat TTE',
                'height' => 'Koordinat TTE',
                'halaman' => 'Koordinat TTE',
            ]);
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));
            //$berkas = $this->uploadFile($berkasFile, 'berkas/temp');

        } else {
            $berkas = null;
        }


        $status_sk = 'draft';

        $tujuan = $request->input('tujuan');

        $kepada_fix = $request->input('kepada');
        $kepada_opd = '';
        $id_opd_fix = null;

        if ($tujuan == 'dalam') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                $kepada_fix = '';
            }
        }

        if ($tujuan == 'keduanya') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                //$kepada_fix = $kepada;
            }

        }


        $dataCreate = array(
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'tujuan' => $tujuan,
            'kepada_opd' => $kepada_opd,
            'kepada' => $kepada_fix,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'perihal' => $request->input('perihal'),
            'kategori_ttd' => 'elektronik',
            'id_jenis_ttd_fk' => $request->input('id_jenis_ttd_fk'),
            'id_visualisasi' => $request->input('visualisasi_tte'),

            'diisi_oleh' => 'tu',
            'bagikan_tu' => 'ya',
            'status_sk' => $status_sk,
            'berkas' => $berkas,
            'hash' => $hash,
            'is_download' => $request->input('is_download'),
        );

        $dataCreate['x'] = $request->x;
        $dataCreate['y'] = $request->y;
        $dataCreate['halaman'] = $request->halaman;
        $dataCreate['width'] = $request->width;
        $dataCreate['height'] = $request->height;


        $insert = SuratKeluar::create($dataCreate);

        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-keluar-tte/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratKeluar::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);

        $suratKeluarServices->placeQRtoPDF($berkas, $nameFile, $insert);
        //Bg Service send emailnya

        SendNotificationSuratKeluar::dispatchAfterResponse($insert);


        if ($insert) :
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            if ($id_jenis_ttd_fk) {
                $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
                $titlefb = 'Surat Keluar dengan No ' . $request->input('no_surat');
                $bodyfb = 'Perlu ditandatangi secara elektronik';
                sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
            }
            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat keluar TTE');
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat keluar TTE berhasil ditambahkan',
                    'judul' => 'data surat keluar'
                ]);
        else :
            return redirect(route('surat-keluar-tte.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat keluar TTE gagal ditambahkan',
                    'judul' => 'Data surat keluar'
                ]);
        endif;

    }


    public function create_from_opd(Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $makeHash = random_strings(10);
        $hash = $makeHash;
        $rule = SuratKeluar::$validationRule;
        $bagikan_tu = $request->input('bagikan_tu');
	$id_visualisasi = $request->input('visualisasi_tte');

        if ($bagikan_tu == 'tidak') {
            $no_surat = $request->input('no_surat');
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            //$id_visualisasi = $request->input('visualisasi_tte');

            $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat';
            $rule['berkas'] = 'required|max:7500|mimes:pdf';
        } else {
            $no_surat = null;
            $kategori_ttd = 'elektronik';
            $id_jenis_ttd_fk = null;
            //$id_visualisasi = null;
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');
            if ($bagikan_tu == 'tidak') {

                $this->validate($request, [
                    'x' => 'required',
                    'y' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'halaman' => 'required',
                ], [], [
                    'x' => 'Koordinat TTE',
                    'y' => 'Koordinat TTE',
                    'width' => 'Koordinat TTE',
                    'height' => 'Koordinat TTE',
                    'halaman' => 'Koordinat TTE',
                ]);
                //$berkas = $this->uploadFile($berkasFile, 'berkas/temp');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));

            } else {
                //$berkas = $this->uploadFile($berkasFile, 'berkas');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));
            }
        } else {
            $berkas = null;
        }

//        if ($bagikan_tu == 'tidak') {
//            $status_sk = 'final';
//        } else {
        $status_sk = 'draft';
//        }


        $tujuan = $request->input('tujuan');

        $kepada_fix = $request->input('kepada');
        $kepada_opd = '';
        $id_opd_fix = null;

        if ($tujuan == 'dalam') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                $kepada_fix = '';
            }
        }

        if ($tujuan == 'keduanya') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                //$kepada_fix = $kepada;
            }

        }


        $dataCreate = array(
            'no_surat' => $no_surat,
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'tujuan' => $tujuan,
            'kepada_opd' => $kepada_opd,
            'kepada' => $kepada_fix,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'perihal' => $request->input('perihal'),
            'diisi_oleh' => 'perangkat_daerah',
            'bagikan_tu' => $bagikan_tu,
            'kategori_ttd' => 'elektronik',
            'id_jenis_ttd_fk' => $id_jenis_ttd_fk,
            'id_visualisasi' => $id_visualisasi,

            'status_sk' => $status_sk,
            'berkas' => $berkas,
            'hash' => $hash,
            'is_download' => $request->input('is_download'),
        );
        if ($bagikan_tu == 'tidak') {

            $dataCreate['x'] = $request->x;
            $dataCreate['y'] = $request->y;
            $dataCreate['halaman'] = $request->halaman;
            $dataCreate['width'] = $request->width;
            $dataCreate['height'] = $request->height;

        }

        $insert = SuratKeluar::create($dataCreate);

        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-keluar-tte/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratKeluar::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);

        $suratKeluarServices->placeQRtoPDF($berkas, $nameFile, $insert);
        //Bg Service send emailnya

        SendNotificationSuratKeluar::dispatchAfterResponse($insert);


        if ($insert) :
            if ($id_jenis_ttd_fk != null) {
                $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
                $titlefb = 'Surat Keluar dengan No ' . $request->input('no_surat');
                $bodyfb = 'Perlu ditandatangi secara elektronik';
                sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
            }
            saveLogs('menambahkan data ' . $no_surat . ' pada fitur surat keluar TTE');
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat keluar TTE berhasil ditambahkan',
                    'judul' => 'data surat keluar'
                ]);
        else :
            return redirect(route('surat-keluar-tte.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat keluar TTE gagal ditambahkan',
                    'judul' => 'Data surat keluar'
                ]);
        endif;


    }


    public function edit($id)
    {
        $checkData = SuratKeluar::find(Hashids::decode($id)[0]);
        if ($checkData) :
            $dataMaster = $checkData;
            $id_opd_fix = '';
            $kepada_id_opd = $dataMaster->kepada_id_opd;
            if (isset($kepada_id_opd)) {
                $id_opd_fix = rtrim($kepada_id_opd, ',');
            }
            if (Auth::user()->level == 'sespri') {
                if ($dataMaster->bagikan_tu == 'ya' && $dataMaster->status_sk == 'final') {
                    return redirect(route('surat-keluar-tte'))
                        ->with('pesan_status', [
                            'tipe' => 'error',
                            'desc' => 'Surat Keluar TTE sudah final dan telah dibagikan ke TU, tidak dapat diubah',
                            'judul' => 'Halaman Surat Keluar'
                        ]);
                }
                if ($dataMaster->id_opd_fk == Auth::user()->id_opd_fk && $dataMaster->diisi_oleh == 'perangkat_daerah') {
                    $data =
                        [
                            'mode' => 'ubah',
                            'action' => url('dashboard/surat-keluar-tte/update-from-opd/' . $id),
                            'no_surat' => $dataMaster->no_surat,
                            'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                            'kepada' => $dataMaster->kepada,
                            'lampiran' => $dataMaster->lampiran,
                            'perihal' => $dataMaster->perihal,
                            'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : $dataMaster->id_jenis_ttd_fk,
                            'qrcode' => $dataMaster->qrcode,
                            'id_visualisasi' => $dataMaster->id_visualisasi,

                            'berkas' => $dataMaster->berkas,
                            'kepada_id_opd' => $id_opd_fix,
                            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                            'kategori_ttd' => $dataMaster->kategori_ttd,
                            'status_sk' => $dataMaster->status_sk,
                            'tujuan' => $dataMaster->tujuan,
                            'dataMaster' => $dataMaster,
                            'id_opd_fk' => Auth::user()->id_opd_fk,
                            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('id_opd_fk', Auth::user()->id_opd_fk)->get(),
                            'diisi_oleh' => $dataMaster->diisi_oleh,
                            'bagikan_tu' => $dataMaster->bagikan_tu,
                            'is_download' => $dataMaster->is_download
                        ];
                    return view('dashboard_page.suratkeluartte.form_opd', $data);
                } else {
                    return redirect(route('surat-keluar-tte'))
                        ->with('pesan_status', [
                            'tipe' => 'error',
                            'desc' => 'Surat Keluar TTE tidak diizinkan untuk diubah',
                            'judul' => 'Halaman Surat Keluar TTE'
                        ]);
                }

            } else {
                $data =
                    [
                        'mode' => 'ubah',
                        'action' => url('dashboard/surat-keluar-tte/update/' . $id),
                        'no_surat' => $dataMaster->no_surat,
                        'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                        'kepada' => $dataMaster->kepada,
                        'lampiran' => $dataMaster->lampiran,
                        'perihal' => $dataMaster->perihal,
                        'id_opd_fk' => $dataMaster->id_opd_fk,
                        'id_jenis_ttd_fk' => $dataMaster->id_jenis_ttd_fk,
                        'id_visualisasi' => $dataMaster->id_visualisasi,

                        'tujuan' => $dataMaster->tujuan,
                        'qrcode' => $dataMaster->qrcode,
                        'berkas' => $dataMaster->berkas,
                        'kepada_id_opd' => $id_opd_fix,
                        'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                        'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                        'kategori_ttd' => $dataMaster->kategori_ttd,
                        'status_sk' => $dataMaster->status_sk,
                        'tujuan' => $dataMaster->tujuan,
                        'dataMaster' => $dataMaster,
                        'diisi_oleh' => $dataMaster->diisi_oleh,
                        'bagikan_tu' => $dataMaster->bagikan_tu,
                        'is_download' => $dataMaster->is_download
                    ];
                return view('dashboard_page.suratkeluartte.form', $data);
            }
        else :
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar TTE tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar'
                ]);
        endif;
    }

    public function update($id, Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $rule = SuratKeluar::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratKeluar::find($idDecode);

        $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat,' . $idDecode . ',id';

        if ($dataMaster['status_sk'] == 'draft') {
            $rule['id_jenis_ttd_fk'] = 'required';
        }


        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:7500|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        $status_sk = $dataMaster['status_sk'];


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');

            if ($status_sk == 'draft') {
                $this->validate($request, [
                    'x' => 'required',
                    'y' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'halaman' => 'required',
                ], [], [
                    'x' => 'Koordinat TTE',
                    'y' => 'Koordinat TTE',
                    'width' => 'Koordinat TTE',
                    'height' => 'Koordinat TTE',
                    'halaman' => 'Koordinat TTE',
                ]);
                $koor = [
                    'x' => $request->x,
                    'y' => $request->y,
                    'halaman' => $request->halaman,
                    'width' => $request->width,
                    'height' => $request->height
                ];
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));
                //$berkas = $this->uploadFile($berkasFile, 'berkas/temp');
                if ($dataMaster['kategori_ttd'] == 'elektronik') {
                    $this->deleteFile('berkas/temp', $dataMaster['berkas']);
                } else {
                    $this->deleteFile('berkas', $dataMaster['berkas']);
                }
                $suratKeluarServices->placeQRtoPDF($berkas, $dataMaster->qrcode, $dataMaster);
            } else {
                $berkas = $dataMaster->berkas;
            }


        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :

                if ($status_sk == 'draft') {


                    $this->deleteFile('berkas/temp', $dataMaster['berkas']);

                    $berkas = '';
                } else {
                    $berkas = $dataMaster->berkas;
                }


            else :
                $berkas = $dataMaster->berkas;
            endif;
        }

        $tujuan = $request->input('tujuan');

        $kepada_fix = $request->input('kepada');
        $kepada_opd = '';
        $id_opd_fix = null;

        if ($tujuan == 'dalam') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                $kepada_fix = '';
            }
        }

        if ($tujuan == 'keduanya') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                //$kepada_fix = $kepada;
            }

        }


        $dataUpdate = [
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'kepada' => $kepada_fix,
            'kepada_id_opd' => $id_opd_fix,
            'kepada_opd' => $kepada_opd,
            'tujuan' => $tujuan,
            'lampiran' => $request->input('lampiran'),
            'perihal' => $request->input('perihal'),
            'berkas' => $berkas,
            'status_sk' => $status_sk,
            'is_download' => $request->input('is_download'),
        ];
        if (isset($koor))
            $dataUpdate = array_merge($dataUpdate, $koor);

        if ($dataMaster['status_sk'] == 'draft') {
            $dataUpdate['id_visualisasi'] = $request->input('visualiasi_tte');
            $dataUpdate['id_jenis_ttd_fk'] = $request->input('id_jenis_ttd_fk');
        }

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :

            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat keluar TTE');
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Keluar TTE berhasil diupdate',
                    'judul' => 'Data Surat Keluar TTE'
                ]);
        else :
            return redirect(url('dashboard/surat-keluar-tte/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar TTE gagal diupdate',
                    'judul' => 'Data Surat Keluar TTE'
                ]);
        endif;
    }

    public function update_from_opd($id, Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $rule = SuratKeluar::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratKeluar::find($idDecode);
        $bagikan_tu = $request->input('bagikan_tu');
 	$id_visualisasi = $dataMaster->id_visualisasi;

        if ($bagikan_tu == 'tidak') {
            if ($dataMaster['bagikan_tu'] == 'tidak') {
                $status_sk = $dataMaster['status_sk'];
            } else {
                $status_sk = 'draft';
            }

            $no_surat = $request->input('no_surat');
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            $id_visualisasi = $request->input('visualisasi_tte');

            $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat,' . $idDecode . ',id';

            if ($dataMaster['status_sk'] == 'draft') {
                $rule['id_jenis_ttd_fk'] = 'required';
            }

        } else {
            if ($dataMaster['bagikan_tu'] == 'tidak') {
                $status_sk = 'draft';
            } else {
                $status_sk = $dataMaster['status_sk'];
            }
            $no_surat = $dataMaster->no_surat;
            $kategori_ttd = $dataMaster->kategori_ttd;
            $id_jenis_ttd_fk = $dataMaster->id_jenis_ttd_fk;
            $id_visualisasi = $dataMaster->id_visualisasi;

        }


        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:7500|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );

        $kategori_ttd = 'elektronik';

        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');
            if ($status_sk == 'draft') {
                $this->validate($request, [
                    'x' => 'required',
                    'y' => 'required',
                    'width' => 'required',
                    'height' => 'required',
                    'halaman' => 'required',
                ], [], [
                    'x' => 'Koordinat TTE',
                    'y' => 'Koordinat TTE',
                    'width' => 'Koordinat TTE',
                    'height' => 'Koordinat TTE',
                    'halaman' => 'Koordinat TTE',
                ]);
                $koor = [
                    'x' => $request->x,
                    'y' => $request->y,
                    'halaman' => $request->halaman,
                    'width' => $request->width,
                    'height' => $request->height
                ];
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas/temp', Str::slug($request->input('perihal'), '-'));
                //$berkas = $this->uploadFile($berkasFile, 'berkas/temp');
                if ($dataMaster['kategori_ttd'] == 'elektronik') {
                    $this->deleteFile('berkas/temp', $dataMaster['berkas']);
                } else {
                    $this->deleteFile('berkas', $dataMaster['berkas']);
                }
                $suratKeluarServices->placeQRtoPDF($berkas, $dataMaster->qrcode, $dataMaster);
            } else {
                $berkas = $dataMaster->berkas;
            }


        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :


                $this->deleteFile('berkas/temp', $dataMaster['berkas']);

                $berkas = '';


            else :
                $berkas = $dataMaster->berkas;
            endif;
        }
        //}

        $tujuan = $request->input('tujuan');

        $kepada_fix = $request->input('kepada');
        $kepada_opd = '';
        $id_opd_fix = null;

        if ($tujuan == 'dalam') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                $kepada_fix = '';
            }
        }

        if ($tujuan == 'keduanya') {
            $kepada_id_opd = $request->input('kepada_id_opd');
            if (isset($kepada_id_opd)) {
                $id_opd = '';
                $kepada = '';
                for ($x = 0; $x < count($kepada_id_opd); $x++) {
                    $explode_kepada_id_opd = explode(';', $kepada_id_opd[$x]);
                    //$id_opd = $explode_kepada_id_opd[0];
                    $id_opd .= $explode_kepada_id_opd[0] . ',';
                    $kepada .= $explode_kepada_id_opd[1] . ';';
                }
                $id_opd_fix = rtrim($id_opd, ',');
                $kepada_opd = rtrim($kepada, ';');
                //$kepada_fix = $kepada;
            }

        }

        $dataUpdate = [
            'no_surat' => $no_surat,
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'kepada' => $kepada_fix,
            'kepada_id_opd' => $id_opd_fix,
            'kepada_opd' => $kepada_opd,
            'tujuan' => $tujuan,
            'lampiran' => $request->input('lampiran'),
            'perihal' => $request->input('perihal'),
            'bagikan_tu' => $bagikan_tu,
            'berkas' => $berkas,
            'status_sk' => $status_sk,
            'is_download' => $request->input('is_download'),
        ];
        if (isset($koor))
            $dataUpdate = array_merge($dataUpdate, $koor);

        if ($dataMaster['status_sk'] == 'draft') {
            $dataUpdate['kategori_ttd'] = $kategori_ttd;
            $dataUpdate['id_jenis_ttd_fk'] = $id_jenis_ttd_fk;
            $dataUpdate['id_visualisasi'] = $id_visualisasi;

        }

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $no_surat . ' pada fitur surat keluar TTE');
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Keluar TTE berhasil diupdate',
                    'judul' => 'Data Surat Keluar TTE'
                ]);
        else :
            return redirect(url('dashboard/surat-keluar-tte/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar TTE gagal diupdate',
                    'judul' => 'Data Surat Keluar TTE'
                ]);
        endif;
    }


    public function tanda_tangan($id)
    {
        $checkData = SuratKeluar::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/surat-keluar-tte/update-tanda-tangan'),
                    'id' => $id,
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'kepada' => $dataMaster->kepada,
                    'lampiran' => $dataMaster->lampiran,
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => PerangkatDaerah::where('id_opd', $dataMaster->id_opd_fk)->first()->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'kepada_id_opd' => $dataMaster->kepada_id_opd,
                    'tujuan' => $dataMaster->tujuan,
                    'kepada_opd' => $dataMaster->kepada_opd,
                    'kategori_ttd' => $dataMaster->kategori_ttd,
                    'status_sk' => $dataMaster->status_sk,
                ];
            return view('dashboard_page.suratkeluartte.formpenandatangan', $data);
        else :
            return redirect(route('surat-keluar-tte'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar TTE tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar TTE'
                ]);
        endif;
    }

    public function tanda_tangan_front($id)
    {
        $checkData = SuratKeluar::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('update-ttd'),
                    'id' => $id,
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'kepada' => $dataMaster->kepada,
                    'lampiran' => $dataMaster->lampiran,
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => PerangkatDaerah::where('id_opd', $dataMaster->id_opd_fk)->first()->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'kepada_id_opd' => $dataMaster->kepada_id_opd,
                    'tujuan' => $dataMaster->tujuan,
                    'kepada_opd' => $dataMaster->kepada_opd,
                    'kategori_ttd' => $dataMaster->kategori_ttd,
                    'status_sk' => $dataMaster->status_sk,
                ];
            return view('dashboard_page.suratkeluartte.formpenandatanganfront', $data);
        else :
            return Redirect::back()
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar TTE tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar TTE'
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
        $dataMaster = SuratKeluar::find($idDecode);

        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
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
                $namattd = $dataJenisTTD->jenis_ttd ?? '';

            } else {

                $fileImage = Storage::disk('uploads')->path('lampung_gray.png');
                $namattd = null;
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
                DB::table('error_log')->insert([
                    'status_code' => $responseBodyAsString->status_code,
                    'description_log' => $responseBodyAsString->error,
                    'time_log' => date('Y-m-d H-i-s'),
                    'caused_by' => Auth::user()->id,
                ]);
                // dd(json_decode($responseBodyAsString)->error);
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
                'status_sk' => 'final',
            ];


            $update = $dataMaster->update($dataUpdate);
            if ($update) :
                saveLogs($namattd . ' menandatangani data ' . $dataMaster->no_surat . ' pada fitur surat keluar TTE');
                return redirect(route('surat-keluar-tte'))
                    ->with('pesan_status', [
                        'tipe' => 'success',
                        'desc' => 'Surat Keluar TTE berhasil diupdate',
                        'judul' => 'Data Surat Keluar TTE'
                    ]);
            else :
                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => 'Surat Keluar TTE gagal diupdate',
                        'judul' => 'Data Surat Keluar TTE'
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
        $dataMaster = SuratKeluar::find($idDecode);

        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
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
                'status_sk' => 'final',
            ];


            $update = $dataMaster->update($dataUpdate);

            if ($update) :
                saveLogs('menandatangani data ' . $request->input('no_surat') . ' pada fitur surat keluar TTE');
                return redirect(url('surat-keluar-tte/' . $id))
                    ->with('pesan_status', [
                        'tipe' => 'success',
                        'desc' => 'Surat Keluar TTE berhasil diupdate',
                        'judul' => 'Data Surat Keluar TTE'
                    ]);
            else :
                return Redirect::back()
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => 'Surat Keluar TTE gagal diupdate',
                        'judul' => 'Data Surat Keluar TTE'
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

//        $ttd = JenisPenandatangan::find($id);
        $ttd = Visualisasi::find($id);
//        return response()->json(url('uploads/' . $ttd->img_ttd), 200);
        return response()->json(url('uploads/' . $ttd->img_visualisasi), 200);
    }

}
