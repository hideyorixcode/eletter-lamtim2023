<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPenandatangan extends Model
{
    use HasFactory;

    protected $table = 'tbl_jenis_ttd';
    protected $primaryKey = 'id_jenis_ttd';
    protected $fillable = [
        'jenis_ttd',
        'nik',
        'id_opd_fk',
        'active',
        'img_ttd',
        'created_by',
        'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }


    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';


    public static $validationRule = [
        'active' => 'required',
        'id_opd_fk' => 'required',
//        'cert' => 'max:512',
//        'priv_key' => 'max:512'
    ];

    public static $attributeRule = [
        'jenis_ttd' => 'Jenis Penandatangan',
        'active' => 'Status Aktif',
    ];

    public function scopeOpd($query)
    {
        return $query->leftjoin('tbl_opd', 'id_opd', '=', 'id_opd_fk')->select(['tbl_jenis_ttd.*', 'nama_opd']);
    }

    public function masterdokumen()
    {
        return $this->hasMany(MasterDokumen::class, 'dokumen_jenis_ttd', 'id_jenis_ttd');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_jenis_ttd_fk', 'id_jenis_ttd');
    }

    public function scoperelasiuser($query)
    {
        return $query->leftjoin('users', 'id_jenis_ttd_fk', '=', 'id_jenis_ttd')->select(['tbl_jenis_ttd.*', 'email','username','id']);
    }

}
