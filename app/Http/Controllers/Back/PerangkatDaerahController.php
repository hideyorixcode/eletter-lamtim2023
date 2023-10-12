<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDaerah;
use App\Models\UnkerSimpedu;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

class PerangkatDaerahController extends Controller
{
    public function index()
    {

        return view('dashboard_page.perangkat-daerah.index');
    }

    public function data(Request $request)
    {
        $data = PerangkatDaerah::select('*');
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id_opd) . '" class="data-check">';
                return $checkbox;
            })
            ->editColumn('id_opd', function ($row) {
                return Hashids::encode($row->id_opd);
            })
            ->editColumn('nama_opd', function ($row) {
//                $nama = '<label class="font-weight-bolder">' . $row->nama_opd . '</label>';
//                $alias = '<label> Alias : ' . $row->alias_opd . '</label>';
//                return $nama . '<br/>' . $alias;

                $nama = '<label class="font-weight-bolder">' . $row->nama_opd . '</label>';
                $alias = $row->alias_opd ? ' <em>(' . $row->alias_opd . ')</em>' : '';
                return $nama .'' . $alias;
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? TanggalIndowaktu($row->updated_at) : '';
            })
            ->editColumn('active', function ($row) {
                $active = '<div class="' . getActive($row->active) . ' text-small font-600-bold"><i class="fas fa-circle"></i> ' . getActiveTeks($row->active) . '</div>';
                return $active;
            })
            ->editColumn('jenis', function ($row) {
                if ($row->jenis == 'opd') {
                    $jenis = '<div class="badge badge-warning">OPD</div>';
                } elseif ($row->jenis == 'pimpinan daerah') {
                    $jenis = '<div class="badge badge-primary">PIMPINAN DAERAH</div>';
                } elseif ($row->jenis == 'sekretariat daerah') {
                    $jenis = '<div class="badge badge-success">SEKRETARIAT DAERAH</div>';
                } elseif ($row->jenis == 'tu') {
                    $jenis = '<div class="badge badge-dark">TU</div>';
                } else {
                    $jenis = '-';
                }
                return $jenis;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btn .= '<a href="' . url('dashboard/perangkat-daerah/show/' . Hashids::encode($row->id_opd)) . '" class="btn btn-sm btn-icon btn-warning waves-effect" title="Detail"><i class="fa fa-eye"></i></a>';
                $btn .= '<a href="' . url('dashboard/perangkat-daerah/edit/' . Hashids::encode($row->id_opd)) . '" class="btn btn-sm btn-icon btn-success waves-effect" title="Edit"><i class="fa fa-edit"></i></a>';
                $btn .= '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id_opd) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }

    public function destroy($id)
    {
        $this->destroyFunction($id, PerangkatDaerah::class, '', 'nama_opd', 'perangkat daerah', '', '');
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
            $this->destroyFunction($id, PerangkatDaerah::class, '', 'nama_opd', 'perangkat daerah', '', '');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {

        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/perangkat-daerah/create'),
                'nama_opd' => old('nama_opd') ? old('nama_opd') : '',
                'alias_opd' => old('alias_opd') ? old('alias_opd') : '',
                'alamat_opd' => old('alamat_opd') ? old('alamat_opd') : '',
                'email_opd' => old('email_opd') ? old('email_opd') : '',
                'notelepon_opd' => old('notelepon_opd') ? old('notelepon_opd') : '',
                'active' => old('active') ? old('active') : '',
                'jenis' => old('jenis') ? old('jenis') : '',
                'T_KUnker' => old('T_KUnker') ? old('T_KUnker') : '',

            ];
        return view('dashboard_page.perangkat-daerah.form', $data);
    }

    public function create(Request $request)
    {
        $rule = PerangkatDaerah::$validationRule;
        $rule['nama_opd'] = 'required|unique:tbl_opd,nama_opd';
        $rule['alias_opd'] = 'required|unique:tbl_opd,alias_opd';

        $this->validate($request,
            $rule,
            [],
            PerangkatDaerah::$attributeRule,
        );

//        $T_KUnker = $request->input('T_KUnker');
//        $T_KUnker_convert = explode('|', $T_KUnker);
        $dataCreate = array(
            'nama_opd' => $request->input('nama_opd'),
            'alias_opd' => $request->input('alias_opd'),
            'alamat_opd' => $request->input('alamat_opd'),
            'email_opd' => $request->input('email_opd'),
            'notelepon_opd' => $request->input('notelepon_opd'),
            'active' => $request->input('active'),
            'jenis' => $request->input('jenis'),
//            'T_KUnker' => $T_KUnker_convert[0],
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        );

        $insert = PerangkatDaerah::create($dataCreate);

        if ($insert) :
            saveLogs('menambahkan data ' . $request->input('nama_opd') . ' pada fitur perangkat daerah');
            return redirect(route('perangkat-daerah'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'perangkat daerah berhasil ditambahkan',
                    'judul' => 'Data Perangkat Daerah'
                ]);
        else :
            return redirect(route('perangkat-daerah.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'perangkat daerah gagal ditambahkan',
                    'judul' => 'Data Perangkat Daerah'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = PerangkatDaerah::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/perangkat-daerah/update/' . $id),
                    'nama_opd' => old('nama_opd') ? old('nama_opd') : $dataMaster->nama_opd,
                    'alias_opd' => old('alias_opd') ? old('alias_opd') : $dataMaster->alias_opd,
                    'alamat_opd' => old('alamat_opd') ? old('alamat_opd') : $dataMaster->alamat_opd,
                    'email_opd' => old('email_opd') ? old('email_opd') : $dataMaster->email_opd,
                    'notelepon_opd' => old('notelepon_opd') ? old('notelepon_opd') : $dataMaster->notelepon_opd,
                    'active' => old('active') ? old('active') : $dataMaster->active,
                    'jenis' => old('jenis') ? old('jenis') : $dataMaster->jenis,
//                    'listUnkerSimpedu' => UnkerSimpedu::whereIn('Nom', [1, 2, 3, 4])->select('T_KUnker', 'Unit_Kerja')->get(),
                    'T_KUnker' => old('T_KUnker') ? old('T_KUnker') : $dataMaster->T_KUnker,
                ];
            return view('dashboard_page.perangkat-daerah.form', $data);
        else :
            return redirect(route('perangkat-daerah'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'perangkat daerah tidak ditemukan',
                    'judul' => 'halaman perangkat daerah'
                ]);
        endif;
    }

    public function update($id, Request $request)
    {
        $rule = PerangkatDaerah::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = PerangkatDaerah::find($idDecode);
        $rule['nama_opd'] = 'required|unique:tbl_opd,nama_opd,' . $idDecode . ',id_opd';
        $rule['alias_opd'] = 'required|unique:tbl_opd,alias_opd,' . $idDecode . ',id_opd';

        $this->validate($request,
            $rule,
            [],
            PerangkatDaerah::$attributeRule,
        );
//        $T_KUnker = $request->input('T_KUnker');
//        $T_KUnker_convert = explode('|', $T_KUnker);

        $dataUpdate = [
            'nama_opd' => $request->input('nama_opd'),
            'alias_opd' => $request->input('alias_opd'),
            'alamat_opd' => $request->input('alamat_opd'),
            'email_opd' => $request->input('email_opd'),
            'notelepon_opd' => $request->input('notelepon_opd'),
            'active' => $request->input('active'),
            'jenis' => $request->input('jenis'),
//            'T_KUnker' => $T_KUnker_convert[0],
            'updated_by' => Auth::user()->id,
        ];

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $request->input('nama_opd') . ' pada fitur perangkat daerah');
            return redirect(route('perangkat-daerah'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'perangkat daerah berhasil diupdate',
                    'judul' => 'data perangkat daerah'
                ]);
        else :
            return redirect(url('dashboard/perangkat-daerah/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'perangkat daerah gagal diupdate',
                    'judul' => 'data perangkat daerah'
                ]);
        endif;
    }

    public function show($id)
    {
        $checkData = PerangkatDaerah::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'nama_opd' => $dataMaster->nama_opd,
                    'alias_opd' => $dataMaster->alias_opd,
                    'alamat_opd' => $dataMaster->alamat_opd,
                    'email_opd' => $dataMaster->email_opd,
                    'notelepon_opd' => $dataMaster->notelepon_opd,
                    'active' => $dataMaster->active,
                    'jenis' => $dataMaster->jenis,
                ];
            return view('dashboard_page.perangkat-daerah.show', $data);
        else :
            return redirect(route('perangkat-daerah'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'perangkat daerah tidak ditemukan',
                    'judul' => 'halaman perangkat daerah'
                ]);
        endif;
    }

    public function get_unker_simpedu()
    {
        $response = Http::withHeaders([
            'X-Authorization' => env('TOKENJAMAJAMA'),
        ])->get(env('LINKJAMAJAMA') . '/opd');
        if ($response['status'] == 'OK') {

            foreach ($jenis as $r) {
                $selected = $r->id_jenis_ttd == $id_jenis_ttd_fk ? "selected" : "";
                echo '<option value="' . $r->id_jenis_ttd . '" ' . $selected . '>' . $r->jenis_ttd . ' - ' . $r->nama_opd . '</option>';
            }
        }
    }

    public function sinkronisasi_id_simpedu()
    {
        //$dataSimpedu = UnkerSimpedu::whereIn('Nom', [1,2,3,4])->select('T_KUnker','Unit_Kerja')->get();
        $listPerangkat = PerangkatDaerah::select('id_opd', 'nama_opd')->get();
        foreach ($listPerangkat as $x) {
            $dataMaster = PerangkatDaerah::find($x->id_opd);
            $T_KUnker = '';
            $nama_opd = $x->nama_opd;
            $cekTunker = UnkerSimpedu::where('Unit_Kerja', $x->nama_opd)->first();
            if ($cekTunker) {
                $T_KUnker = $cekTunker->T_KUnker;
                $nama_opd = $cekTunker->Unit_Kerja;
            }
            $dataUpdate = [
                'T_KUnker' => $T_KUnker,
                'nama_opd' => $nama_opd,
            ];
            $dataMaster->update($dataUpdate);
        }


    }

    public function sinkronisasi_unker()
    {
        DB::beginTransaction();
        try {
            $listPerangkat = UnkerSimpedu::select('T_KUnker', 'Unit_Kerja')->get();
            foreach ($listPerangkat as $x) {
                $cekunker = PerangkatDaerah::where('T_KUnker', $x->T_KUnker)->count();
                if ($cekunker == 0) {
                    $storeData = [
                        'nama_opd' => $x->Unit_Kerja,
                        'alias_opd' => '',
                        'active' => 0,
                        'jenis' => 'opd',
                        'T_KUnker' => $x->T_KUnker,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ];
                    PerangkatDaerah::create($storeData);
                }
            }
        } catch (\ErrorException $e) {
            DB::rollBack();
            $msgerror = $e->getMessage() ?? 'gagal sinkronisasi';
            return $msgerror;
        }

        DB::commit();
        $msgsuccess = 'berhasil sinkronisasi';
        return $msgsuccess;


    }

}
