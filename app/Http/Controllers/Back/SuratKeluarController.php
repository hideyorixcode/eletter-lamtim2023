<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationSuratKeluar;
use App\Models\JenisPenandatangan;
use App\Models\Opd;
use App\Models\PerangkatDaerah;
use App\Models\SuratKeluar;
use App\Models\User;
use App\Models\ViewModel\v_bar_opd_tahunan;
use App\Models\ViewModel\v_bar_perangkat_daerah;
use App\Services\SuratKeluarServices;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Image;
use QrCode;
use Vinkla\Hashids\Facades\Hashids;


class SuratKeluarController extends Controller
{
    public function index()
    {
        $data = [
            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get()
        ];
        return view('dashboard_page.suratkeluar.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratKeluar::opd()->where('kategori_ttd', 'basah');
        $id_opd_fk = $request->get('id_opd_fk');
        $id_jenis_ttd = $request->get('id_jenis_ttd');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        $tampilkan = ($request->get('tampilkan'));

        if (in_array(Auth::user()->level, ['adpim'])) {
            $data = $data->where('tbl_surat_keluar.bagikan_tu', 'ya');
        }

        if ($tampilkan) :
            if ($tampilkan != 'all') {
                $data = $data->where('tbl_surat_keluar.diisi_oleh', $tampilkan);
            }
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
                    $checkbox = $aksicheckbox;
                } else {
                    if ($row->status_sk == 'draft') {
                        $checkbox = $aksicheckbox;
                    } else {
                        $checkbox = '-';
                    }
                }
                return $checkbox;
            })
            ->editColumn('id', function ($row) {
                return Hashids::encode($row->id);
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

                    $statusnya = '<div class="p-1 color bg-danger text-white"> DRAFT </div>';

                } else if ($row->status_sk == 'revisi') {

                    $statusnya = '<div class="p-1 color bg-warning text-dark"> REVISI </div>';

                } else {

                    $statusnya = '<div class="p-1 color bg-primary text-white"> FINAL </div>';

                }
                return $statusnya;
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
            ->editColumn('qrcode', function ($row) {
                $qrcode = $row->qrcode ? url('kodeqr/' . $row->qrcode) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $qrcode . '"><img src="' . $qrcode . '" height="25px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btnprint = '<a href="' . url('dashboard/surat-keluar/print/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-sm btn-icon btn-dark waves-effect" title="Print"><i class="fa fa-print"></i></a>';
                $btndetail = '<a href="' . url('surat-keluar/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="Detail"><i class="fa fa-eye"></i></a>';
                $btnedit = '<a href="' . url('dashboard/surat-keluar/edit/' . Hashids::encode($row->id)) . '" class="btn btn-sm btn-icon btn-success waves-effect" title="Edit"><i class="fa fa-edit"></i></a>';
                $btnhapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></a>';

                if (in_array(Auth::user()->level, ['adpim', 'admin', 'superadmin'])) {
                    $btn .= $btndetail;

                    $btn .= $btnprint . $btnedit . $btnhapus;

                } else {
                    if (Auth::user()->level == 'penandatangan') {
                        $btnView = '<a href="' . url('surat-keluar/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="View"><i class="fa fa-check-double"></i> Lihat Surat</a>';
                        if ($row->status_sk == 'draft') {

                            $btn .= $btnView;

                        } else {
                            $btn .= $btnView;
                        }
                    } else if (Auth::user()->level == 'sespri') {
                        $btn .= $btndetail;
                        if ($row->bagikan_tu == 'ya') {
                            if (in_array($row->status_sk, ['draft', 'revisi']) && $row->diisi_oleh == 'perangkat_daerah' && $row->id_opd_fk == Auth::user()->id_opd_fk) {

                                $btn .= $btnedit . $btnhapus;
                            }
                        } else {
//                            if ($row->kategori_ttd == 'basah') {
                            $btn .= $btnedit . $btnhapus;
//                            } else {
//                                if ($row->status_sk == 'draft' && $row->diisi_oleh == 'perangkat_daerah' && $row->id_opd_fk == Auth::user()->id_opd_fk) {
//                                    $btn .= $btnedit . $btnhapus;
//                                }
//                            }

                            //$btn .= $btnedit . $btnhapus;
                        }

                    } else {
                        $btn .= $btndetail;
                    }
                }
                $btn .= '</div>';
                return $btn;
            })
            ->
            escapeColumns([])
            ->toJson();
    }


    public function destroy($id)
    {
        $idDecode = Hashids::decode($id);
        $dataMaster = SuratKeluar::find($idDecode)[0];
        if (in_array(Auth::user()->level, ['adpim', 'admin', 'superadmin'])) {
            $this->destroyBerkas($id, SuratKeluar::class, 'berkas', 'berkas', 'berkas/temp');
            $this->destroyFunction($id, SuratKeluar::class, 'qrcode', 'no_surat', 'Surat Keluar', 'kodeqr', '');
        } else {
            if (Auth::user()->level == 'sespri') {
                if ($dataMaster->id_opd_fk == Auth::user()->id_opd_fk) {
                    $this->destroyBerkas($id, SuratKeluar::class, 'berkas', 'berkas', 'berkas/temp');
                    $this->destroyFunction($id, SuratKeluar::class, 'qrcode', 'no_surat', 'Surat Keluar', 'kodeqr', '');
                }
            } else {
                return false;
            }
        }

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
            $idDecode = Hashids::decode($id);
            $dataMaster = SuratKeluar::find($idDecode)[0];
            if (in_array(Auth::user()->level, ['adpim', 'admin', 'superadmin'])) {
                $this->destroyBerkas($id, SuratKeluar::class, 'berkas', 'berkas', 'berkas/temp');
                $this->destroyFunction($id, SuratKeluar::class, 'qrcode', 'no_surat', 'Surat Keluar', 'kodeqr', '');
            } else {
                if (Auth::user()->level == 'sespri') {
                    if ($dataMaster->id_opd_fk == Auth::user()->id_opd_fk) {
                        $this->destroyBerkas($id, SuratKeluar::class, 'berkas', 'berkas', 'berkas/temp');
                        $this->destroyFunction($id, SuratKeluar::class, 'qrcode', 'no_surat', 'Surat Keluar', 'kodeqr', '');
                    }
                } else {
                    return false;
                }
            }


            //$this->destroyBerkas($id, SuratKeluar::class, 'berkas', 'berkas', '');
            //$this->destroyFunction($id, SuratKeluar::class, 'qrcode', 'no_surat', 'Surat Keluar', 'kodeqr', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
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
                    'kepada_id_opd' => $id_opd_fix,
                    'tujuan' => old('tujuan') ? old('tujuan') : 'luar',
                    'mode' => 'tambah',
                    'action' => url('dashboard/surat-keluar/create-from-opd'),
                    'no_surat' => old('no_surat') ? old('no_surat') : '',
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                    'kepada' => old('kepada') ? old('kepada') : '',
                    'lampiran' => old('lampiran') ? old('lampiran') : '',
                    'perihal' => old('perihal') ? old('perihal') : '',
                    'id_opd_fk' => Auth::user()->id_opd_fk,
                    'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : '',
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->where('id_opd', '!=', Auth::user()->id_opd_fk)->all(),
                    'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('id_opd_fk', Auth::user()->id_opd_fk)->get(),
                    'catatan' => '',
                    'tanggapan' => '',
                    'qrcode' => '',
                    'berkas' => '',
                    'kategori_ttd' => 'basah',
                    'status_sk' => 'draft',
                    'diisi_oleh' => 'perangkat_daerah',
                    'bagikan_tu' => old('bagikan_tu') ? old('bagikan_tu') : 'tidak',
                    'is_download' => 1,
                ];
            $view = 'dashboard_page.suratkeluar.form_opd';
        } else {
            $data =
                [
                    'kepada_id_opd' => $id_opd_fix,
                    'tujuan' => old('tujuan') ? old('tujuan') : 'luar',
                    'mode' => 'tambah',
                    'action' => url('dashboard/surat-keluar/create'),
                    'no_surat' => old('no_surat') ? old('no_surat') : '',
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                    'kepada' => old('kepada') ? old('kepada') : '',
                    'lampiran' => old('lampiran') ? old('lampiran') : '',
                    'perihal' => old('perihal') ? old('perihal') : '',
                    'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : cekIdPemprov(),
                    'id_jenis_ttd_fk' => old('id_jenis_ttd_fk') ? old('id_jenis_ttd_fk') : '',
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                    'qrcode' => '',
                    'berkas' => '',
                    'catatan' => '',
                    'tanggapan' => '',
                    'kategori_ttd' => 'basah',
                    'status_sk' => 'draft',
                    'diisi_oleh' => 'tu',
                    'bagikan_tu' => 'ya',
                    'is_download' => 1,
                ];
            $view = 'dashboard_page.suratkeluar.form';
        }

        return view($view, $data);
    }

    public function create(Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $makeHash = random_strings(10);
        $hash = $makeHash;
        $rule = SuratKeluar::$validationRule;
        $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat';
        $kategori_ttd = $request->input('kategori_ttd');

        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }

        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');


            //$berkas = $this->uploadFile($berkasFile, 'berkas');
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));

        } else {
            $berkas = null;
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


        $status_sk = 'final';


        $dataCreate = array(
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'kepada' => $kepada_fix,
            'kepada_opd' => $kepada_opd,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'tujuan' => $request->input('tujuan'),
            'perihal' => $request->input('perihal'),
            'kategori_ttd' => 'basah',
            'id_jenis_ttd_fk' => $request->input('id_jenis_ttd_fk'),
            'diisi_oleh' => 'tu',
            'bagikan_tu' => 'ya',
            'status_sk' => $status_sk,
            'berkas' => $berkas,
            'hash' => $hash,
            'is_download' => $request->input('is_download'),
        );
        $insert = SuratKeluar::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
//        $generator = url('surat-keluar/' . Hashids::encode($insert->id));
        $generator = url('surat-keluar/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratKeluar::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);

        if ($berkas != null) {
            $suratKeluarServices->placeQRtoPDF($berkas, $nameFile, $insert);
        }


        if ($insert) :
            if ($status_sk == 'final') {
                $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
                if ($id_jenis_ttd_fk) {
                    $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
                    $titlefb = 'Surat Keluar No ' . $request->input('no_surat');
                    $bodyfb = 'Berhasil dibuat menggunakan tanda tangan basah anda';
                    sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
                }
            }

            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat keluar');
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat keluar berhasil ditambahkan',
                    'judul' => 'data surat keluar'
                ]);
        else :
            return redirect(route('surat-keluar.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'suratkeluar gagal ditambahkan',
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
        $tujuan = $request->input('tujuan');
        if ($bagikan_tu == 'tidak') {
            $no_surat = $request->input('no_surat');
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat';
            $rule['berkas'] = 'required|max:4096|mimes:pdf';
        } else {
            $no_surat = null;
            $kategori_ttd = 'basah';
            $id_jenis_ttd_fk = null;
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');
            if ($bagikan_tu == 'tidak') {

                //$berkas = $this->uploadFile($berkasFile, 'berkas');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));

            } else {
                //$berkas = $this->uploadFile($berkasFile, 'berkas');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));
            }
        } else {
            $berkas = null;
        }

        if ($bagikan_tu == 'tidak') {
            $status_sk = 'final';
        } else {
            $status_sk = 'draft';
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


        $dataCreate = array(
            'no_surat' => $no_surat,
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'kepada' => $kepada_fix,
            'kepada_opd' => $kepada_opd,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'tujuan' => $request->input('tujuan'),
            'perihal' => $request->input('perihal'),
            'diisi_oleh' => 'perangkat_daerah',
            'bagikan_tu' => $bagikan_tu,
            'kategori_ttd' => 'basah',
            'id_jenis_ttd_fk' => $id_jenis_ttd_fk,
            'status_sk' => $status_sk,
            'berkas' => $berkas,
            'hash' => $hash,
            'is_download' => $request->input('is_download'),
        );

        $insert = SuratKeluar::create($dataCreate);

        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-keluar/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratKeluar::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);

        $suratKeluarServices->placeQRtoPDF($berkas, $nameFile, $insert);
        //Bg Service send emailnya
        if ($insert->kategori_ttd == 'elektronik') {
            SendNotificationSuratKeluar::dispatchAfterResponse($insert);
        }

        if ($insert) :
            if ($status_sk == "final") {
                if ($id_jenis_ttd_fk) {
                    $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
                    $titlefb = 'Surat Keluar No ' . $request->input('no_surat');
                    $bodyfb = 'Berhasil dibuat menggunakan tanda tangan basah anda';
                    sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
                }
            }

            saveLogs('menambahkan data ' . $no_surat . ' pada fitur surat keluar');
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat keluar berhasil ditambahkan',
                    'judul' => 'data surat keluar'
                ]);
        else :
            return redirect(route('surat-keluar.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'suratkeluar gagal ditambahkan',
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
                    return redirect(route('surat-keluar'))
                        ->with('pesan_status', [
                            'tipe' => 'error',
                            'desc' => 'Surat Keluar sudah final dan telah dibagikan ke TU, tidak dapat diubah',
                            'judul' => 'Halaman Surat Keluar'
                        ]);
                }
                if ($dataMaster->id_opd_fk == Auth::user()->id_opd_fk && $dataMaster->diisi_oleh == 'perangkat_daerah') {
                    $data =
                        [
                            'tujuan' => $dataMaster->tujuan,
                            'mode' => 'ubah',
                            'action' => url('dashboard/surat-keluar/update-from-opd/' . $id),
                            'no_surat' => $dataMaster->no_surat,
                            'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                            'kepada' => $dataMaster->kepada,
                            'lampiran' => $dataMaster->lampiran,
                            'perihal' => $dataMaster->perihal,
                            'id_jenis_ttd_fk' => $dataMaster->id_jenis_ttd_fk,
                            'kepada_id_opd' => $id_opd_fix,
                            'qrcode' => $dataMaster->qrcode,
                            'berkas' => $dataMaster->berkas,
                            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                            'kategori_ttd' => $dataMaster->kategori_ttd,
                            'status_sk' => $dataMaster->status_sk,
                            'dataMaster' => $dataMaster,
                            'id_opd_fk' => Auth::user()->id_opd_fk,
                            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('id_opd_fk', Auth::user()->id_opd_fk)->get(),
                            'diisi_oleh' => $dataMaster->diisi_oleh,
                            'bagikan_tu' => $dataMaster->bagikan_tu,
                            'catatan' => $dataMaster->catatan,
                            'tanggapan' => $dataMaster->tanggapan,
                            'is_download' => $dataMaster->is_download,
                        ];
                    return view('dashboard_page.suratkeluar.form_opd', $data);
                } else {
                    return redirect(route('surat-keluar'))
                        ->with('pesan_status', [
                            'tipe' => 'error',
                            'desc' => 'Surat Keluar tidak diizinkan untuk diubah',
                            'judul' => 'Halaman Surat Keluar'
                        ]);
                }

            } else {
                $data =
                    [
                        'tujuan' => $dataMaster->tujuan,
                        'mode' => 'ubah',
                        'action' => url('dashboard/surat-keluar/update/' . $id),
                        'no_surat' => $dataMaster->no_surat,
                        'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                        'kepada' => $dataMaster->kepada,
                        'lampiran' => $dataMaster->lampiran,
                        'perihal' => $dataMaster->perihal,
                        'id_opd_fk' => $dataMaster->id_opd_fk,
                        'id_jenis_ttd_fk' => $dataMaster->id_jenis_ttd_fk,
                        'kepada_id_opd' => $id_opd_fix,
                        'qrcode' => $dataMaster->qrcode,
                        'berkas' => $dataMaster->berkas,
                        'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                        'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get(),
                        'kategori_ttd' => $dataMaster->kategori_ttd,
                        'status_sk' => $dataMaster->status_sk,
                        'dataMaster' => $dataMaster,
                        'diisi_oleh' => $dataMaster->diisi_oleh,
                        'bagikan_tu' => $dataMaster->bagikan_tu,
                        'catatan' => $dataMaster->catatan,
                        'tanggapan' => $dataMaster->tanggapan,
                        'is_download' => $dataMaster->is_download,
                    ];
                return view('dashboard_page.suratkeluar.form', $data);
            }
        else :
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar'
                ]);
        endif;
    }

    public function update($id, Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $rule = SuratKeluar::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratKeluar::find($idDecode);

        if ($dataMaster->diisi_oleh == 'tu') {
            $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat,' . $idDecode . ',id';
        }

        if ($dataMaster->diisi_oleh == 'tu') {
            $rule['id_jenis_ttd_fk'] = 'required';
        }


        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        //ada folder berkas (disk) , folder temp, disk

        $status_sk = 'final';
        $catatan = '';

        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');

            //$berkas = $this->uploadFile($berkasFile, 'berkas');
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));

            $this->deleteFile('berkas', $dataMaster['berkas']);

            if ($berkas != null) {
                $suratKeluarServices->placeQRtoPDF($berkas, $dataMaster->qrcode, $dataMaster);
            }


        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :


                $this->deleteFile('berkas', $dataMaster['berkas']);

                $berkas = '';


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

        if ($dataMaster->diisi_oleh == 'perangkat_daerah' && $dataMaster->bagikan_tu == 'ya') {
            $status_sk = $request->input('status_sk');
            $catatan = $request->input('catatan');
        }

        $dataUpdate = [
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'id_opd_fk' => $request->input('id_opd_fk'),
            'kepada' => $kepada_fix,
            'kepada_opd' => $kepada_opd,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'tujuan' => $request->input('tujuan'),
            'perihal' => $request->input('perihal'),
            'catatan' => $catatan,
            'berkas' => $berkas,
            'status_sk' => $status_sk,
            'is_download' => $request->input('is_download'),
        ];


        $dataUpdate['kategori_ttd'] = 'basah';
        $dataUpdate['id_jenis_ttd_fk'] = $request->input('id_jenis_ttd_fk');


        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
//            if ($status_sk == "final") {
//                $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
//                if ($id_jenis_ttd_fk) {
//                    $firebaseToken = User::whereNotNull('fcm_token')->where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->pluck('fcm_token')->all();
//                    $titlefb = 'Surat Keluar No ' . $request->input('no_surat');
//                    $bodyfb = 'Data Surat Diubah';
//                    sendfirebasemessage($firebaseToken, $titlefb, $bodyfb);
//                }
//            }
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat keluar');
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Keluar berhasil diupdate',
                    'judul' => 'Data Surat Keluar'
                ]);
        else :
            return redirect(url('dashboard/surat-keluar/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar gagal diupdate',
                    'judul' => 'Data Surat Keluar'
                ]);
        endif;
    }

    public function update_from_opd($id, Request $request, SuratKeluarServices $suratKeluarServices)
    {
        $rule = SuratKeluar::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratKeluar::find($idDecode);
        $bagikan_tu = $request->input('bagikan_tu');
        if ($bagikan_tu == 'tidak') {
            if ($dataMaster['bagikan_tu'] == 'tidak') {
                $status_sk = $dataMaster['status_sk'];
            } else {
                $status_sk = 'draft';
            }

            $no_surat = $request->input('no_surat');
            $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');
            $rule['no_surat'] = 'required|unique:tbl_surat_keluar,no_surat,' . $idDecode . ',id';

            $rule['id_jenis_ttd_fk'] = 'required';

        } else {
            if ($dataMaster['bagikan_tu'] == 'tidak') {
                $status_sk = 'draft';
                $tanggapan = '';
            } else {
                $status_sk = $dataMaster['status_sk'];
                $tanggapan = $request->input('tanggapan');
            }
            $no_surat = $dataMaster->no_surat;
            $kategori_ttd = $dataMaster->kategori_ttd;
            $id_jenis_ttd_fk = $dataMaster->id_jenis_ttd_fk;
        }


        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratKeluar::$attributeRule,
        );


        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');

            $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));
            //$berkas = $this->uploadFile($berkasFile, 'berkas');

            $this->deleteFile('berkas', $dataMaster['berkas']);

            $suratKeluarServices->placeQRtoPDF($berkas, $dataMaster->qrcode, $dataMaster);


        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :


                $this->deleteFile('berkas', $dataMaster['berkas']);

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
            'kepada_opd' => $kepada_opd,
            'kepada_id_opd' => $id_opd_fix,
            'lampiran' => $request->input('lampiran'),
            'tujuan' => $request->input('tujuan'),
            'perihal' => $request->input('perihal'),
            'tanggapan' => $tanggapan,
            'bagikan_tu' => $bagikan_tu,
            'berkas' => $berkas,
            'status_sk' => $status_sk,
            'is_download' => $request->input('is_download'),
        ];

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $no_surat . ' pada fitur surat keluar');
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Keluar berhasil diupdate',
                    'judul' => 'Data Surat Keluar'
                ]);
        else :
            return redirect(url('dashboard/surat-keluar/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar gagal diupdate',
                    'judul' => 'Data Surat Keluar'
                ]);
        endif;
    }

    public function show($id)
    {
//        $checkData = SuratKeluar::find(Hashids::decode($id));
        $checkData = SuratKeluar::where('hash', $id)->get();
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'kepada' => $dataMaster->kepada,
                    'tujuan' => $dataMaster->tujuan,
                    'kepada_opd' => $dataMaster->kepada_opd,
                    'lampiran' => $dataMaster->lampiran,
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => PerangkatDaerah::where('id_opd', $dataMaster->id_opd_fk)->first()->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'kategori_ttd' => $dataMaster->kategori_ttd,
                    'status_sk' => $dataMaster->status_sk,
                    'kepada_id_opd' => $dataMaster->kepada_id_opd,
                    'catatan' => $dataMaster->catatan,
                    'tanggapan' => $dataMaster->tanggapan,
                    'is_download' => $dataMaster->is_download,

                ];
            return view('dashboard_page.suratkeluar.show', $data);
        else :
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar'
                ]);
        endif;
    }

    public function print($id)
    {
        $checkData = SuratKeluar::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'qrcode' => $dataMaster->qrcode,
                ];
            return view('dashboard_page.suratkeluar.print', $data);
        else :
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar'
                ]);
        endif;
    }


    public function statistik_tanda_tangan()
    {
//        $data_grafik = v_bar_ttd_tahunan::selectRaw("SUM(jumlah) AS jumlah, id_jenis_ttd_fk, jenis_ttd")
//            ->groupBy('id_jenis_ttd_fk')
//            ->orderBy('id_jenis_ttd_fk', 'ASC')
//            ->get();
        $data = [
            'suratkeluar' => SuratKeluar::count(),
            'data_tahun' => SuratKeluar::selectRaw("DISTINCT(DATE_FORMAT(tgl_surat, '%Y')) as tahun")->get(),
            //'data_grafik' => $data_grafik
        ];
        return view('dashboard_page.jenis-penandatangan.statistik', $data);
    }

    public function tampil_grafik_ttd(Request $request)
    {
        //if ($request->ajax()) {
//        $tahunnya = '';
//        $bulannya = '';
        $tahun = $request->get('tahun');

//        if ($tahun != "") {
//            $tahunnya = $tahun;
//        }
        $bulan = $request->get('bulan');
//        if ($bulan != "") {
//            $bulannya = $bulan;
//        }

//            if ($tahun != "") {
//                if ($bulan != "") {
//                    $periode = $bulan . '-' . $tahun;
//                    $query = v_bar_penandatangan::select('*')->where('periode', $periode)->groupBy('id_jenis_ttd_fk');
//                } else {
//                    $query = v_bar_ttd_tahunan::select('*')->where('periode', $tahun)->groupBy('id_jenis_ttd_fk');
//                }
//            } else {
//                $query = v_bar_ttd_tahunan::selectRaw('SUM(jumlah) AS jumlah, id_jenis_ttd_fk, jenis_ttd, kategori_ttd')->groupBy('id_jenis_ttd_fk')->groupBy('kategori_ttd');
//            }
//            $data = $query->get();

        $data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'jenisttd' => JenisPenandatangan::where('active', 1)->orderBy('id_jenis_ttd')->get(),
        ];
        //echo json_encode($data);
        return view('dashboard_page.jenis-penandatangan.render', $data)->render();
        // }
        //return false;
    }

    public function statistik_perangkat_daerah()
    {
        $data = [
            //'suratkeluar' => SuratKeluar::count(),
            'data_tahun' => SuratKeluar::selectRaw("DISTINCT(DATE_FORMAT(tgl_surat, '%Y')) as tahun")->get(),
            //'data_tahun' => SuratKeluar::tahun()->get(),
        ];
        return view('dashboard_page.perangkat-daerah.statistik', $data);
    }

    public function tampil_grafik_pd(Request $request)
    {
        if ($request->ajax()) {
            $tahun = $request->get('tahun');
            $bulan = $request->get('bulan');
            if ($tahun != "") {
                if ($bulan != "") {
                    $periode = $bulan . '-' . $tahun;
                    $query = v_bar_perangkat_daerah::select('*')->where('periode', $periode)->groupBy('id_opd_fk');
                } else {
                    $query = v_bar_opd_tahunan::select('*')->where('periode', $tahun)->groupBy('id_opd_fk');
                }
            } else {
                $query = v_bar_opd_tahunan::selectRaw('SUM(jumlah) AS jumlah, id_opd_fk, nama_opd, alias_opd')->groupBy('id_opd_fk');
            }

            $data = $query->get();
            echo json_encode($data);
        }
        return false;
    }

    public function testing($id)
    {
        $checkData = SuratKeluar::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'kepada' => $dataMaster->kepada,
                    'lampiran' => $dataMaster->lampiran,
                    'perihal' => $dataMaster->perihal,
                    'nama_opd' => PerangkatDaerah::where('id_opd', $dataMaster->id_opd_fk)->first()->nama_opd,
                    'jenis_ttd' => JenisPenandatangan::where('id_jenis_ttd', $dataMaster->id_jenis_ttd_fk)->first(),
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'kategori_ttd' => $dataMaster->kategori_ttd,
                ];
            return view('dashboard_page.suratkeluar.testing', $data);
        else :
            return redirect(route('surat-keluar'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Keluar tidak ditemukan',
                    'judul' => 'Halaman Surat Keluar'
                ]);
        endif;
    }


    public function getListTTDbyTipe(Request $request)
    {
        $kategori = $request->kategori;
        $id_jenis_ttd_fk = $request->id_jenis_ttd_fk;

        if ($kategori == 'elektronik') {
            if (Auth::user()->level == 'sespri') {
                $jenis = JenisPenandatangan::opd()->where('img_ttd', '!=', '')->where('tbl_jenis_ttd.active', 1)->where('tbl_jenis_ttd.id_opd_fk', Auth::user()->id_opd_fk)->get();
            } else {
                $jenis = JenisPenandatangan::opd()->where('img_ttd', '!=', '')->where('tbl_jenis_ttd.active', 1)->get();
            }
            // $dataJenis->where('img_ttd', '!=', null);
        } else {
            if (Auth::user()->level == 'sespri') {
                $jenis = JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->where('tbl_jenis_ttd.id_opd_fk', Auth::user()->id_opd_fk)->get();
            } else {
                $jenis = JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get();
            }

            //$dataJenis->where('img_ttd', null);
        }
        echo '<option value="">-PILIH PENANDATANGAN-</option>';
        foreach ($jenis as $r) {
            $selected = $r->id_jenis_ttd == $id_jenis_ttd_fk ? "selected" : "";
            echo '<option value="' . $r->id_jenis_ttd . '" ' . $selected . '>' . $r->jenis_ttd . ' - ' . $r->nama_opd . '</option>';
        }
    }

    public function index_surat_masuk_instansi()
    {
        $data = [
            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
            'listJenis' => JenisPenandatangan::opd()->where('tbl_jenis_ttd.active', 1)->get()
        ];
        return view('dashboard_page.suratkeluar.index_masuk', $data);
    }

    public function data_surat_masuk_instansi(Request $request)
    {
        $data = SuratKeluar::opd()->where('status_sk', 'final');
        if (in_array(Auth::user()->level, ['adpim', 'umum', 'sespri', 'penandatangan'])) {
            $search = Auth::user()->id_opd_fk;
            $data = SuratKeluar::opd()->whereRaw("find_in_set('" . $search . "',tbl_surat_keluar.kepada_id_opd)");
        }

        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));


        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        return Datatables::of($data)
            ->addIndexColumn()
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
            ->editColumn('qrcode', function ($row) {
                $qrcode = $row->qrcode ? url('kodeqr/' . $row->qrcode) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $qrcode . '"><img src="' . $qrcode . '" height="25px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btnprint = '<a href="' . url('dashboard/surat-keluar/print/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-sm btn-icon btn-dark waves-effect" title="Print"><i class="fa fa-print"></i></a>';
                $btndetail = '<a href="' . url('surat-keluar/' . $row->hash) . '" target="_blank" class="btn btn-sm btn-icon btn-primary waves-effect" title="Detail"><i class="fa fa-eye"></i></a>';


                $btn .= $btndetail;

                $btn .= $btnprint;


                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }
}
