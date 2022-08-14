<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Models\Media;
use Carbon\Carbon;


class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
/**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    public function importFile($file){
        $original_name=$file->file('fpath')->getClientOriginalName();
        $size=$file->file('fpath')->getSize();
        $extension=$file->file('fpath')->getClientOriginalExtension();
        $filename= Carbon::now()->format('Ymd').'_'.'.'.$extension;
        $audiopath=url('/storage/upload/files/file/'.$filename);
        $path=$file->storeAs('public/upload/files/file/',$filename);
        $all_audios=$audiopath;

        return $all_audios;
    }

    public function getMedia($recordItemId)
    {
        return Media::where(['record_item' =>  $recordItemId,  'chg' => CHG_VALID_VALUE])->first();
    }
    public function getMedias($recordId)
    {
        return Media::where(['record' =>  $recordId,  'chg' => CHG_VALID_VALUE])->get();
    }
    public function getImages($recordId)
    {
        return Media::where(['record' =>  $recordId, 'mime' => MIME_IMAGE_VALUE,  'chg' => CHG_VALID_VALUE])->get();
    }
}