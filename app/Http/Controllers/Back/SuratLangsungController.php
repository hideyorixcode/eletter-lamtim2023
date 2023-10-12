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

class SuratLangsungController extends Controller
{
    public function index()
    {
        $data = [
        ];
        return view('dashboard_page.suratlangsung.index', $data);
    }

    public function data(Request $request)
    {
        $data = SuratMasuk::select('*')->where('sifat_surat', 'langsung');
        $tgl_mulai = ($request->get('tgl_mulai'));
        $tgl_akhir = ($request->get('tgl_akhir'));
        if ($tgl_mulai && $tgl_akhir) :
            $tgl_mulaiFormat = ubahformatTgl($tgl_mulai);
            $tgl_akhirFormat = ubahformatTgl($tgl_akhir);
            $data = $data->whereBetween('tgl_surat', [$tgl_mulaiFormat, $tgl_akhirFormat]);
        endif;

        if (in_array(Auth::user()->level, ['sespri', 'penandatangan'])) {
            $cekDataIdSuratMasuk = Disposisi::where('penerima', Auth::user()->id_opd_fk)->distinct()->get(['id_sm_fk'])->pluck('id_sm_fk')->toArray();
            $data = $data->whereIn('id', $cekDataIdSuratMasuk);
        }


        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
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
                $btn_disposisi = '<a href="' . url('dashboard/disposisi/' . Hashids::encode($row->id)) . '" class="btn btn-primary" title="DISPOSISI"><i class="fa fa-forward"></i> DISPOSISI</a>';
                $btn_print = '<a href="' . url('dashboard/surat-langsung/print/' . Hashids::encode($row->id)) . '" target="_blank" title="CETAK QR" class="btn btn-dark"><i class="fa fa-print"></i> QR</a>';
                $btn_detail = '<a href="' . url('surat-langsung/' . Hashids::encode($row->id)) . '" target="_blank" class="btn btn-warning" title="DETAIL"><i class="fa fa-list"></i> DETAIL</a>';
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'adpim'])) {
                    $btn_hapus = '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" title="HAPUS" class="btn btn-danger"><i class="fa fa-trash"></i> HAPUS</a>';
                }
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'adpim', 'umum'])) {
                    $btn_edit = '<a href="' . url('dashboard/surat-langsung/edit/' . Hashids::encode($row->id)) . '" title="EDIT" class="btn btn-success"><i class="fa fa-edit"></i> EDIT</a>';

                }
                $btn .= $btn_disposisi;
                $btn .= $btn_print;
                $btn .= $btn_detail;
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'adpim', 'umum'])) {
                    $btn .= $btn_edit;

                }
                if (in_array(Auth::user()->level, ['superadmin', 'admin', 'adpim'])) {
                    $btn .= $btn_hapus;
                }
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }


    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/surat-langsung/create'),
                'id' => old('id') ? old('id') : '',
                'dari' => old('dari') ? old('dari') : '',
                'kepada' => old('kepada') ? old('kepada') : cekIdPemprov(),
                'perihal' => old('perihal') ? old('perihal') : '',
                'no_surat' => old('no_surat') ? old('no_surat') : '',
                'tgl_surat' => old('tgl_surat') ? old('tgl_surat') : date('d/m/Y'),
                'tgl_masuk' => old('tgl_masuk') ? old('tgl_masuk') : date('d/m/Y H:i:s'),
                'lampiran' => old('lampiran') ? old('lampiran') : '',
                'sifat_surat' => old('sifat_surat') ? old('sifat_surat') : 'langsung',
                'nama_penerima' => old('nama_penerima') ? old('nama_penerima') : '',
                'tujuan' => old('tujuan') ? old('tujuan') : '',
                'catatan' => old('catatan') ? old('catatan') : '',
                'qrcode' => '',
                'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                //'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['opd','sekretariat daerah'])->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['opd', 'sekretariat daerah', 'tu', 'pimpinan daerah'])->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                'listMelalui' => PerangkatDaerah::where('jenis', 'tu')->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                'melalui' => cekIdBiroAdpim(),
                'editTujuan' => 1,
            ];
        return view('dashboard_page.suratlangsung.form', $data);
    }

    public function createBAK(Request $request)
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

        $dataCreate = array(
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'catatan' => $request->input('catatan'),
            'sifat_surat' => 'langsung',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        );

        $insert = SuratMasuk::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-langsung/' . Hashids::encode($insert->id));
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratMasuk::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);
        if ($insert) :
            $id_sm_fk = $insert->id;
            $penerima = cekIdBiroAdpim();
            $melalui = cekIdBiroUmum();
            $catatan_disposisi = '';
            $status = 'diteruskan';
            $tujuan = $request->input('tujuan');
            if (isset($tujuan)):
                $tujuanKoma = implode(',', $tujuan);
            else :
                $tujuanKoma = null;
            endif;
            $dataInput = [
                'id_sm_fk' => $id_sm_fk,
                'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                'penerima' => $penerima,
                'nama_penerima' => $request->input('nama_penerima'),
                'status' => $status,
                'kepada' => $tujuanKoma,
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
                'kepada' => $tujuanKoma,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput2);

            SendNotificationSuratMasuk::dispatchAfterResponse($insert);
            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat langsung');
            return redirect(route('surat-langsung'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat langsung berhasil ditambahkan',
                    'judul' => 'data surat langsung'
                ]);
        else :
            return redirect(route('surat-langsung.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat langsung gagal ditambahkan',
                    'judul' => 'Data surat langsung'
                ]);
        endif;

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

        $dataCreate = array(
            'dari' => $request->input('dari'),
            'kepada' => $request->input('kepada'),
            'perihal' => $request->input('perihal'),
            'no_surat' => $request->input('no_surat'),
            'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
            'lampiran' => $request->input('lampiran'),
            'catatan' => $request->input('catatan'),
            'sifat_surat' => 'langsung',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        );

        $insert = SuratMasuk::create($dataCreate);
        $nameFile = round(microtime(true) * 1000) . '.png';
        $generator = url('surat-langsung/' . Hashids::encode($insert->id));
        QrCode::format('png')->errorCorrection('H')->size(250)->merge('uploads/lampung_gray.png', 0.3, true)->generate($generator, base_path('kodeqr/' . $nameFile));
        $dataMaster = SuratMasuk::find($insert->id);
        $dataUpdate = [
            'qrcode' => $nameFile,
        ];
        $dataMaster->update($dataUpdate);
        if ($insert) :
            $melalui = $request->input('melalui');
            $id_sm_fk = $insert->id;
            if ($melalui == cekIdBiroAdpim()) {
                //$penerima = cekIdBiroUmum();
                //$melalui = cekIdBiroAdpim();
                $penerima = cekIdBiroAdpim();
                $melaluiKelanjutan = null;
            } else {
                $penerima = cekIdBiroAdpim();
                $melaluiKelanjutan = cekIdBiroUmum();
            }

            $catatan_disposisi = '';
            $status = 'diteruskan';
            $tujuan = $request->input('tujuan');
            if (isset($tujuan)):
                $tujuanKoma = implode(',', $tujuan);
            else :
                $tujuanKoma = null;
            endif;
            $dataInput = [
                'id_sm_fk' => $id_sm_fk,
                'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                'penerima' => $penerima,
                'nama_penerima' => $request->input('nama_penerima'),
                'status' => $status,
                'kepada' => $tujuanKoma,
                'melalui_id_opd' => $melaluiKelanjutan,
                'catatan_disposisi' => $catatan_disposisi,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            Disposisi::create($dataInput);

            if ($melalui != cekIdBiroAdpim()) {
                $dataInput2 = [
                    'id_sm_fk' => $id_sm_fk,
                    'penerima' => $melalui,
                    'status' => $status,
                    'kepada' => $tujuanKoma,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];
                Disposisi::create($dataInput2);
            } else {
                if (isset($tujuan)):
                    for ($x = 0; $x < count($tujuan); $x++) {
                        $dataTujuan = array(
                            'penerima' => $tujuan[$x],
                            'id_sm_fk' => $id_sm_fk,
                            'status' => $status,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        );
                        Disposisi::create($dataTujuan);
                    }
                endif;
            }

            SendNotificationSuratMasuk::dispatchAfterResponse($insert);
            saveLogs('menambahkan data ' . $request->input('no_surat') . ' pada fitur surat langsung');
            return redirect(route('surat-langsung'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'surat langsung berhasil ditambahkan',
                    'judul' => 'data surat langsung'
                ]);
        else :
            return redirect(route('surat-langsung.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'surat langsung gagal ditambahkan',
                    'judul' => 'Data surat langsung'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'langsung')->find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $cekJumlahDisposisi = Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->count();
            $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', Hashids::decode($id)[0])->orderBy('id', 'ASC')->first();
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/surat-langsung/update/' . $id),
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
                    'catatan' => old('catatan') ? old('catatan') : $dataMaster->catatan,
                    'nama_penerima' => old('nama_penerima') ? old('nama_penerima') : $dataMasterDisposisiAwal->nama_penerima,
                    'tujuan' => old('tujuan') ? old('tujuan') : $dataMasterDisposisiAwal->kepada,
                    'qrcode' => $dataMaster->qrcode,
                    'berkas' => $dataMaster->berkas,
                    'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
                    //'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['pi', 'opd'])->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                    'listPimpinan' => PerangkatDaerah::whereIn('jenis', ['opd', 'sekretariat daerah', 'tu', 'pimpinan daerah'])->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                    'listMelalui' => PerangkatDaerah::where('jenis', 'tu')->orderBy('nama_opd', 'ASC')->pluck('id_opd', 'nama_opd')->all(),
                    'melalui' => $dataMasterDisposisiAwal->melalui_id_opd,
                    'editTujuan' => $cekJumlahDisposisi <= 2 ? 1 : 0,

                ];
            //dd($data);
            if (Auth::user()->level == 'umum') {
                return view('dashboard_page.suratlangsung.editumum', $data);
            } else {
                return view('dashboard_page.suratlangsung.form', $data);
            }

        else :
            return redirect(route('surat-langsung'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Langsung tidak ditemukan',
                    'judul' => 'Halaman Surat Langsung'
                ]);
        endif;
    }

    public function updateBAK($id, Request $request)
    {
        $rule = SuratMasuk::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratMasuk::find($idDecode);

        $rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat,' . $idDecode . ',id';
        if (Auth::user()->level == 'umum') {
            if ($request->hasFile('berkas')) {
                $rule['berkas'] = 'max:4096|mimes:pdf';
            }
        }
        if (Auth::user()->level != 'umum') {
            $this->validate($request,
                $rule,
                [],
                SuratMasuk::$attributeRule,
            );
        }

        if (Auth::user()->level == 'umum') {
            if ($request->hasFile('berkas')) {
                $berkasFile = $request->file('berkas');
                $berkas = $this->uploadFile($berkasFile, 'berkas');

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
        }

        if (Auth::user()->level != 'umum') {
            $dataUpdate = [
                'dari' => $request->input('dari'),
                'kepada' => $request->input('kepada'),
                'perihal' => $request->input('perihal'),
                'no_surat' => $request->input('no_surat'),
                'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
                'lampiran' => $request->input('lampiran'),
                'catatan' => $request->input('catatan'),
                'sifat_surat' => 'langsung',
                'updated_by' => Auth::user()->id,
            ];
        } else {
            $dataUpdate = [
                'kode' => $request->input('kode'),
                'indek' => $request->input('indek'),
                'berkas' => $berkas,
                'updated_by' => Auth::user()->id,
            ];
        }
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            if (Auth::user()->level != 'umum') {
                $penerima = cekIdBiroAdpim();
                $melalui = cekIdBiroUmum();
                $tujuan = $request->input('tujuan');
                if (isset($tujuan)):
                    $tujuanKoma = implode(',', $tujuan);
                else :
                    $tujuanKoma = null;
                endif;
                $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', $idDecode)->orderBy('id', 'ASC')->first();
                if ($dataMasterDisposisiAwal != null) {
                    $dataUpdateDisposisi = [
                        'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                        'penerima' => $penerima,
                        'nama_penerima' => $request->input('nama_penerima'),
                        'kepada' => $tujuanKoma,
                        'melalui_id_opd' => $melalui,
                        'updated_by' => Auth::user()->id,
                    ];
                    $dataMasterDisposisiAwal->update($dataUpdateDisposisi);
                }
            }
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat langsung');
            return redirect(route('surat-langsung'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Langsung berhasil diupdate',
                    'judul' => 'Data Surat Langsung'
                ]);
        else :
            return redirect(url('dashboard/surat-langsung/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Langsung gagal diupdate',
                    'judul' => 'Data Surat Langsung'
                ]);
        endif;
    }

    public function update($id, Request $request)
    {
        $rule = SuratMasuk::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = SuratMasuk::find($idDecode);

        $rule['no_surat'] = 'required|unique:tbl_surat_masuk,no_surat,' . $idDecode . ',id';
        if (Auth::user()->level == 'umum') {
            if ($request->hasFile('berkas')) {
                $rule['berkas'] = 'max:4096|mimes:pdf';
            }
        }
        if (Auth::user()->level != 'umum') {
            $this->validate($request,
                $rule,
                [],
                SuratMasuk::$attributeRule,
            );
        }

        if (Auth::user()->level == 'umum') {
            if ($request->hasFile('berkas')) {
                $berkasFile = $request->file('berkas');
                //$berkas = $this->uploadFile($berkasFile, 'berkas');
                $berkas = $this->uploadFileWithName($berkasFile, 'berkas',  Str::slug($dataMaster->perihal, '-'));

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
        }

        if (Auth::user()->level != 'umum') {
            $dataUpdate = [
                'dari' => $request->input('dari'),
                'kepada' => $request->input('kepada'),
                'perihal' => $request->input('perihal'),
                'no_surat' => $request->input('no_surat'),
                'tgl_surat' => ubahformatTgl($request->input('tgl_surat')),
                'lampiran' => $request->input('lampiran'),
                'catatan' => $request->input('catatan'),
                'sifat_surat' => 'langsung',
                'updated_by' => Auth::user()->id,
            ];
        } else {
            $dataUpdate = [
                'kode' => $request->input('kode'),
                'indek' => $request->input('indek'),
                'berkas' => $berkas,
                'updated_by' => Auth::user()->id,
            ];
        }
        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            if (Auth::user()->level != 'umum') {
//                $penerima = cekIdBiroAdpim();
//                $melalui = cekIdBiroUmum();
//                $tujuan = $request->input('tujuan');
//                if (isset($tujuan)):
//                    $tujuanKoma = implode(',', $tujuan);
//                else :
//                    $tujuanKoma = null;
//                endif;
                $dataMasterDisposisiAwal = Disposisi::where('id_sm_fk', $idDecode)->orderBy('id', 'ASC')->first();
                if ($dataMasterDisposisiAwal != null) {
                    $dataUpdateDisposisi = [
                        'tgl_diterima' => DateTimeFormatDB($request->input('tgl_masuk')),
                        //'penerima' => $penerima,
                        'nama_penerima' => $request->input('nama_penerima'),
                        //'kepada' => $tujuanKoma,
                        //'melalui_id_opd' => $melalui,
                        'updated_by' => Auth::user()->id,
                    ];
                    $dataMasterDisposisiAwal->update($dataUpdateDisposisi);
                }
            }
            saveLogs('memperbarui data ' . $request->input('no_surat') . ' pada fitur surat langsung');
            return redirect(route('surat-langsung'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Surat Langsung berhasil diupdate',
                    'judul' => 'Data Surat Langsung'
                ]);
        else :
            return redirect(url('dashboard/surat-langsung/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Surat Langsung gagal diupdate',
                    'judul' => 'Data Surat Langsung'
                ]);
        endif;
    }

    public function show($id)
    {
        $checkData = SuratMasuk::where('sifat_surat', 'langsung')->find(Hashids::decode($id));
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
                    'catatan' => $dataMaster->catatan,
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
