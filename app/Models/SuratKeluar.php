<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'tbl_surat_keluar';
    protected $primaryKey = 'id';
    protected $fillable = [
        'no_surat',
        'tgl_surat',
        'id_opd_fk',
        'kepada_id_opd',
        'kepada_opd',
        'tujuan',
        'kepada',
        'lampiran',
        'perihal',
        'kategori_ttd',
        'id_jenis_ttd_fk',
        'berkas',
        'qrcode',
        'x',
        'y',
        'halaman',
        'width',
        'height',
        'status_sk',
        'diisi_oleh',
        'bagikan_tu',
        'catatan',
        'tanggapan',
        'is_download',
        'hash',
	'id_visualisasi',
        'created_by',
        'updated_by',
    ];


    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';


    public static $validationRule = [
        'tgl_surat' => 'required',
        'id_opd_fk' => 'required',
        //'kepada' => 'required',
        'lampiran' => 'required',
        'perihal' => 'required',
//        'id_jenis_ttd_fk' => 'required',
    ];

    public static $attributeRule = [
        'no_surat' => 'No Surat',
        'tgl_surat' => 'Tgl Surat',
        'id_opd_fk' => 'Pilih Perangkat Daerah',
        'kepada' => 'Tujuan Kepada',
        'lampiran' => 'Jumlah Lampiran',
        'perihal' => 'Perihal Surat',
        'id_jenis_ttd_fk' => 'Jenis Tanda Tangan',
        'berkas' => 'Berkas',
    ];

    public function scopeOpd($query)
    {
        return $query->join('tbl_opd', 'id_opd', '=', 'id_opd_fk')->leftjoin('v_penandatangan', 'id_jenis_ttd','=','id_jenis_ttd_fk')
            ->select(['tbl_surat_keluar.*', 'nama_opd', 'jenis_ttd', 'nama_opd_penandatangan']);
    }

    public function ttd()
    {
        return $this->belongsTo(JenisPenandatangan::class, 'id_jenis_ttd_fk');
    }
}
