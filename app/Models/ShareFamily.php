<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ShareFamily extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'share_families';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'from',
        'to',
        'mail',
        'chg',
        'new_by',
        'new_ts',
        'upd_by',
        'upd_ts'
    ];
}
