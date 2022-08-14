<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class People extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'peoples';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'org',
        'dept',
        'name',
        'doctor',
        'user',
        'post',
        'pref',
        'pref_code',
        'address',
        'xaddress',
        'remark',
        'phone',
        'email',
        'chg',
        'new_by',
        'new_ts',
        'upd_by',
        'upd_ts',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    public function share(){
        return $this->hasMany(Share::class , 'to');
    }
}
