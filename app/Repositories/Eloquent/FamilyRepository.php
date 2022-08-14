<?php

namespace App\Repositories\Eloquent;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\FamilyRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\People;

class FamilyRepository extends BaseRepository  implements FamilyRepositoryInterface
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
    public function __construct(People $model)
    {
        $this->model = $model;
    }


    public function accepted($id, array $data)
    {
        return People::whereId($id)->update($data);
    }

    public function getListRequests($email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'email' => $email, 'chg' => REQUEST_STATUS_WAIT_VALUE])->get();
    }

    public function getListRequestsJoin($email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'email' => $email, 'chg' => REQUEST_STATUS_JOIN_VALUE])->get();
    }

    public function removeFamily($id, $email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'user' => $id, 'email' => $email, 'chg' => CHG_VALID_VALUE])->update(['chg' => CHG_INVALID_VALUE]);
    }

    public function sendAgain($id, $email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'user' => $id, 'email' => $email, 'chg' => REQUEST_STATUS_DENIED_VALUE])->update(['chg' => REQUEST_STATUS_WAIT_VALUE]);
    }

    public function addAgain($id, $email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'user' => $id, 'email' => $email, 'chg' => CHG_INVALID_VALUE])->update(['chg' => CHG_VALID_VALUE]);
    }

    public function findFamily($userId, $email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'user' => $userId, 'email' => $email,])->first();
    }

    public function findFamilyValid($userId, $email)
    {
        return People::where(['type' => FAMILY_KEY_VALUE, 'user' => $userId, 'email' => $email, 'chg' => CHG_VALID_VALUE])->first();
    }
    // public function getAll()
    // {
    //     return People::where(['type' => FAMILY_KEY_VALUE, 'user' => Auth::user()->id, 'chg' => CHG_VALID_VALUE])->get();
    // }

    // public function getDetail($id)
    // {
    //     return People::where(['id' => $id, 'user' => Auth::user()->id, 'chg' => CHG_VALID_VALUE])->first();
    // }

    // public function create(array $data)
    // {
    //     return People::create($data);
    // }

    // public function update($id, array $data)
    // {
    //     return People::whereId($id)->update($data);
    // }

    // public function delete($id)
    // {
    //     People::where('id', $id)->update(['chg' => CHG_DELETE_VALUE]);
    // }
}