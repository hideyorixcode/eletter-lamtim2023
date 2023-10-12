<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visualisasi extends Model
{
    use HasFactory;

    protected $table = 'visualisasi_tte';
    protected $primaryKey = 'id';
    protected $guarded = [
        'id',

    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }


    public const UPDATED_AT = 'updated_at';
    public const CREATED_AT = 'created_at';

}
