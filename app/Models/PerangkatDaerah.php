<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerangkatDaerah extends Model
{
    use HasFactory;

    protected $table = 'tbl_opd';
    protected $primaryKey = 'id_opd';
    protected $fillable = [
        'nama_opd',
        'alias_opd',
        'alamat_opd',
        'email_opd',
        'notelepon_opd',
        'jenis',
        'active',
        'status',
        'T_KUnker',
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
    ];

    public static $attributeRule = [
        'nama_opd' => 'Nama Perangkat Daerah',
        'alias_opd' => 'Alias Perangkat Daerah',
        'active' => 'Status Aktif',
    ];

    public function scopeOpd($query, $idopd)
    {
        $getResult = $query->select(['nama_opd', 'alias_opd'])->where('id_opd', $idopd)->first();
        if ($getResult != null) {
            return $getResult->nama_opd;
        } else {
            return '-';
        }

    }

    public function pns_detaildokumen()
    {
        return $this->hasMany(DetailDokumen::class, 'opd_id', 'id_opd');
    }

    public function dokumentte()
    {
        return $this->hasMany(DokumenTTE::class, 'id_opd_fk', 'id_opd');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_opd_fk', 'id_opd');
    }

    public function suratmasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'kepada', 'id_opd');
    }


}
