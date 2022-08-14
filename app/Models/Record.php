<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Record extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'records';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'begin',
        'end',
        'title',
        'hospital',
        'people',
        'medicine',
        'user',
        'folder',
        'media',
        'visible',
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
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function share()
    {
        return $this->hasMany(Favorite::class);
    }

    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media');
    }

    public function folder()
    {
        return $this->hasOne('App\Models\Folder', 'id', 'folder');
    }

    public function hospitals()
    {
        return $this->hasOne('App\Models\Hospital', 'id', 'hospital');
    }

    public function peoples()
    {
        return $this->hasOne('App\Models\People', 'id', 'people');
    }

    public function medicines()
    {
        return $this->hasOne('App\Models\Keyword', 'id', 'medicine');
    }

    public function keywords()
    {
        return $this->hasMany('App\Models\RecordKeyword', 'record', 'id');
    }
}
