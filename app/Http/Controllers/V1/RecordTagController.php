<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\RecordTagRepositoryInterface;
use App\Http\Requests\V1\RecordTagRequest;
use App\Http\Controllers\BaseController;
use Carbon\Carbon;

class RecordTagController extends BaseController
{
    /**
     * @var RecordTagRepositoryInterface
     */
    private $recordTagRepository;

    /**
     * RecordTagController constructor.
     * @param RecordTagRepositoryInterface $recordTagRepository
     */
    public function __construct(RecordTagRepositoryInterface $recordTagRepository) 
    {
        $this->recordTagRepository = $recordTagRepository;
    }

    /**
     * @param null
     */
    public function index() 
    {
        try {
            $recordTags = $this->recordTagRepository->allBy([
                'type' => RECORDTYPE_RECORD_KEYWORD_VALUE, 
                'chg' => CHG_VALID_VALUE
            ]);
            return $this->sendResponse($recordTags, 'Get recordTag list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function detail($id) 
    {
        try {
            $recordTag = $this->recordTagRepository->findById($id);
            if($recordTag) {
                return $this->sendResponse($recordTag, 'Get recordTag detail successfully.');
            }
            return $this->sendError("RecordTag not found with ID : $id!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function store(RecordTagRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['time'] = $request->time;
            $input['record_item'] = $request->record_item;
            $input['tag'] = $request->tag;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordTag = $this->recordTagRepository->create($input);
            if($recordTag) {
                return $this->sendResponse($recordTag, 'Create recordTag successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param RecordTagRequest $request
     */
    public function update(RecordTagRequest $request)
    {
        try {
            $recordTag = $this->recordTagRepository->findById($request->id);
            if(!$recordTag) {
                return $this->sendError("RecordTag not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordTag = $this->recordTagRepository->update($request->id, $input);
            if($recordTag) {
                return $this->sendResponse($recordTag, 'Update recordTag successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function delete(Request $request)
    {
        try {
            $recordTag = $this->recordTagRepository->findById($request->id);
            if(!$recordTag) {
                return $this->sendError("RecordTag not found with ID : $request->id!", 404);
            }
            $this->recordTagRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete recordTag successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    
}
