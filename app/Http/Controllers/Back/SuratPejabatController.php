<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
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

class SuratPejabatController extends Controller
{
    public function index()
    {
        $data = [
        ];
        return view('dashboard_page.suratpejabat.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratMasuk::select('*')->where('sifat_surat', 'pejabat');
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


        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                if (in_array(Auth::user()->level, ['superadmin', 'admin'])) {
                    $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                } else {
                    if (Auth::user()->level == 'sespri') {
                        if ($row->kepada == Auth::user()->id_opd_fk) {
                            $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                        }
                    }
                    else
                    {
                        $checkbox = '';
                    }
                }

                return $checkbox;
            })
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
                $btn_detail = '<a href="' . url('surat-masuk-pejabat/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-warning" title="DETAIL"><i class="fa fa-list"></i> DETAIL</a>';
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri'])) {
                    $btn_edit = '<a href="' . url('dashboard/surat-masuk-pejabat/edit/' . Hashids::encode($row->id)) . '" title="EDIT" class="btn btn-success"><i class="fa fa-edit"></i> EDIT</a>';
                    $btn_hapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" title="HAPUS" class="btn btn-danger"><i class="fa fa-trash"></i> HAPUS</a>';
                }

                $btn .= $btn_detail;
                if (in_array(Auth::user()->level, ['superadmin', 'admin'])) {
                    $btn .= $btn_edit;
                    $btn .= $btn_hapus;
                } else {
                    if (Auth::user()->level == 'sespri') {
                        if ($row->kepada == Auth::user()->id_opd_fk) {
                            $btn .= $btn_edit;
                            $btn .= $btn_hapus;
                        }
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
        $this->destroyBerkas($id, SuratMasuk::class, 'berkas', 'berkas', '');
        $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Masuk Langsung Pejabat', 'kodeqr', '');
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
            $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Masuk Langsung Pejabat', 'kodeqr', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/surat-masuk-pejabat/create'),
                'id' => old('id') ? old('id') : '',
                'dari' => old('dari') ? old('dari') : '',
                'kepada' => old('kepada') ? old('kepada') : Auth::user()->id_opd_fk,
                'perihal' => old('perihal') ? old('perihal') : '',
                'no_surat' => old('no_surat') ? old('no_surat') : '',
                'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                'lampiran' => old('lampiran') ? old('lampiran') : '',
                'sifat_surat' => old('sifat_surat') ? old('sifat_surat') : 'pejabat',
                'berkas' => '',
                'catatan' => old('catatan') ? old('catatan') : '',
                'qrcode' => '',
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),

            ];
        return view('dashboard_page.suratpejabat.form', $data);
    }

    public function create(Request $request)
    {
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
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas',  Str::slug($request->input('perihal'), '-'));

        } else {
            $berkas = null;
        }

        $dataCreate = array(
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'sifat_surat' => 'pejabat',
            'catatan' => $request->input('catatan'),
            'berkas' => $berkas,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        );

        $insert = SuratMasuk::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-masuk-pejabat/' . Hashids::encode($insert->id));
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratMasuk::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);
        if ($insert) :
            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat masuk');
            return redirect(route('surat-masuk-pejabat'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat masuk langsung pejabat berhasil ditambahkan',
                    'judul' => 'data surat masuk'
                ]);
        else :
            return redirect(route('surat-masuk-pejabat.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat masuk langsung pejabat gagal ditambahkan',
                    'judul' => 'Data surat masuk'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'pejabat')->find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/surat-masuk-pejabat/update/' . $id),
                    'id' => old('id') ? old('id') : $dataMaster->id,
                    'catatan' => old('catatan') ? old('catatan') : $dataMaster->catatan,
                    'dari' => old('dari') ? old('dari') : $dataMaster->dari,
                    'kepada' => old('kepada') ? old('kepada') : $dataMaster->kepada,
                    'perihal' => old('perihal') ? old('perihal') : $dataMaster->perihal,
                    'no_surat' => old('no_surat') ? old('no_surat') : $dataMaster->no_surat,
                    'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : TanggalIndo2($dataMaster->tgl_surat),
                    'lampiran' => old('lampiran') ? old('lampiran') : $dataMaster->lampiran,
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                ];
            //dd($data);
            return view('dashboard_page.suratpejabat.form', $data);
        else :
            return redirect(route('surat-masuk-pejabat'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk Langsung Pejabat tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk Langsung Pejabat'
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
            //$berkas = $this->uploadFile($berkasFile, 'berkas');
            $berkas = $this->uploadFileWithName($berkasFile, 'berkas',  Str::slug($request->input('perihal'), '-'));
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
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'catatan' => $request->input('catatan'),
            'sifat_surat' => 'pejabat',
            'berkas' => $berkas,
            'updated_by' => Auth::user()->id,
        ];
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat masuk');
            return redirect(route('surat-masuk-pejabat'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Masuk berhasil diupdate',
                    'judul' => 'Data Surat Masuk Langsung Pejabat'
                ]);
        else :
            return redirect(url('dashboard/surat-masuk-pejabat/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk gagal diupdate',
                    'judul' => 'Data Surat Masuk Langsung Pejabat'
                ]);
        endif;
    }

    public function show($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'pejabat')->find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'id' => $id,
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
                ];
            return view('dashboard_page.suratpejabat.show', $data);
        else :
            return redirect(route('surat-masuk-pejabat'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Masuk Langsung Pejabat tidak ditemukan',
                    'judul' => 'Halaman Surat Masuk Langsung Pejabat'
                ]);
        endif;
    }
}
