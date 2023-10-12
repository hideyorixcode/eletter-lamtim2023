<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $primaryKey = 'log_Id';
    const CREATED_AT = 'log_Time';
    public $timestamps = false;
    protected $fillable = [
        'log_Time',
        'log_IdUser',
        'log_Description',
    ];

}
