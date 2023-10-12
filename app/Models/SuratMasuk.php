<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'tbl_surat_masuk';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'kode',
        'indek',
        'dari',
        'kepada',
        'perihal',
        'no_surat',
        'tgl_surat',
        'lampiran',
        'sifat_surat',
        'catatan',
        'berkas',
        'qrcode',
        'hash',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';


    public static $validationRule = [
        'dari' => 'required',
        //'penerima' => 'required',
        'no_surat' => 'required',
        'tgl_surat' => 'required',
        //'tgl_masuk' => 'required',
        'perihal' => 'required',
    ];

    public static $attributeRule = [
        'dari' => 'Dari / Pengirim',
        //'penerima' => 'Penerima',
        'no_surat' => 'No Surat',
        'tgl_surat' => 'Tanggal Surat',
        // 'tgl_masuk' => 'Tanggal Masuk',
        'perihal' => 'Perihal',
        'berkas' => 'Berkas',
        'qrcode' => 'Kode QR',
    ];

    public function opd()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'kepada', 'id_opd');
    }


}
