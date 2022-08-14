<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Models\RecordItem;

class RecordItemRepository extends BaseRepository implements RecordItemRepositoryInterface
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
    public function __construct(RecordItem $model)
    {
        $this->model = $model;
    }

    public function getItem($recordId)
    {
        return RecordItem::where(['record' =>  $recordId,  'chg' => CHG_VALID_VALUE])->get();
    }
}