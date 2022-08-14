<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Hospital extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    
    protected $table = 'orgs';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'user',
        'post',
        'pref',
        'pref_code',
        'address',
        'xaddress',
        'remark',
        'phone',
        'mail',
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

}
