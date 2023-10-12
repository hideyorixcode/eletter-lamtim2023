<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\PerangkatDaerah;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Image;
use Vinkla\Hashids\Facades\Hashids;

class PenggunaController extends Controller
{
    public function index()
    {
        return view('dashboard_page.pengguna.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $level = $request->get('level');
            $cari = $request->get('cari');
            $arrayList = $request->get('arrayList');
            $paginateList = [10, 25, -1];
            $page_count = $request->get('page_count');
            $countPaginate = $page_count == -1 ? User::count() : $page_count;
            if (Auth::user()->level == 'superadmin'):
                $eloUser = User::perangkat()->whereIn('level', ['superadmin', 'admin', 'adpim', 'sespri', 'umum']);
            else:
                $eloUser = User::perangkat()->whereIn('level', ['admin', 'adpim', 'sespri', 'umum']);
            endif;
            if ($level) :
                if ($cari) :
                    $dataUser = $eloUser->where('level', $level)->where('name', 'like', "%" . $cari . "%")->paginate($countPaginate);
                else :
                    $dataUser = $eloUser->where('level', $level)->paginate($countPaginate);
                endif;
            else :
                if ($cari) :
                    $dataUser = $eloUser->where('name', 'like', "%" . $cari . "%")->paginate($countPaginate);
                else :
                    $dataUser = $eloUser->paginate($countPaginate);
                endif;
            endif;

            $data = [
                "listPengguna" => $dataUser,
                "arrayList" => $arrayList != null ? $arrayList : [],
                "page_count" => $page_count,
                "paginateList" => $paginateList,
            ];
            return view('dashboard_page.pengguna.data', $data)->render();
        } else {
            return false;
        }
    }

    public function destroy($id)
    {
        $this->destroyFunction($id, User::class, 'avatar', 'name', 'pengguna', 'uploads', 'uploads/thumbnail');
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
            $this->destroyFunction($id, User::class, 'avatar', 'name', 'pengguna', 'uploads', 'uploads/thumbnail');
        }
        return Respon('', true, 'Berhasil menghapus beberapa data', 200);
    }

    public function form()
    {
        $data =
            [
                'mode' => 'tambah',
                'action' => url('dashboard/pengguna/create'),
                'name' => old('name') ? old('name') : '',
                'address' => old('address') ? old('address') : '',
                'email' => old('email') ? old('email') : '',
                'avatar' => url('uploads/blank.png'),
                'avatarText' => 'blank.png',
                'level' => old('level') ? old('level') : '',
                'active' => old('active') ? old('active') : '',
                'username' => old('username') ? old('username') : '',
                'password' => old('password') ? old('password') : '',
                'password_confirmation' => old('password_confirmation') ? old('password_confirmation') : '',
                'listPerangkat' => PerangkatDaerah::where('jenis','!=','tu')->pluck('id_opd', 'nama_opd')->all(),
                'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : '',

            ];
        return view('dashboard_page.pengguna.form', $data);
    }

    public function create(Request $request)
    {

        $rule = User::$validationRule;
        $rule['username'] = 'required|unique:users,username';
        $rule['password'] = 'required|string|confirmed|min:8';
        $rule['email'] = 'required|string|email|max:255|unique:users';
        $rule['level'] = 'required';
        $rule['active'] = 'required';
        if ($request->hasFile('avatar')) {
            $rule['avatar'] = 'max:1024|mimes:jpg,png';
        }
        $this->validate($request,
            $rule,
            [],
            User::$attributeRule,
        );

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $avatar = $this->uploadImage($image, 'uploads', 'uploads/thumbnail');
        } else {
            $avatar = null;
        }

        $dataCreate = array(
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'level' => $request->input('level'),
            'active' => $request->input('active'),
            'avatar' => $avatar
        );

        $level = $request->input('level');
        if ($level == 'sespri') {
            $dataCreate['id_opd_fk'] = $request->input('id_opd_fk');
        } else if ($level == 'umum') {
            $dataCreate['id_opd_fk'] = cekIdBiroUmum();
        } else if ($level == 'adpim') {
            $dataCreate['id_opd_fk'] = cekIdBiroAdpim();
        } else {
            $dataCreate['id_opd_fk'] = null;
        }

        $insert = User::create($dataCreate);

        if ($insert) :
            saveLogs('menambahkan data ' . $request->input('name') . ' pada fitur pengguna');
            return redirect(route('pengguna'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Pengguna berhasil ditambahkan',
                    'judul' => 'Data Pengguna'
                ]);
        else :
            return redirect(route('pengguna.form'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Pengguna gagal ditambahkan',
                    'judul' => 'Data Pengguna'
                ]);
        endif;

    }

    public function edit($id)
    {
        $checkData = User::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'mode' => 'ubah',
                    'action' => url('dashboard/pengguna/update/' . $id),
                    'name' => old('name') ? old('name') : $dataMaster->name,
                    'email' => old('email') ? old('email') : $dataMaster->email,
                    'avatar' => $dataMaster->avatar ? assetRoot('uploads/' . $dataMaster->avatar) : url('uploads/blank.png'),
                    'avatarText' => $dataMaster->avatar == '' ? 'blank.png' : $dataMaster->avatar,
                    'level' => old('level') ? old('level') : $dataMaster->level,
                    'active' => old('active') ? old('active') : $dataMaster->active,
                    'username' => old('username') ? old('username') : $dataMaster->username,
                    'password' => old('password') ? old('password') : '',
                    'password_confirmation' => old('password_confirmation') ? old('password_confirmation') : '',
                    'id' => old('password_confirmation') ? old('password_confirmation') : '',
                    'listPerangkat' => PerangkatDaerah::where('jenis','!=','tu')->pluck('id_opd', 'nama_opd')->all(),
                    'id_opd_fk' => old('id_opd_fk') ? old('id_opd_fk') : $dataMaster->id_opd_fk,

                ];
            return view('dashboard_page.pengguna.form', $data);
        else :
            return redirect(route('pengguna'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Pengguna tidak ditemukan',
                    'judul' => 'Halaman Pengguna'
                ]);
        endif;
    }

    public function update($id, Request $request)
    {
        $rule = User::$validationRule;
        $idDecode = Hashids::decode($id)[0];
        $dataMaster = User::find($idDecode);
        $rule['username'] = 'required|unique:users,username,' . $idDecode . ',id';
        $rule['email'] = 'required|string|email|max:255|unique:users,email,' . $idDecode . ',id';
        $rule['level'] = 'required';
        $rule['active'] = 'required';
        if ($request->input('password')) {
            $rule['password'] = 'string|confirmed|min:8';
        }
        if ($request->hasFile('avatar')) {
            $rule['avatar'] = 'max:1024|mimes:jpg,png';
        }
        $this->validate($request,
            $rule,
            [],
            User::$attributeRule,
        );

        if ($request->hasFile('avatar')) :
            $image = $request->file('avatar');
            $avatar = $this->uploadImage($image, 'uploads', 'uploads/thumbnail');
            $this->deleteFile('uploads', $dataMaster['avatar']);
            $this->deleteFile('uploads/thumbnail', $dataMaster['avatar']);
        else :
            $remove_avatar = $request->input('remove_avatar');
            if ($remove_avatar) :
                $this->deleteFile('uploads', $dataMaster['avatar']);
                $this->deleteFile('uploads/thumbnail', $dataMaster['avatar']);
                $avatar = '';
            else :
                $avatar = $dataMaster->avatar;
            endif;
        endif;

        $dataUpdate = [
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'level' => $request->input('level'),
            'phone' => $request->input('phone'),
            'active' => $request->input('active'),
            'address' => $request->input('address'),
            'avatar' => $avatar
        ];

        $level = $request->input('level');
        if ($level == 'sespri') {
            $dataUpdate['id_opd_fk'] = $request->input('id_opd_fk');
        } else if ($level == 'umum') {
            $dataUpdate['id_opd_fk'] = cekIdBiroUmum();
        } else if ($level == 'adpim') {
            $dataUpdate['id_opd_fk'] = cekIdBiroAdpim();
        } else {
            $dataUpdate['id_opd_fk'] = null;
        }

        if ($request->input('password')) {
            $dataUpdate['password'] = Hash::make($request->input('password'));
        }

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if ($update) :
            saveLogs('memperbarui data ' . $request->input('name') . ' pada fitur pengguna');
            return redirect(url('dashboard/pengguna/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Pengguna berhasil diupdate',
                    'judul' => 'Data Pengguna'
                ]);
        else :
            return redirect(url('dashboard/pengguna/edit/' . $id))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Pengguna gagal diupdate',
                    'judul' => 'Data Pengguna'
                ]);
        endif;
    }

    public function show($id)
    {
        $checkData = User::find(Hashids::decode($id));
        if (count($checkData) > 0) :
            $dataMaster = $checkData[0];
            $data =
                [
                    'id' => $id,
                    'iduser' => $dataMaster->id,
                    'name' => $dataMaster->name,
                    'email' => $dataMaster->email,
                    'avatar' => $dataMaster->avatar ? assetRoot('uploads/' . $dataMaster->avatar) : url('uploads/blank.png'),
                    'thumb' => $dataMaster->avatar ? assetRoot('uploads/thumbnail/' . $dataMaster->avatar) : url('uploads/blank.png'),
                    'level' => $dataMaster->level,
                    'active' => $dataMaster->active,
                    'created_at' => $dataMaster->created_at,
                    'warnaActive' => $dataMaster->active ? '<span class="badge badge-pill badge-light-success"> AKTIF </span>' : '<span class="badge badge-pill badge-light-danger"> NON AKTIF </span>',
                    'username' => $dataMaster->username,
                ];
            return view('dashboard_page.pengguna.show', $data);
        else :
            return redirect(route('pengguna'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Pengguna tidak ditemukan',
                    'judul' => 'Halaman Pengguna'
                ]);
        endif;
    }

    public function sideProfil(Request $request)
    {
        if ($request->ajax()) {
            $segment = $request->get('segment');
            if (Auth::user()->level == 'opd') :
                $view = 'dashboard_page.opd.side';
                $checkData = User::find(Auth::user()->id);
                $dataMasterOPD = Opd::where('opd_id', Auth::user()->id_jenis)->first();
                $data = [
                    'avatar' => $checkData->avatar ? assetRoot('uploads/' . $checkData->avatar) : url('uploads/blank.png'),
                    'opd_kontak' => $dataMasterOPD->opd_kontak,
                    'opd_website' => $dataMasterOPD->opd_website,
                    'opd_alamat' => $dataMasterOPD->opd_alamat,
                    'opd_detail' => $dataMasterOPD->opd_detail,
                    'segment' => $segment
                ];
            else:
                $view = 'dashboard_page.pengguna.side';
                $checkData = User::find(Auth::user()->id);
                $data = [
                    'avatar' => $checkData->avatar ? assetRoot('uploads/' . $checkData->avatar) : url('uploads/blank.png'),
                    'segment' => $segment
                ];
            endif;
            return view($view, $data)->render();
        } else {
            return false;
        }
    }

    public function profil()
    {
        if (Auth::user()->level == 'opd') :
            $view = 'dashboard_page.opd.profil';
            $dataMasterOPD = Opd::where('opd_id', Auth::user()->id_jenis)->first();
            $data = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'avatar' => Auth::user()->avatar ? assetRoot('uploads/' . Auth::user()->avatar) : url('uploads/blank.png'),
                'level' => Auth::user()->level,
                'active' => Auth::user()->active,
                'username' => Auth::user()->username,
                'opd_kontak' => $dataMasterOPD->opd_kontak,
                'opd_website' => $dataMasterOPD->opd_website,
                'opd_alamat' => $dataMasterOPD->opd_alamat,
                'opd_detail' => $dataMasterOPD->opd_detail,
            ];
        else :
            $view = 'dashboard_page.pengguna.profil';
            $data = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'avatar' => Auth::user()->avatar ? assetRoot('uploads/' . Auth::user()->avatar) : url('uploads/blank.png'),
                'level' => Auth::user()->level,
                'active' => Auth::user()->active,
                'username' => Auth::user()->username,
            ];
        endif;

        return view($view, $data);
    }

    public function updateProfil(Request $request)
    {
        $rule = User::$validationRule;
        $idDecode = Auth::user()->id;
        $dataMaster = User::find($idDecode);
        $rule['username'] = 'required|unique:users,username,' . $idDecode . ',id';
        $rule['email'] = 'required|string|email|max:255|unique:users,email,' . $idDecode . ',id';
        if ($request->hasFile('avatar')) {
            $rule['avatar'] = 'max:1024|mimes:jpg,png';
        }
        $this->validate($request,
            $rule,
            [],
            User::$attributeRule,
        );


        if ($request->hasFile('avatar')) :
            $image = $request->file('avatar');
            $avatar = $this->uploadImage($image, 'uploads', 'uploads/thumbnail');
            $this->deleteFile('uploads', $dataMaster['avatar']);
            $this->deleteFile('uploads/thumbnail', $dataMaster['avatar']);
        else :
            $remove_avatar = $request->input('remove_avatar');
            if ($remove_avatar) :
                $this->deleteFile('uploads', $dataMaster['avatar']);
                $this->deleteFile('uploads/thumbnail', $dataMaster['avatar']);
                $avatar = '';
            else :
                $avatar = $dataMaster->avatar;
            endif;
        endif;

        $dataUpdate = [
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar' => $avatar
        ];

        //simpan perubahan
        $update = $dataMaster->update($dataUpdate);

        if (Auth::user()->level == 'opd'):
            $dataMasterOPD = Opd::find($dataMaster->id_jenis);
            $dataOPD = array(
                'opd_nama' => $request->input('name'),
                'opd_detail' => $request->input('opd_detail'),
                'opd_kontak' => $request->input('opd_kontak'),
                'opd_website' => $request->input('opd_website'),
                'opd_alamat' => $request->input('opd_alamat'),
            );

            $dataMasterOPD->update($dataOPD);
        endif;

        if ($update) :
            saveLogs('memperbarui profil');
            return redirect(route('profil'))
                ->with('pesan_status', [
                    'tipe' => 'success',
                    'desc' => 'Profil berhasil diupdate',
                    'judul' => 'Data Profil'
                ]);
        else :
            return redirect(route('profil'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Profil gagal diupdate',
                    'judul' => 'Data Profil'
                ]);
        endif;
    }

    public function ubahPassword()
    {
        return view('dashboard_page.pengguna.password');
    }

    public function updatePassword(Request $request)
    {
        $idDecode = Auth::user()->id;
        $dataMaster = User::find($idDecode);

        $user = Auth::user();
        $this->validate($request, [
            'password_old' => 'required',
            'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:8',
        ]);

        if (Hash::check($request->input('password_old'), $user->password)) {
            $dataUpdate = [
                'password' => Hash::make($request->input('password')),
            ];
            //simpan perubahan
            $update = $dataMaster->update($dataUpdate);
            if ($update) :
                saveLogs('memperbarui password');
                return redirect(route('ubah-password'))
                    ->with('pesan_status', [
                        'tipe' => 'success',
                        'desc' => 'Password berhasil diupdate',
                        'judul' => 'Data Profil'
                    ]);
            else :
                return redirect(route('ubah-password'))
                    ->with('pesan_status', [
                        'tipe' => 'error',
                        'desc' => 'Password gagal diupdate',
                        'judul' => 'Data Profil'
                    ]);
            endif;
        } else {
            return redirect(route('ubah-password'))
                ->with('pesan_status', [
                    'tipe' => 'error',
                    'desc' => 'Password lama tidak sesuai',
                    'judul' => 'Data Profil'
                ]);
        }
    }
}
