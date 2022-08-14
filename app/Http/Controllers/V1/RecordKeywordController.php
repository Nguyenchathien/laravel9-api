<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RecordAndKeywordRequest;
use App\Http\Requests\V1\RecordKeywordRequest;
use App\Http\Requests\V1\RecordKeywordUpdateRequest;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordKeywordController extends BaseController
{
    /**
     * @var RecordKeywordRepositoryInterface
     */
    private $recordKeywordRepository;
    private $recordRepository;
    private $keywordRepository;

    /**
     * RecordKeywordController constructor.
     * @param RecordKeywordRepositoryInterface $recordKeywordRepository
     */
    public function __construct(
        RecordKeywordRepositoryInterface $recordKeywordRepository,
        RecordRepositoryInterface $recordRepository,
        RecordItemRepositoryInterface $recordItemRepository,
        MediaRepositoryInterface $mediaRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        FolderRepositoryInterface $folderRepository,
        KeywordRepositoryInterface $keywordRepository,
        FavoriteRepositoryInterface $favoriteRepository
    ) {
        $this->recordKeywordRepository = $recordKeywordRepository;
        $this->recordRepository = $recordRepository;
        $this->recordItemRepository = $recordItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->doctorRepository = $doctorRepository;
        $this->folderRepository = $folderRepository;
        $this->keywordRepository = $keywordRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $recordKeywords = $this->recordKeywordRepository->allBy([
                'type' => RECORDTYPE_RECORD_KEYWORD_VALUE,
                'chg' => CHG_VALID_VALUE,
            ]);
            return $this->sendResponse($recordKeywords, 'Get recordKeyword list successfully.');
        } catch (\Exception$e) {
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
            $recordKeyword = $this->recordKeywordRepository->findById($id);
            if ($recordKeyword) {
                return $this->sendResponse($recordKeyword, 'Get recordKeyword detail successfully.');
            }
            return $this->sendError("RecordKeyword not found with ID : $id!", 404);
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordAndKeywordRequest $request
     */
    public function createRecordKeyWords(RecordAndKeywordRequest $request)
    {
        try {
            $exsited = $this->recordKeywordRepository->findBy(['record' => $request->recordId, 'keyword' => $request->keywordId]);
            if (isset($exsited)) {
                return $this->sendError("Record $request->recordId has this keyword $request->keywordId!");
            }
            $request->validated();
            $input = $request->all();
            $input['type'] = RECORDTYPE_RECORD_KEYWORD_VALUE;
            $input['record'] = $request->recordId;
            $input['keyword'] = $request->keywordId;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordKeyword = $this->recordKeywordRepository->create($input);
            if ($recordKeyword) {
                return $this->sendResponse($recordKeyword, 'Create recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function createRecordKeyword($recordId, $keywordId)
    {
        DB::beginTransaction();
        try {
            $input['type'] = RECORDTYPE_RECORD_KEYWORD_VALUE;
            $input['record'] = $recordId;
            $input['keyword'] = $keywordId;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordKeyword = $this->recordKeywordRepository->create($input);
            if ($recordKeyword) {
                DB::commit();
                return $this->sendResponse($recordKeyword, 'Create recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function deleteRecordKeyword(RecordAndKeywordRequest $request)
    {
        try {
            $recordKeyword = $this->recordKeywordRepository->findBy(['record' => $request->recordId, 'keyword' => $request->keywordId]);
            if (!$recordKeyword) {
                return $this->sendError("Record Keyword not found with record ID : $request->recordId!", 404);
            } else {
                $recordKeywordId = $recordKeyword->id;
                $this->recordKeywordRepository->deleteById($recordKeywordId);
                return $this->sendResponse($recordKeyword, 'Delete recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordAndKeywordRequest $request
     */
    public function controlRecordKeyWord(RecordAndKeywordRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            $exsited = $this->recordKeywordRepository->findBy(['record' => $request->recordId, 'keyword' => $request->keywordId]);
            if (isset($exsited)) {
                if ($exsited['chg'] == CHG_VALID_VALUE) {
                    $recordKeywordId = $exsited->id;
                    $removed = $this->recordKeywordRepository->chgInvalid($recordKeywordId);
                    DB::commit();
                    return $this->sendResponse($removed, "Delete keyword $request->keywordId successfully.");
                }
                if ($exsited['chg'] == CHG_INVALID_VALUE) {
                    $recordKeywordId = $exsited->id;
                    $added = $this->recordKeywordRepository->chgValid($recordKeywordId);
                    DB::commit();
                    return $this->sendResponse($added, "Add keyword $request->keywordId successfully.");
                }
            }

            $input = $request->all();
            $input['type'] = RECORDTYPE_RECORD_KEYWORD_VALUE;
            $input['record'] = $request->recordId;
            $input['keyword'] = $request->keywordId;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordKeyword = $this->recordKeywordRepository->create($input);
            if ($recordKeyword) {
                DB::commit();
                return $this->sendResponse($recordKeyword, 'Create recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function store(RecordKeywordRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['type'] = RECORDTYPE_RECORD_KEYWORD_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordKeyword = $this->recordKeywordRepository->create($input);
            if ($recordKeyword) {
                return $this->sendResponse($recordKeyword, 'Create recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param RecordKeywordRequest $request
     */
    public function update(RecordKeywordRequest $request)
    {
        try {
            $recordKeyword = $this->recordKeywordRepository->findById($request->id);
            if (!$recordKeyword) {
                return $this->sendError("RecordKeyword not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $recordKeyword = $this->recordKeywordRepository->update($request->id, $input);
            if ($recordKeyword) {
                return $this->sendResponse($recordKeyword, 'Update recordKeyword successfully.');
            }
        } catch (\Exception$e) {
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
            $recordKeyword = $this->recordKeywordRepository->findById($request->id);
            if (!$recordKeyword) {
                return $this->sendError("RecordKeyword not found with ID : $request->id!", 404);
            }
            $this->recordKeywordRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete recordKeyword successfully.');
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function listRecordsByKeyword(Request $request)
    {
        try {
            $records = $this->recordRepository->listRecordsByKeyword($request->keyword);
            if (!$records) {
                return $this->sendError("Not found data by keyword $request->keyword!", 404);
            }
            foreach ($records as $key => $value) {
                $recordId = $value->id;
                $like = $this->favoriteRepository->findLiked($recordId);
                $value->favorite = !empty($like) ? true : false;
                $hospitalID = $value->hospital;
                if (isset($hospitalID)) {
                    $value->hospital = ($this->hospitalRepository->findById($hospitalID)) ? $this->hospitalRepository->findById($hospitalID)->name : '';
                }
                $peopleID = $value->people;
                if (isset($peopleID)) {
                    $value->people = ($this->doctorRepository->findById($peopleID)) ? $this->doctorRepository->findById($peopleID)->name : '';
                }
                $folderID = $value->folder;
                if (isset($folderID)) {
                    $value->folder = ($this->folderRepository->findById($folderID)) ? $this->folderRepository->findById($folderID)->name : '';
                }
                $mediaId = $value->media;
                if (isset($mediaId)) {
                    $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                }

                $recordKeywords = $this->recordKeywordRepository->getRecordKeyWordsNotMedicine($recordId);
                $medicineNames = [];
                if (isset($recordKeywords)) {
                    foreach ($recordKeywords as $key => $keyword) {
                        $keyword->makeHidden(['fdisk', 'fext', 'fname', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        $keywordId = $keyword['keyword'];
                        $tags = $this->keywordRepository->getKeywords($keywordId);
                        if (isset($tags)) {
                            foreach ($tags as $key => $tag) {
                                $tag->makeHidden(['fdisk', 'fext', 'fname', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                                $medicineNames[] = $tag;
                            }
                            $value->keyword = $medicineNames;
                        }
                    }
                }
            }

            return $this->sendResponse($records, 'Get record list successfully.');
        } catch (\Exception$e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function controlRecordKeywordUpdate(RecordKeywordUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $record = $this->recordRepository->findBy(['id' =>$request->recordId, 'new_by' => Auth::id(), 'chg' => CHG_VALID_VALUE]);
            if (!$record) {
                return $this->sendError("Record ID ".$request->recordId." does not exist!", 500);
            }

            $recordKeywords = $this->recordKeywordRepository->getRecordKeyWordsNotMedicine($request->recordId);
            foreach ($recordKeywords as $recordKeyword) {
                $this->recordKeywordRepository->deleteById($recordKeyword->id);
            }

            foreach ($request->keywordId as $keywordId) {
                $keywordExists = $this->keywordRepository->findById($keywordId);
                if (!$keywordExists) {
                    return $this->sendError("Keyword ID ".$keywordId." does not exist!", 500);
                }

                $input = [
                    'type' => RECORDTYPE_RECORD_KEYWORD_VALUE,
                    'record' => $request->recordId,
                    'keyword' => $keywordId,
                    'new_by' => Auth::user()->id,
                    'new_ts' => Carbon::now(),
                    'upd_by' => Auth::user()->id,
                    'upd_ts' => Carbon::now(),
                ];
                $this->recordKeywordRepository->create($input);
            }

            $recordKeywordUpdates = $this->recordKeywordRepository->getRecordKeyWordsNotMedicine($request->recordId);

            if ($recordKeywordUpdates) {
                DB::commit();
                return $this->sendResponse($recordKeywordUpdates, 'Create recordKeyword successfully.');
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
