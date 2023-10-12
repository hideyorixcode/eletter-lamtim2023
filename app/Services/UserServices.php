<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserServices {
    public function create($data)
    {
        $level = isset($data['level']) ? $data['level'] : 'pencaker';
        $insert = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => 0,
            'level' => $level,
            'id_jenis' => $data['id_jenis'],
            'created_by' => auth()->id()
        ]);
        if($insert) {
            return $insert;
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $user = User::find($id);
        if(isset($data['name']))
            $user->name = $data['name'];
        if(isset($data['avatar'])) {
            if (file_exists('uploads/user/avatar/' . $user->avatar) && $user->avatar) :
                unlink('uploads/user/avatar/' . $user->avatar);
            endif;
            $fileName = setImage($data['avatar'], 'uploads/user/avatar');
            $user->avatar = $fileName;
        }
        $update = $user->save();
        if($update) {
            return $user;
        } else {
            return false;
        }
    }
}
