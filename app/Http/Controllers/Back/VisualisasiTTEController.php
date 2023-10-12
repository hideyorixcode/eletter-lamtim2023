<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\JenisPenandatangan;
use App\Models\Visualisasi;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class VisualisasiTTEController extends Controller
{


    public function data(Request $request)
    {
        $data = Visualisasi::where('id_jenis_ttd_fk', $request->id_jenis_ttd_fk);

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id) . '" class="data-check">';
                return $checkbox;
            })
            ->editColumn('id', function ($row) {
                return Hashids::encode($row->id);
            })
//            ->editColumn('active', function ($row) {
//                $active = '<div class="' . getActive($row->active) . ' text-small font-600-bold"><i class="fas fa-circle"></i> ' . getActiveTeks($row->active) . '</div>';
//                return $active;
//            })

            ->editColumn('img_visualisasi', function ($row) {

                $img_visualisasi = $row->img_visualisasi ? url('uploads/' . $row->img_visualisasi) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $img_visualisasi . '"><img src="' . $img_visualisasi . '" height="50px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {

                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btn .= '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }

    public function destroy($id)
    {

        $masternya = Visualisasi::where('id', Hashids::decode($id))->first();
        if ($masternya) {
            $this->destroyFunction($id, Visualisasi::class, 'img_visualisasi', 'judul_visualisasi', 'jenis penandatangan', 'uploads', 'uploads/thumbnail');

            if (true):
                $jumlahvisualisasi = Visualisasi::where('id_jenis_ttd_fk', $masternya->id_jenis_ttd_fk)->count();
                $img_ttd = null;
                if ($jumlahvisualisasi > 0) {
                    $img_ttd = 'ada';
                }
                $this->sync_jenis_ttd($masternya->id_jenis_ttd_fk, $img_ttd);
                return Respon('', true, 'Berhasil menghapus data', 200);
            else:
                return Respon('', false, 'Gagal menghapus data', 500);
            endif;

        }


    }

    public function bulkDelete(Request $request)
    {
        $list_id = $request->input('id');
        foreach ($list_id as $id) {
            $this->destroyFunction($id, Visualisasi::class, 'img_visualisasi', 'judul_visualisasi', 'visualisasi TTE', 'uploads', 'uploads/thumbnail');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function create(Request $request)
    {

        $active = $request->input('active');
        $judul_visualisasi = $request->input('judul_visualisasi');
        $id_jenis_ttd_fk = $request->input('id_jenis_ttd_fk');


        //validasi
        $data = [];
        $data['error_string'] = [];
        $data['inputerror'] = [];


        $rule['judul_visualisasi'] = 'required';
        $rule['img_visualisasi'] = 'max:1024|mimes:jpg,png';

        $validator = Validator::make($request->all(),
            $rule,
            [],
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
            if ($request->hasFile('img_visualisasi')) {
                $Rimg_visualisasi = $request->file('img_visualisasi');
                $img_visualisasi = $this->uploadImage($Rimg_visualisasi, 'uploads', 'uploads/thumbnail');
            } else {
                $img_visualisasi = null;
            }
            $dataInput = [
                'active' => $active,
                'judul_visualisasi' => $judul_visualisasi,
                'id_jenis_ttd_fk' => $id_jenis_ttd_fk,
                'img_visualisasi' => $img_visualisasi,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            $data['status'] = true;
        }

        //simpan
        $insert = Visualisasi::create($dataInput);
        if ($insert) {
//            $cekttd = JenisPenandatangan::where('id', $id_jenis_ttd_fk)->first();
//            if ($cekttd) {
//                $dataUpdate = [
//                    'img_ttd' => 'ada'
//                ];
//                $cekttd->update($dataUpdate);
//            }
            $jumlahvisualisasi = Visualisasi::where('id_jenis_ttd_fk', $id_jenis_ttd_fk)->count();
            $img_ttd = null;
            if ($jumlahvisualisasi > 0) {
                $img_ttd = 'ada';
            }
            $this->sync_jenis_ttd($id_jenis_ttd_fk, $img_ttd);
            saveLogs('menambahkan data ' . $judul_visualisasi . ' pada fitur visualisasi TTE');
            return Respon('', true, 'Berhasil input data', 200);
        } else {
            return Respon('', false, 'Gagal input data', 200);
        }
    }

    public function sync_jenis_ttd($id_jenis_ttd_fk, $img_ttd = null)
    {

        $cekttd = JenisPenandatangan::where('id_jenis_ttd', $id_jenis_ttd_fk)->first();
        if ($cekttd) {
            $dataUpdate = [
                'img_ttd' => $img_ttd
            ];
            $cekttd->update($dataUpdate);
        }
    }

    public function getlistvisualisasi(Request $request)
    {
        $id_jenis_ttd_fk = $request->id_jenis_ttd_fk;


        $jenis = Visualisasi::where('id_jenis_ttd_fk',  $id_jenis_ttd_fk)->get();
        echo '<option value="">-PILIH VISUALISASI-</option>';
        foreach ($jenis as $r) {
            $selected = '';
            echo '<option value="' . $r->id . '" ' . $selected . '>' . $r->judul_visualisasi.'</option>';
        }
    }


}
