<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RecordTagRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\RecordTag;

class RecordTagRepository extends BaseRepository implements RecordTagRepositoryInterface
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
    public function __construct(RecordTag $model)
    {
        $this->model = $model;
    }

    public function getRecordTags($recordItemId)
    {
        return RecordTag::where(['record' =>  $recordItemId,  'chg' => CHG_VALID_VALUE])->get();
    }
}