<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Keyword extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    protected $table = 'keywords';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'name',
        'color',
        'user',
        'vx01',
        'vx02',
        'remark',
        'chg',
        'new_by',
        'new_ts',
        'upd_by',
        'upd_ts'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    public function mediaKeyword()
    {
        return $this->hasOne('App\Models\MediaKeyword', 'keyword');
    }
}
