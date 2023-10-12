<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'level',
        'id_opd_fk',
        'id_jenis_ttd_fk',
        'active',
        'id_jenis',
        'fcm_token',
        'api_token',
        'expire_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'expire_token' => 'datetime'
    ];

    /**
     * Scope a query to only include
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */


    public static $rules = [
        'username' => 'required',
        'name' => 'required',
        'email' => 'required|email',
    ];

    public static $validationRule = [
        'name' => 'required|string|max:255',
        'phone' => 'numeric',
    ];

    public static $attributeRule = [
        'name' => 'nama lengkap',
        'active' => 'status keaktifan',
    ];

    public function scopePerangkat($query)
    {
        return $query->leftJoin('tbl_opd', 'id_opd', '=', 'id_opd_fk')
            ->select(['users.*', 'nama_opd as nama_opd']);
    }

    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }

    public function opd()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'id_opd_fk', 'id_opd');
    }

    public function jenisttd()
    {
        return $this->belongsTo(JenisPenandatangan::class, 'id_jenis_ttd_fk', 'id_jenis_ttd');
    }
}
