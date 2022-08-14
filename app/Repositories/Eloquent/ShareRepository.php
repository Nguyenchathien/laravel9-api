<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ShareRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Share;

class ShareRepository extends BaseRepository implements ShareRepositoryInterface
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
    public function __construct(Share $model)
    {
        $this->model = $model;
    }

    public function accepted($id, array $data)
    {
        return Share::whereId($id)->update($data);
    }

    public function getListSharing($id)
    {
        return Share::where(['status' => STATUS_ACCEPT_VALUE, 'user' => Auth::user()->id, 'to' => $id, 'chg' => CHG_VALID_VALUE])->get();
    }

    public function getListSharedRecords($fromId, $toId)
    {
        return Share::where(['status' => STATUS_ACCEPT_VALUE, 'user' => $fromId, 'to' => $toId, 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }

    public function getListReceivedRecords()
    {
        return Share::where(['status' => STATUS_REQUEST_VALUE, 'to' => Auth::id() , 'chg' => CHG_VALID_VALUE])->orderBy('id', 'desc')->get();
    }
}