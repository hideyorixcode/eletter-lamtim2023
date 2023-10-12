<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDokumen extends Model
{
    use HasFactory;

    protected $table = 'dtl_tbl_dokumen';
    protected $primaryKey = 'id';
    protected $fillable = [
        'dokumen_id',
        'tanggal_dokumen',
        'nomor_dokumen',
        'tentang_dokumen',
        'opd_id',
        'created_by',
        'updated_by',
    ];

    public function scopeActive($query)
    {
        //return $query->where('active', 1);
    }


    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';


    public static $validationRule = [
        'dokumen_id' => 'required',
        'tanggal_dokumen' => 'required',
        'nomor_dokumen' => 'required',
        'opd_id' => 'required',
    ];

    public static $attributeRule = [
        'dokumen_id' => 'Pilih Dokumen',
        'tanggal_dokumen' => 'Tanggal Dokumen',
        'nomor_dokumen' => 'Nomor Dokumen',
        'tentang_dokumen' => 'Tentang Dokumen',
    ];

    public function pns()
    {
        return $this->hasMany(PNS::class, 'dokumen_id', 'id');
    }

    public function opd()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'opd_id', 'id_opd');
    }

    public function master()
    {
        return $this->belongsTo(MasterDokumen::class, 'dokumen_id', 'dokumen_id');
    }

}
