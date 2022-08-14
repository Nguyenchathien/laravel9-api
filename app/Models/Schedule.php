<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Schedule extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'schedules';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'title',
        'color',
        'date',
        'hospital',
        'people',
        'remark',
        'user',
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

    public function hospital()
    {
        return $this->hasOne('App\Models\Hospital', 'id', 'hospital');
    }

    public function people()
    {
        return $this->hasOne('App\Models\People', 'id', 'people');
    }
}
