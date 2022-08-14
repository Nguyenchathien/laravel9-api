<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\RecordKeyword;

class RecordKeywordRepository extends BaseRepository implements RecordKeywordRepositoryInterface
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
    public function __construct(RecordKeyword $model)
    {
        $this->model = $model;
    }
    public function getRecordKeyWords($recordId)
    {
        return RecordKeyword::where(['record' =>  $recordId,  'chg' => CHG_VALID_VALUE])->get();
    }
    public function chgValid($recordKeywordId)
    {
        return RecordKeyword::where(['id' => $recordKeywordId])->update(['chg' => CHG_VALID_VALUE]);
    }
    public function chgInvalid($recordKeywordId)
    {
        return RecordKeyword::where(['id' => $recordKeywordId])->update(['chg' => CHG_INVALID_VALUE]);
    }
    public function listRecordsByKeyWord($keywordId)
    {
        return RecordKeyword::where(['new_by' => Auth::user()->id, 'keyword' => $keywordId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function updateRecordKeyword($record, $keyword, $newKey)
    {
        return RecordKeyword::where(['record' => $record, 'keyword' => $keyword])->update(['keyword' => $newKey]);
    }

    public function findRecordMedicine($record)
    {
        return RecordKeyword::where(['new_by' => Auth::user()->id, 'record' => $record, 'type' => MEDICINE_KEY_VALUE, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function listRecordsByMedicine($medicineId)
    {
        return RecordKeyword::where(['new_by' => Auth::user()->id, 'keyword' => $medicineId, 'type' => MEDICINE_KEY_VALUE, 'chg' => CHG_VALID_VALUE])->get();
    }

    public function getRecordKeyWordsNotMedicine($recordId)
    {
        return RecordKeyword::where(['record' =>  $recordId,  'chg' => CHG_VALID_VALUE, 'type' => RECORDTYPE_RECORD_KEYWORD_VALUE])->get();
    }

    /**
     * Delete model by id.
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model
    {
        $model = $this->model->create($payload);

        return $model->fresh();
    }
}
