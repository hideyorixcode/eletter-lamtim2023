<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationSuratMasuk;
use App\Models\Disposisi;
use App\Models\Opd;
use App\Models\PerangkatDaerah;
use App\Models\SuratMasuk;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Image;
use QrCode;
use Vinkla\Hashids\Facades\Hashids;

class SuratMasukController extends Controller
{
    public function index()
    {
        $data = [
        ];
        return view('dashboard_page.suratmasuk.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratMasuk::select('*')->where('sifat_surat', 'biasa');
        if (in_array(Auth::user()->level, ['sespri', 'penandatangan'])) {
            $data = SuratMasuk::select('*')->whereIn('sifat_surat', ['biasa', 'langsung', 'rahasia']);
        }

        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (in_array(Auth::user()->level, ['penandatangan', 'sespri'])) {
            //$nama_opd = User::perangkat()->where('id', Auth::user()->id)->first()->nama_opd;
            //$cekDataIdSuratMasuk = Disposisi::where('penerima', Auth::user()->id_opd_fk)->orWhere('kepada', $nama_opd)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $cekDataIdSuratMasuk = Disposisi::where('penerima', Auth::user()->id_opd_fk)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $data = $data->whereIn('id', $cekDataIdSuratMasuk);
        }


        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                return $checkbox;
            })
//            ->addColumn('dispoakhir', function ($row) {
//               $dispoakhir = Disposisi::where('id_sm_fk', $row->id)->where('tgl_diterima','!=', NULL)->orderBy('id', 'DESC')->first();
//               if($dispoakhir)
//               {
//                   return $dispoakhir->penerima;
//               }
//               else
//               {
//                   return '';
//               }
//
//            })
            ->editColumn('id', function ($row) {
                return Hashids::encode($row->id);
            })
            ->editColumn('no_surat', function ($row) {
                $no_surat = '<label class="font-weight-bolder">' . $row->no_surat . '</label>';
                return $no_surat;
            })
            ->editColumn('tgl_surat', function ($row) {
                return $row->tgl_surat ? tanggalIndo($row->tgl_surat) : '';
            })
            ->editColumn('qrcode', function ($row) {
                $qrcode = $row->qrcode ? url('kodeqr/' . $row->qrcode) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $qrcode . '"><img src="' . $qrcode . '" height="25px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';

                $btn_disposisi = '<a href="' . url('dashboard/disposisi/' . Hashids::encode($row->id)) . '" class="btn btn-primary" title="DISPOSISI"><i class="fa fa-forward"></i> DISPOSISI</a>';
                $btn_print = '<a href="' . url('dashboard/surat-masuk/print/' . Hashids::encode($row->id)) . '" title="CETAK QR" target="_blank" class="btn btn-dark"><i class="fa fa-print"></i> QR</a>';
                $btn_detail = '<a href="' . url('surat-masuk/' . $row->hash) . '" target="_blank" class="btn btn-warning" title="DETAIL"><i class="fa fa-list"></i> DETAIL</a>';
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'umum'])) {
                    $btn_edit = '<a href="' . url('dashboard/surat-masuk/edit/' . Hashids::encode($row->id)) . '" title="EDIT" class="btn btn-success"><i class="fa fa-edit"></i> EDIT</a>';
                    $btn_hapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" title="HAPUS" class="btn btn-danger"><i class="fa fa-trash"></i> HAPUS</a>';
                }
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'umum', 'sespri', 'adpim'])) {
                    $btn .= $btn_disposisi;
                    $btn .= $btn_print;
                }

                $btn .= $btn_detail;
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'umum'])) {
                    $btn .= $btn_edit;
                    $btn .= $btn_hapus;
                }
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }


    public function destroy($id)
    {
        $this->destroyBerkas($id, SuratMasuk::class, 'berkas', 'berkas', '');
        $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Masuk', 'kodeqr', '');
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
            $this->destroyBerkas($id, SuratMasuk::class, 'berkas', 'berkas', '');
            $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Masuk', 'kodeqr', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/surat-masuk/create'),
                'id' => old('id') ? old('id') : '',
                'kode' => old('kode') ? old('kode') : '',
                'indek' => old('indek') ? old('indek') : '',
                'dari' => old('dari') ? old('dari') : '',
                'kepada' => old('kepada') ? old('kepada') : cekIdPemprov(),
                'perihal' => old('perihal') ? old('perihal') : '',
                'no_surat' => old('no_surat') ? old('no_surat') : '',
                'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                'tgl_masuk' => old('tgl_masuk') ? old('tgl_masuk') : date('d/m/Y H:i:s'),
                'lampiran' => old('lampiran') ? old('lampiran') : '',
                'sifat_surat' => old('sifat_surat') ? old('sifat_surat') : '',
                'nama_penerima' => old('nama_penerima') ? old('nama_penerima') : '',
                'tujuan' => old('tujuan') ? old('tujuan') : '',
                'berkas' => '',
                'qrcode' => '',
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),

            ];
        return view('dashboard_page.suratmasuk.form', $data);
    }

    public function create(Request $request)
    {
        $makeHash = random_strings(10);
        $hash = $makeHash;
//        $cekhash = SuratMasuk::where('hash', $makeHash)->count();
//        if ($cekhash > 0) {
//            $hash = random_strings(10);
//        }


        $rule = SuratMasuk::$validationRule;
        $rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat';
        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratMasuk::$attributeRule,
        );

        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');
            //$berkas = $this->uploadFile($berkasFile, 'berkas');
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));

        } else {
            $berkas = null;
        }


        $dataCreate = array(
            'kode' => $request->input('kode'),
            'indek' => $request->input('indek'),
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'sifat_surat' => 'biasa',
            'berkas' => $berkas,
            'hash' => $hash,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        );

        $insert = SuratMasuk::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
//        $generator = url('surat-masuk/' . Hashids::encode($insert->id));
        $generator = url('surat-masuk/' . $hash);
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratMasuk::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);
        if ($insert) :
            $id_sm_fk = $insert->id;
            $penerima = cekIdBiroUmum();
            $melalui = cekIdBiroAdpim();
            $catatan_disposisi = '';
            $status = 'diteruskan';
            $dataInput = [
                'id_sm_fk' => $id_sm_fk,
                'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                'penerima' => $penerima,
                'nama_penerima' => $request->input('nama_penerima'),
                'status' => $status,
                'kepada' => $request->input('tujuan'),
                'melalui_id_opd' => $melalui,
                'catatan_disposisi' => $catatan_disposisi,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput);

            $dataInput2 = [
                'id_sm_fk' => $id_sm_fk,
                'penerima' => $melalui,
                'status' => $status,
                'kepada' => $request->input('tujuan'),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput2);

//            $dataInput3 = [
//                'id_sm_fk' => $id_sm_fk,
//                'penerima' => $request->input('tujuan'),
//                'created_by' => Auth::user()->id,
//                'updated_by' => Auth::user()->id,
//            ];
//            Disposisi::create($dataInput3);
            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat masuk');

            //BG Service untuk mengirim surat
            SendNotificationSuratMasuk::dispatchAfterResponse($insert);

            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat masuk berhasil ditambahkan',
                    'judul' => 'data surat masuk'
                ]);
        else :
            return redirect(route('surat-masuk.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat masuk gagal ditambahkan',
                    'judul' => 'Data surat masuk'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'biasa')->find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->orderBy('id', 'ASC')->first();
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/surat-masuk/update/' . $id),
                    'id' => old('id') ? old('id') : $dataMaster->id,
                    'kode' => old('kode') ? old('kode') : $dataMaster->kode,
                    'indek' => old('indek') ? old('indek') : $dataMaster->indek,
                    'dari' => old('dari') ? old('dari') : $dataMaster->dari,
                    'kepada' => old('kepada') ? old('kepada') : $dataMaster->kepada,
                    'perihal' => old('perihal') ? old('perihal') : $dataMaster->perihal,
                    'no_surat' => old('no_surat') ? old('no_surat') : $dataMaster->no_surat,
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : TanggalIndo2($dataMaster->tgl_surat),
                    'tgl_masuk' => $dataMasterDisposisiAwal != null ? TanggalIndowaktu($dataMasterDisposisiAwal->tgl_diterima) : '',
                    'lampiran' => old('lampiran') ? old('lampiran') : $dataMaster->lampiran,
                    'nama_penerima' => old('nama_penerima') ? old('nama_penerima') : $dataMasterDisposisiAwal->nama_penerima,
                    'tujuan' => old('tujuan') ? old('tujuan') : $dataMasterDisposisiAwal->kepada,
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                ];
            //dd($data);
            return view('dashboard_page.suratmasuk.form', $data);
        else :
            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk'
                ]);
        endif;
    }

    public function update($id, Request $request)
    {
        $rule = SuratMasuk::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratMasuk::find($idDecode);

        $rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat,' . $idDecode . ',id';
        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratMasuk::$attributeRule,
        );

        if ($request->hasFile('berkas')) {
            $berkasFile = $request->file('berkas');
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas', Str::slug($request->input('perihal'), '-'));
            // $berkas = $this->uploadFile($berkasFile, 'berkas');

            $this->deleteFile('berkas', $dataMaster['berkas']);
        } else {
            $remove_berkas = $request->input('remove_berkas');
            if ($remove_berkas) :
                $this->deleteFile('berkas', $dataMaster['berkas']);
                $berkas = '';
            else :
                $berkas = $dataMaster->berkas;
            endif;
        }

        $dataUpdate = [
            'kode' => $request->input('kode'),
            'indek' => $request->input('indek'),
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'sifat_surat' => 'biasa',
            'berkas' => $berkas,
            'updated_by' => Auth::user()->id,
        ];
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            $penerima = cekIdBiroUmum();
            $melalui = cekIdBiroAdpim();
            $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', $idDecode)->orderBy('id', 'ASC')->first();
            if ($dataMasterDisposisiAwal != null) {
                $dataUpdateDisposisi = [
                    'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                    'penerima' => $penerima,
                    'nama_penerima' => $request->input('nama_penerima'),
                    'kepada' => $request->input('tujuan'),
                    'melalui_id_opd' => $melalui,
                    'updated_by' => Auth::user()->id,
                ];
                $dataMasterDisposisiAwal->update($dataUpdateDisposisi);
            }
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat masuk');
            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Masuk berhasil diupdate',
                    'judul' => 'Data Surat Masuk'
                ]);
        else :
            return redirect(url('dashboard/surat-masuk/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk gagal diupdate',
                    'judul' => 'Data Surat Masuk'
                ]);
        endif;
    }

    public function show($id)
    {
        //$checkData = SuratMasuk::where('sifat_surat', 'biasa')->find(Hashids::decode($id));
        $checkData = SuratMasuk::where('hash', $id)->get();
//        $checkData = SuratMasuk::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'id' => $id,
                    'kode' => $dataMaster->kode,
                    'indek' => $dataMaster->indek,
                    'dari' => $dataMaster->dari,
                    'kepada' => $dataMaster->kepada ? PerangkatDaerah::where('id_opd', $dataMaster->kepada)->first()->nama_opd : '',
                    'perihal' => $dataMaster->perihal,
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'lampiran' => $dataMaster->lampiran,
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'sifat_surat' => $dataMaster->sifat_surat,
                    'listDetail' => Disposisi::where('id_sm_fk', $dataMaster->id)->orderBy('id', 'DESC')->get(),
                ];
            return view('dashboard_page.suratmasuk.show', $data);
        else :
            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk'
                ]);
        endif;
    }

    public function print($id)
    {
        $checkData = SuratMasuk::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'kode' => $dataMaster->kode,
                    'indek' => $dataMaster->indek,
                    'dari' => $dataMaster->dari,
                    'kepada' => $dataMaster->kepada,
                    'perihal' => $dataMaster->perihal,
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo($dataMaster->tgl_surat),
                    'lampiran' => $dataMaster->lampiran,
                    'catatan' => $dataMaster->catatan,
                    'qrcode' => $dataMaster->qrcode,
                    'sifat_surat' => $dataMaster->sifat_surat,
                    'catatan' => $dataMaster->catatan,
                ];
            return view('dashboard_page.suratmasuk.cetak', $data);
        else :
            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk'
                ]);
        endif;
    }


    public function disposisi($id)
    {
        $checkData = SuratMasuk::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            if (Auth::user()->level != 'superadmin') {
                $penerima = Auth::user()->id_opd_fk;
            } else {
                $penerima = '';
            }
            $data =
                [
                    'id' => $id,
                    'kode' => $dataMaster->kode,
                    'indek' => $dataMaster->indek,
                    'dari' => $dataMaster->dari,
                    'kepada' => $dataMaster->kepada ? PerangkatDaerah::where('id_opd', $dataMaster->kepada)->first()->nama_opd : '',
                    'perihal' => $dataMaster->perihal,
                    'no_surat' => $dataMaster->no_surat,
                    'tgl_surat' => TanggalIndo2($dataMaster->tgl_surat),
                    'lampiran' => $dataMaster->lampiran,
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'sifat_surat' => $dataMaster->sifat_surat,
                    'catatan' => $dataMaster->catatan,
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'penerima' => $penerima,
                ];
            return view('dashboard_page.suratmasuk.disposisi', $data);
        else :
            return redirect(route('surat-masuk'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk'
                ]);
        endif;
    }

    public function dataDisposisi(Request $request, $id)
    {
        //$data = Disposisi::suratmasuk();
        //$data->where('id_sm_fk', Hashids::decode($id)[0]);
        $data = Disposisi::where('id_sm_fk', Hashids::decode($id)[0]);
        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('tgl_diterima', function ($row) {
                if ($row->tgl_diterima) {
                    return TanggalIndowaktu($row->tgl_diterima);
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-sm btn-success clickable-edit-diterima" title="isi data diterimanya surat masuk"
                                                                           data-id="' . Hashids::encode($row->id) . '"
                                                                           data-id_sm_fk="' . $row->id_sm_fk . '"
                                                                           data-tgl_diterima="' . TanggalIndowaktu($row->tgl_diterima) . '"
                                                                           data-penerima="' . $row->penerima . '"
                                                                           data-nama_penerima="' . $row->nama_penerima . '"> diterima </a>';
                }

            })
            ->editColumn('penerima', function ($row) {
                return PerangkatDaerah::opd($row->penerima);
            })
            ->editColumn('kepada', function ($row) {
                return '<strong>' . PerangkatDaerah::opd($row->kepada) . '</strong>';
            })
            ->editColumn('melalui_id_opd', function ($row) {
                return PerangkatDaerah::opd($row->melalui_id_opd);
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'diteruskan'):
                    $active = '<div class="text-primary font-weight-bolder"> DITERUSKAN </div>';
                else:
                    $active = '<div class="text-success font-weight-bolder"> DIOLAH </div>';
                endif;
                return $active;
            })
            ->addColumn('action', function ($row) {

                $btn = '<div class="btn-group" role="group" aria-label="First group">';

                $editaksi = '<a href="javascript:void(0)" class="btn btn-sm btn-icon btn-success waves-effect clickable-edit" title="Edit"
                                                       data-id="' . Hashids::encode($row->id) . '"
                                                       data-id_sm_fk="' . $row->id_sm_fk . '"
                                                       data-tgl_diterima="' . TanggalIndowaktu($row->tgl_diterima) . '"
                                                       data-penerima="' . $row->penerima . '"
                                                       data-nama_penerima="' . $row->nama_penerima . '"
                                                       data-status="' . $row->status . '"
                                                       data-kepada="' . $row->kepada . '"
                                                       data-catatan_disposisi="' . $row->catatan_disposisi . '"
                                                       data-status="' . $row->status . '"
                                                       ><i class="fa fa-edit"></i></a>';
                $deleteaksi = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></a>';
                if (Auth::user()->level == 'superadmin') {
                    $btn .= $editaksi;
                    $btn .= $deleteaksi;
                } else {
                    if ($row->penerima == Auth::user()->id) {
                        $btn .= $editaksi;
                        $btn .= $deleteaksi;
                    } else {
                        $btn .= '-';
                    }
                }
                $btn .= '</div>';
                return $btn;

            })
            ->escapeColumns([])
            ->toJson();
    }

    public function dataDisposisiShow(Request $request, $id)
    {
        //$data = Disposisi::suratmasuk();
        $data = Disposisi::where('id_sm_fk', Hashids::decode($id)[0]);
        //$data->where('id_sm_fk', Hashids::decode($id)[0]);
        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('tgl_masuk', function ($row) {
                return TanggalIndowaktu($row->tgl_masuk);
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'diteruskan'):
                    $active = '<div class="text-primary font-weight-bolder"> DITERUSKAN </div>';
                elseif ($row->status == 'dihimpun'):
                    $active = '<div class="text-dark font-weight-bolder"> DIHIMPUN </div>';
                else:
                    $active = '<div class="text-success font-weight-bolder"> TINDAK LANJUT </div>';
                endif;
                return $active;
            })
            ->escapeColumns([])
            ->toJson();
    }
}
