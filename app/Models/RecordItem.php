<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RecordItem extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasFactory;
    
    protected $table = 'record_items';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'record',
        'type',
        'begin',
        'end',
        'content',
        'chg',
        'new_by',
        'new_ts',
        'upd_by',
        'upd_ts',
        'timestamp'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];
}
