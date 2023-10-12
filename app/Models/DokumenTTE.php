<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenTTE extends Model
{
    use HasFactory;

    protected $table = 'tbl_dokumen_tte';
    protected $primaryKey = 'id';
    protected $fillable = [
        'no_dokumen',
        'tgl_dokumen',
        'perihal',
        'kategori_ttd',
        'id_jenis_ttd_fk',
        'id_opd_fk',
        'berkas',
        'qrcode',
        'x',
        'y',
        'halaman',
        'width',
        'height',
        'hash',
        'status_dokumen',
	'id_visualisasi',
        'created_by',
        'updated_by',
    ];


    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';


    public static $validationRule = [
        'tgl_dokumen' => 'required',
        'perihal' => 'required',
//        'id_jenis_ttd_fk' => 'required',
    ];

    public static $attributeRule = [
        'no_dokumen' => 'No Dokumen',
        'tgl_dokumen' => 'Tgl Dokumen',
        'perihal' => 'Perihal Dokmen',
        'id_jenis_ttd_fk' => 'Jenis Tanda Tangan',
        'id_opd_fk' => 'Perangkat Daerah',
        'berkas' => 'Berkas',
    ];

    public function scopeOpd($query)
    {
        return $query->join('v_penandatangan', 'id_jenis_ttd', '=', 'id_jenis_ttd_fk')
            ->select(['tbl_dokumen_tte.*', 'jenis_ttd']);
    }

    public function ttd()
    {
        return $this->belongsTo(JenisPenandatangan::class, 'id_jenis_ttd_fk');
    }

    public function opdrelasi()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'id_opd_fk', 'id_opd');
    }

    public function scopesimpleopd($query)
    {
        return $query->join('v_penandatangan', 'id_jenis_ttd', '=', 'id_jenis_ttd_fk')
            ->select(['id','no_dokumen','tgl_dokumen','perihal','id_jenis_ttd_fk','id_opd_fk','berkas','status_dokumen','qrcode','jenis_ttd']);
    }
}
