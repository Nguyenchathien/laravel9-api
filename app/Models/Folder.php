<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Folder extends Model
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'folders';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'pid',
        'name',
        'type',
        'color',
        'user',
        'chg',
        'new_by',
        'new_ts',
        'upd_by',
        'upd_ts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    public function records()
    {
        return $this->hasMany(Record::class, 'folder')->where('chg', CHG_VALID_VALUE);
    }
}
