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
use Image;
use QrCode;
use Vinkla\Hashids\Facades\Hashids;

class SuratRahasiaController extends Controller
{
    public function index()
    {
        $data = [
        ];
        return view('dashboard_page.suratrahasia.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratMasuk::select('*')->where('sifat_surat', 'rahasia');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (in_array(Auth::user()->level, ['adpim', 'penandatangan', 'sespri'])) {
            $cekDataIdSuratRahasia = Disposisi::where('penerima', Auth::user()->id_opd_fk)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $data = $data->whereIn('id', $cekDataIdSuratRahasia);
        }


        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                return $checkbox;
            })
//
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
                $btn_print = '<a href="' . url('dashboard/surat-rahasia/print/' . Hashids::encode($row->id)) . '" title="CETAK QR" target="_blank" class="btn btn-dark"><i class="fa fa-print"></i> QR</a>';
                $btn_detail = '<a href="' . url('surat-rahasia/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-warning" title="DETAIL"><i class="fa fa-list"></i> DETAIL</a>';
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'umum'])) {
                    $btn_edit = '<a href="' . url('dashboard/surat-rahasia/edit/' . Hashids::encode($row->id)) . '" title="EDIT" class="btn btn-success"><i class="fa fa-edit"></i> EDIT</a>';
                    $btn_hapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" title="HAPUS" class="btn btn-danger"><i class="fa fa-trash"></i> HAPUS</a>';
                }
                $btn .= $btn_disposisi;
                $btn .= $btn_print;
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
        $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Rahasia', 'suratrahasia', '');
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
            $this->destroyFunction($id, SuratMasuk::class, 'qrcode', 'no_surat', 'Surat Rahasia', 'suratrahasia', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/surat-rahasia/create'),
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
                'qrcode' => '',
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),

            ];
        return view('dashboard_page.suratrahasia.form', $data);
    }

    public function create(Request $request)
    {
        $rule = SuratMasuk::$validationRule;
        //$rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat';

        $this->validate($request,
            $rule,
            [],
            SuratMasuk::$attributeRule,
        );

        $dataCreate = array(
            'kode' => $request->input('kode'),
            'indek' => $request->input('indek'),
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'sifat_surat' => 'rahasia',
            'updated_by' => Auth::user()->id,
        );

        $insert = SuratMasuk::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-rahasia/' . Hashids::encode($insert->id));
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratMasuk::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);
        if ($insert) :
            $id_sm_fk = $insert->id;
            $penerima = cekIdBiroUmum();
            $catatan_disposisi = '';
            $status = 'diteruskan';
            $dataInput = [
                'id_sm_fk' => $id_sm_fk,
                'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                'penerima' => $penerima,
                'nama_penerima' => $request->input('nama_penerima'),
                'status' => $status,
                'kepada' => $request->input('tujuan'),
                'catatan_disposisi' => $catatan_disposisi,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput);


            $dataInput2 = [
                'id_sm_fk' => $id_sm_fk,
                'penerima' => $request->input('tujuan'),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput2);

            SendNotificationSuratMasuk::dispatchAfterResponse($insert);

            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat rahasia');
            return redirect(route('surat-rahasia'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat rahasia berhasil ditambahkan',
                    'judul' => 'data surat rahasia'
                ]);
        else :
            return redirect(route('surat-rahasia.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat rahasia gagal ditambahkan',
                    'judul' => 'Data surat rahasia'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'rahasia')->find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->orderBy('id', 'ASC')->first();
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/surat-rahasia/update/' . $id),
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
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pimpinan daerah', 'sekretariat daerah'])->orderBy('jenis', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                ];
            //dd($data);
            return view('dashboard_page.suratrahasia.form', $data);
        else :
            return redirect(route('surat-rahasia'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Rahasia tidak ditemukan',
                    'judul' => 'Halaman Surat Rahasia'
                ]);
        endif;
    }

    public function update($id, Request $request)
    {
        $rule = SuratMasuk::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratMasuk::find($idDecode);

        //$rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat,' . $idDecode . ',id';
        if ($request->hasFile('berkas')) {
            $rule['berkas'] = 'max:4096|mimes:pdf';
        }
        $this->validate($request,
            $rule,
            [],
            SuratMasuk::$attributeRule,
        );


        $dataUpdate = [
            'kode' => $request->input('kode'),
            'indek' => $request->input('indek'),
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'sifat_surat' => 'rahasia',

            'updated_by' => Auth::user()->id,
        ];
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            $penerima = cekIdBiroUmum();

            $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', $idDecode)->orderBy('id', 'ASC')->first();
            if ($dataMasterDisposisiAwal != null) {
                $dataUpdateDisposisi = [
                    'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                    'penerima' => $penerima,
                    'nama_penerima' => $request->input('nama_penerima'),
                    'kepada' => $request->input('tujuan'),
                    'updated_by' => Auth::user()->id,
                ];
                $dataMasterDisposisiAwal->update($dataUpdateDisposisi);
            }
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat rahasia');
            return redirect(route('surat-rahasia'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Rahasia berhasil diupdate',
                    'judul' => 'Data Surat Rahasia'
                ]);
        else :
            return redirect(url('dashboard/surat-rahasia/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Rahasia gagal diupdate',
                    'judul' => 'Data Surat Rahasia'
                ]);
        endif;
    }


    public function show($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'rahasia')->find(Hashids::decode($id));
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
                    'listDetail' => Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->orderBy('id', 'DESC')->get(),
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
}
