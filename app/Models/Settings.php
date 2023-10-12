<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $primaryKey = 'setting_Id';
    //const CREATED_AT = 'kategori_created_at';
    const UPDATED_AT = 'setting_Updated';
    protected $fillable = [
        'setting_Label',
        'setting_Key',
        'setting_Value',
        'setting_Type',
        'setting_Updated',
    ];

}
