<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
//    public function register(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
//            'password' => 'required|string|min:8'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json($validator->errors());
//        }
//
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => Hash::make($request->password)
//        ]);
//
//        $token = $user->createToken('auth_token')->plainTextToken;
//
//        return response()
//            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer',]);
//    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'Username Wajib Diisi',
                'password.required' => 'Password Wajib Diisi',
            ]
        );


        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 401;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        $newDateTime = Carbon::now()->addMonths(12);
        $res = array();

        $user = User::where('username', $request['username'])->first();
        if (!$user) {
            $httpStatus = 401;
            $res['message'] = 'User tidak ditemukan';
            $res['status'] = 'Failed';
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        if (!Auth::attempt($request->only('username', 'password'))) {
            $httpStatus = 401;
            $res['message'] = 'Password Salah';
            $res['status'] = 'Failed';
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        DB::table('users')->where('id', $user->id)
            ->update([
                'api_token' => $token,
                'expire_token' => $newDateTime,
            ]);
        // $res['token'] = hash('sha256', $token);
        $httpStatus = 200;
        $res['message'] = 'Berhasil Login!';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $res['access_token'] = $token;
        $res['token_type'] = 'Bearer';
        $exp = '' . $newDateTime;
        $res['expire_token'] = $exp;


        return response()->json($res, $httpStatus);
    }


// method for user logout and delete token
    public
    function logout()
    {
        auth()->user()->tokens()->delete();

        DB::table('users')->where('id', Auth::user()->id)
            ->update([
                'api_token' => null,
                'expire_token' => null,
            ]);
        return [
            'message' => 'Sukses logout dan token terhapus'
        ];
    }

    public function getProfil(Request $request)
    {

        $dataProfil = User::where('id', Auth::user()->id)->first();
        if (!$dataProfil) {

            $httpStatus = 204;
            $res['message'] = 'Data Profil Tidak Ditemukan';
            $res['status'] = 'OK';
            $res['http_status'] = $httpStatus;
            $res['data'] = $dataProfil;
            return response()->json($res, $httpStatus);
        }

        $httpStatus = 200;
        $res['message'] = 'Data Profil Ditemukan';
        $res['status'] = 'OK';
        $res['http_status'] = $httpStatus;
        $avatar = null;
        if ($dataProfil->avatar) {
            $avatar = url('uploads/' . $dataProfil->avatar);
        }
        $hasil = [
            'id' => $dataProfil->id,
            'username' => $dataProfil->username,
            'name' => $dataProfil->name,
            'avatar' => $avatar,
            'id_opd_fk' => $dataProfil->id_opd_fk,
            'opd' => $dataProfil->opd->nama_opd,
            'level' => $dataProfil->level,
            'email' => $dataProfil->email,
        ];

        if ($dataProfil->level == 'penandatangan') {
            $hasil['id_jenis_ttd_fk'] = $dataProfil->id_jenis_ttd_fk;
            $hasil['nik'] = $dataProfil->jenisttd->nik;
            if ($dataProfil->jenisttd->img_ttd != null) {
                $hasil['memiliki_tte'] = true;
                $hasil['img_ttd'] = $dataProfil->jenisttd->img_ttd;
            } else {
                $hasil['memiliki_tte'] = false;
            }

        }

        $res['data'] = $hasil;
        return response()->json($res, $httpStatus);
    }

    public function ubahProfil(Request $request)
    {
        $rule = User::$validationRule;
        $idDecode = Auth::user()->id;
        $dataMaster = User::find($idDecode);
        $rule['username'] = 'required|unique:users,username,' . $idDecode . ',id';
        $rule['email'] = 'required|string|email|max:255|unique:users,email,' . $idDecode . ',id';
        if ($request->hasFile('avatar')) {
            $rule['avatar'] = 'max:1024|mimes:jpg,png';
        }

        $validator = Validator::make($request->all(),
            $rule,
            [],
            User::$attributeRule,
        );


        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 401;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        try {

            DB::beginTransaction();
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
            $dataMaster->update($dataUpdate);
            DB::commit();
            $pesan = 'berhasil ubah Profil!';
            $status = 'OK';
            $http = 200;
            $datanya = $dataUpdate;
        } catch (\Exception $e) {
            DB::rollback();
            //throw $e;
            $pesan = $e->getMessage();
            $status = 'Failed';
            $http = 500;
            $datanya = null;
        }


        $res['message'] = $pesan;
        $res['status'] = $status;
        $res['http_status'] = $http;
        if ($http == 200) {
            $res['data'] = $datanya;
        }

        return response()->json($res, $http);


    }

    public function updatePassword(Request $request)
    {
        $idDecode = Auth::user()->id;
        $dataMaster = User::find($idDecode);

        $user = Auth::user();

        $validator = Validator::make($request->all(),
            [
                'password_old' => 'required',
                'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:8',
            ]
        );


        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 202;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        try {


            if (Hash::check($request->input('password_old'), $user->password)) {
                DB::beginTransaction();
                $dataUpdate = [
                    'password' => Hash::make($request->input('password')),
                ];
                //simpan perubahan
                $dataMaster->update($dataUpdate);
                DB::commit();
                $pesan = 'Password berhasil diupdate!';
                $status = 'OK';
                $http = 200;

            } else {
                $pesan = 'Password lama tidak sesuai!';
                $status = 'Failed';
                $http = 202;

            }


        } catch (\Exception $e) {
            DB::rollback();
            //throw $e;
            $pesan = $e->getMessage();
            $status = 'Failed';
            $http = 500;

        }


        $res['message'] = $pesan;
        $res['status'] = $status;
        $res['http_status'] = $http;


        return response()->json($res, $http);
    }

    public function storeFcmToken(Request $request)
    {
        $idDecode = Auth::user()->id;
        $dataMaster = User::find($idDecode);

        $validator = Validator::make($request->all(),
            [
                'fcm_token' => 'required',
            ]
        );


        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            $res['message'] = $errorString;
            $res['status'] = 'Failed';
            $httpStatus = 202;
            $res['http_status'] = $httpStatus;
            return response()->json($res, $httpStatus);
        }

        try {


            DB::beginTransaction();
            $dataUpdate = [
                'fcm_token' => $request->input('fcm_token'),
            ];
            //simpan perubahan
            $dataMaster->update($dataUpdate);
            DB::commit();
            $pesan = 'Fcm Token berhasil diupdate!';
            $status = 'OK';
            $http = 200;


        } catch (\Exception $e) {
            DB::rollback();
            //throw $e;
            $pesan = $e->getMessage();
            $status = 'Failed';
            $http = 500;

        }


        $res['message'] = $pesan;
        $res['status'] = $status;
        $res['http_status'] = $http;


        return response()->json($res, $http);
    }
}
