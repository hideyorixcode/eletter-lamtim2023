<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\JenisPenandatangan;
use App\Models\PerangkatDaerah;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class BAKJenisPenandatanganController extends Controller
{
    public function index()
    {
        $data = [
            'listPerangkat' => PerangkatDaerah::pluck('id_opd', 'nama_opd')->all(),
        ];
        return view('dashboard_page.jenis-penandatangan.index', $data);
    }

    public function data(Request $request)
    {
        $data = JenisPenandatangan::relasiuser('');
        $tampilkan = $request->get('tampilkan');
        if ($tampilkan) :
            if ($tampilkan == 'tte') {
                $data = $data->where('img_ttd', '!=', '');
            } else {
                $data = $data->where('img_ttd', '=', '');
            }
        endif;
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                $checkbox = '<input type="checkbox" value="' . Hashids::encode($row->id_jenis_ttd) . '" class="data-check">';
                return $checkbox;
            })
            ->editColumn('id_jenis_ttd', function ($row) {
                return Hashids::encode($row->id_jenis_ttd);
            })
            ->editColumn('id_opd_fk', function ($row) {
                if ($row->id_opd_fk != null) {
                    return cek_opd($row->id_opd_fk)->nama_opd;
                } else {
                    return '-';
                }

            })
            ->editColumn('active', function ($row) {
                $active = '<div class="' . getActive($row->active) . ' text-small font-600-bold"><i class="fas fa-circle"></i> ' . getActiveTeks($row->active) . '</div>';
                return $active;
            })
            ->editColumn('img_ttd', function ($row) {
                $img_ttd = $row->img_ttd ? url('uploads/' . $row->img_ttd) : url('uploads/blank.png');
                $showimage = '<a class="image-popup-no-margins" href="' . $img_ttd . '"><img src="' . $img_ttd . '" height="50px"></a>';
                return $showimage;
            })
            ->addColumn('action', function ($row) {

//                $dataUser = User::where('id_jenis_ttd_fk', $row->id_jenis_ttd)->first();
//                if ($dataUser != null) {
//                    $id = Hashids::encode($dataUser->id);
//                    $email = $dataUser->email;
//                    $username = $dataUser->username;
//                } else {
//                    $id = null;
//                    $email = null;
//                    $username = null;
//                }

                $email = $row->email;
                $username = $row->username;
                $id = Hashids::encode($row->id);


                $btn = '<div class="btn-group" role="group" aria-label="First group">';
                $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-icon btn-success waves-effect clickable-edit" title="Edit"
                data-id_jenis_ttd="' . Hashids::encode($row->id_jenis_ttd) . '"
                data-jenis_ttd="' . $row->jenis_ttd . '"
                data-img_ttd="' . $row->img_ttd . '"
                data-nik="' . $row->nik . '"
                data-id_opd_fk="' . $row->id_opd_fk . '"
                data-id="' . $id . '"
                data-email="' . $email . '"
                data-username="' . $username . '"
                data-active="' . $row->active . '"><i class="fa fa-edit"></i></button>';
                $btn .= '<a href="javascript:void(0)" onclick="deleteData(' . "'" . Hashids::encode($row->id_jenis_ttd) . "'" . ')" class="btn btn-sm btn-icon btn-danger waves-effect" title="Hapus"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->toJson();
    }

    public function destroy($id)
    {

        $this->destroyFunction($id, JenisPenandatangan::class, 'img_ttd', 'jenis_ttd', 'jenis penandatangan', 'uploads', 'uploads/thumbnail');
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
            $this->destroyFunction($id, JenisPenandatangan::class, 'img_ttd', 'jenis_ttd', 'jenis penandatangan', 'uploads', 'uploads/thumbnail');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function create(Request $request)
    {

        $active = $request->input('active');
        $jenis_ttd = $request->input('jenis_ttd');
        $nik = $request->input('nik');
        $username = $request->input('username');
        $id_opd_fk = $request->input('id_opd_fk');

        //validasi
        $data = [];
        $data['error_string'] = [];
        $data['inputerror'] = [];

        $rule = JenisPenandatangan::$validationRule;
        $rule['jenis_ttd'] = 'required|max:255|unique:tbl_jenis_ttd,jenis_ttd';
        $rule['nik'] = 'required|max:30|unique:tbl_jenis_ttd,nik';
        $rule['password'] = 'required|string|confirmed|min:8';
        $rule['email'] = 'required|string|email|max:255|unique:users';
        $rule['username'] = 'required|unique:users,username';

        $validator = Validator::make($request->all(),
            $rule,
            [],
            JenisPenandatangan::$attributeRule,
        );

        if ($request->hasFile('img_ttd')) {
            $rule['img_ttd'] = 'max:1024|mimes:jpg,png';
        }

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
            if ($request->hasFile('img_ttd')) {
                $Rimg_ttd = $request->file('img_ttd');
                $img_ttd = $this->uploadImage($Rimg_ttd, 'uploads', 'uploads/thumbnail');
            } else {
                $img_ttd = null;
            }
            $dataInput = [
                'active' => $active,
                'jenis_ttd' => $jenis_ttd,
                'nik' => $nik,
                'id_opd_fk' => $id_opd_fk,
                'img_ttd' => $img_ttd,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];
            $data['status'] = true;
        }

        //simpan
        $insert = JenisPenandatangan::create($dataInput);
        if ($insert) {
            $id_jenis_ttd_fk = $insert->id_jenis_ttd;
            $dataCreate = array(
                'username' => $username,
                'id_opd_fk' => $request->input('id_opd_fk'),
                'id_jenis_ttd_fk' => $id_jenis_ttd_fk,
                'name' => $jenis_ttd,
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'level' => 'penandatangan',
                'active' => $active,
                'created_by' => Auth::user()->id,
            );
            User::create($dataCreate);
            saveLogs('menambahkan data ' . $jenis_ttd . ' pada fitur penanda tangan');
            return Respon('', true, 'Berhasil input data', 200);
        } else {
            return Respon('', false, 'Gagal input data', 200);
        }
    }


    public function edit($id)
    {
        $checkData = JenisPenandatangan::find(Hashids::decode($id))[0];
        echo json_encode($checkData);
    }

    public function update($id, Request $request)
    {

        $idDecode = Hashids::decode($id)[0];
        $iduser = $request->input('id');
        $dataMaster = JenisPenandatangan::find($idDecode);
        $active = $request->input('active');
        $jenis_ttd = $request->input('jenis_ttd');
        $nik = $request->input('nik');
        $username = $request->input('username');
        $id_opd_fk = $request->input('id_opd_fk');
        //validasi
        $data = [];
        $data['error_string'] = [];
        $data['inputerror'] = [];

        $rule = JenisPenandatangan::$validationRule;
        $rule['jenis_ttd'] = 'required|max:255|unique:tbl_jenis_ttd,jenis_ttd,' . $idDecode . ',id_jenis_ttd';
        $rule['nik'] = 'required|max:30|unique:tbl_jenis_ttd,nik,' . $idDecode . ',id_jenis_ttd';

        if ($iduser != '') {
            $idDecodeUser = Hashids::decode($iduser)[0];
            $rule['username'] = 'required|unique:users,username,' . $idDecodeUser . ',id';
            $rule['email'] = 'required|string|email|max:255|unique:users,email,' . $idDecodeUser . ',id';
            $dataMasterUser = User::find($idDecodeUser);
        } else {
            $rule['email'] = 'required|string|email|max:255|unique:users';
        }

        if ($request->input('password')) {
            $rule['password'] = 'string|confirmed|min:8';
        }


        if ($request->hasFile('img_ttd')) {
            $rule['img_ttd'] = 'max:1024|mimes:jpg,png';
        }

        $validator = Validator::make($request->all(),
            $rule,
            [],
            JenisPenandatangan::$attributeRule,
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

            if ($request->hasFile('img_ttd')) :
                $image = $request->file('img_ttd');
                $img_ttd = $this->uploadImage($image, 'uploads', 'uploads/thumbnail');
                $this->deleteFile('uploads', $dataMaster['img_ttd']);
                $this->deleteFile('uploads/thumbnail', $dataMaster['img_ttd']);
            else :
                $remove_img_ttd = $request->input('remove_img_ttd');
                if ($remove_img_ttd) :
                    $this->deleteFile('uploads', $dataMaster['img_ttd']);
                    $this->deleteFile('uploads/thumbnail', $dataMaster['img_ttd']);
                    $img_ttd = '';
                else :
                    $img_ttd = $dataMaster->img_ttd;
                endif;
            endif;
            $dataUpdate = [
                'active' => $active,
                'jenis_ttd' => $jenis_ttd,
                'nik' => $nik,
                'id_opd_fk' => $id_opd_fk,
                'img_ttd' => $img_ttd,
                'updated_by' => Auth::user()->id,
            ];

            $data['status'] = true;
        }
        //simpan
        $update = $dataMaster->update($dataUpdate);
        if ($update) {
            if ($iduser != '') {
                $dataUpdateUser = [
                    'username' => $username,
                    'id_opd_fk' => $request->input('id_opd_fk'),
                    'id_jenis_ttd_fk' => $idDecode,
                    'name' => $jenis_ttd,
                    'email' => $request->input('email'),
                    'active' => $active,
                    'updated_by' => Auth::user()->id,
                ];
                if ($request->input('password')) {
                    $dataUpdateUser['password'] = Hash::make($request->input('password'));
                }
                $dataMasterUser->update($dataUpdateUser);
            } else {
                $dataCreate = array(
                    'username' => $username,
                    'id_opd_fk' => $request->input('id_opd_fk'),
                    'id_jenis_ttd_fk' => $idDecode,
                    'name' => $jenis_ttd,
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'level' => 'penandatangan',
                    'active' => $active,
                    'created_by' => Auth::user()->id,
                );
                User::create($dataCreate);
            }
            saveLogs('mengubah data ' . $jenis_ttd . ' pada fitur penanda tangan');
            return Respon('', true, 'Berhasil input data', 200);
        } else {
            return Respon('', false, 'Gagal input data', 200);
        }
    }


}
