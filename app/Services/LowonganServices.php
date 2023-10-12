<?php

namespace App\Services;

use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LowonganServices {
    public function create($data)
    {
        if(isset($data['negosiasi']) && $data['negosiasi'] == 'Negosiasi')
            $sallary = 'Negosiasi   ';
        else
            $sallary = 'Rp. '. $data['gaji_min'] . ' - Rp. ' . $data['gaji_maks'];

        $perusahaan = User::select('*', 'users.id as id')->pencaker()->where('users.id', auth()->id())->first();
        $today_time = strtotime(now());
        $open_time = strtotime($data['loker_tgl']);

        if ($open_time < $today_time)
            $status = 'Terbuka';
        else
            $status = 'Tertutup';

        $insert = Lowongan::create([
            'loker_sallary' => $sallary,
            'loker_created_by' => auth()->id(),
            'loker_posisi' => $data['loker_posisi'],
            'loker_deskripsi' => $data['loker_deskripsi'],
            'loker_tgl' => $data['loker_tgl'],
            'loker_expire' => $data['loker_expire'],
            'loker_status' => $status,
            'loker_id_perusahaan' => $perusahaan->perusahaan_id,
            'loker_id_sub' => $data['loker_id_sub']
        ]);
        if($insert) {
            return $insert;
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $perusahaan = Perusahaan::find($id);

        $perusahaan->perusahaan_nama = $data['perusahaan_nama'];
        $perusahaan->perusahaan_alamat = $data['perusahaan_alamat'];
        $perusahaan->perusahaan_provinsi = $data['perusahaan_provinsi'];
        $perusahaan->perusahaan_kabkota = $data['perusahaan_kabkota'];
        $perusahaan->perusahaan_kode_pos = $data['perusahaan_kode_pos'];
        if(isset($data['perusahaan_email']))
            $perusahaan->perusahaan_email = $data['perusahaan_email'];
        $perusahaan->perusahaan_telpon = $data['perusahaan_telpon'];
        $perusahaan->perusahaan_fax = $data['perusahaan_fax'];
        $perusahaan->perusahaan_nama_pic = $data['perusahaan_nama_pic'];
        $perusahaan->perusahaan_jabatan_pic = $data['perusahaan_jabatan_pic'];
        $perusahaan->perusahaan_keterangan = $data['perusahaan_keterangan'];
        $perusahaan->perusahaan_updated_by = auth()->id();

        $update = $perusahaan->save();

        if($update) {
            return $perusahaan;
        } else {
            return false;
        }
    }

    public function updateStatusLowongan() {
        Log::channel('stack')->info('UpdateStatusLowongan Running!');
        Lowongan::whereDate('loker_tgl', '<', now())->whereDate('loker_expire', '>', now())->update([
            'loker_status' => 'Terbuka'
        ]);
        Lowongan::whereDate('loker_expire', '<', now())->update([
            'loker_status' => 'Tertutup'
        ]);
    }

}
