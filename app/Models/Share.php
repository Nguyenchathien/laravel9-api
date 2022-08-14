<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Share extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'shares';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'record',
        'user',
        'to',
        'mail',
        'status',
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

    public function people(){
        return $this->belongsTo(People::class, 'to');
    }

    /**
     * @return HasMany
     */
    public function records()
    {
        return $this->hasOne(Record::class, 'id', 'record')->with(['hospitals', 'peoples', 'folder', 'medicines', 'media', 'keywords']);
    }

    /**
     * @return HasOne
     */
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
}
