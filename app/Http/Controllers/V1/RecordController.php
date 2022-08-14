<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\V1\MediaRequest;
use App\Http\Requests\V1\RecordRequest;
use App\Http\Requests\V1\RecordUpdateRequest;
use App\Http\Requests\V1\UploadSpeechRequest;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use wapmorgan\MediaFile\MediaFile;
use Illuminate\Support\Facades\Http;

class RecordController extends BaseController
{

    /**
     * @var RecordRepositoryInterface
     */
    protected $recordRepository;
    protected $recordItemRepository;
    protected $mediaRepository;
    protected $hospitalRepository;
    protected $doctorRepository;
    protected $recordKeywordRepository;
    protected $folderRepository;
    protected $keywordRepository;
    protected $favoriteRepository;
    protected $userRepository;

    /**
     * RecordController constructor.
     * @param RecordRepository $recordRepository
     */
    public function __construct(
        RecordRepositoryInterface $recordRepository,
        RecordItemRepositoryInterface $recordItemRepository,
        MediaRepositoryInterface $mediaRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        RecordKeywordRepositoryInterface $recordKeywordRepository,
        FolderRepositoryInterface $folderRepository,
        KeywordRepositoryInterface $keywordRepository,
        FavoriteRepositoryInterface $favoriteRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->recordRepository = $recordRepository;
        $this->recordItemRepository = $recordItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->doctorRepository = $doctorRepository;
        $this->recordKeywordRepository = $recordKeywordRepository;
        $this->folderRepository = $folderRepository;
        $this->keywordRepository = $keywordRepository;
        $this->favoriteRepository = $favoriteRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $records = $this->recordRepository->allBy([
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ]);
            foreach ($records as $key => $value) {
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
                    $value->folder = $this->folderRepository->findById($folderID);
                }
                $mediaId = $value->media;
                if (isset($mediaId)) {
                    $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                }
                $value->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            }
            return $this->sendResponse($records, 'Get record list successfully.');
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param null
     */
    function getListRecordByFolder($folder)
    {
        try {
            $records = $this->recordRepository->allBy([
                'folder' => $folder,
                'chg' => CHG_VALID_VALUE
            ], ['*'], ['folder', 'media']);

            return $this->sendResponse($records, 'Get record list successfully.');
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param null
     */
    function list()
    {
        try {
            $records = $this->recordRepository->listRecords();
            foreach ($records as $key => $value) {
                $recordId = $value->id;
                $like = $this->favoriteRepository->findLiked($recordId);
                $value->favorite = !empty($like) ? true : false;
                $hospitalID = $value->hospital;
                if (isset($hospitalID)) {
                    $value->hospital = ($this->hospitalRepository->findByIdHospital($hospitalID)) ? $this->hospitalRepository->findByIdHospital($hospitalID)->name : '';
                }
                $peopleID = $value->people;
                if (isset($peopleID)) {
                    $value->people = ($this->doctorRepository->findByIdDoctor($peopleID)) ? $this->doctorRepository->findByIdDoctor($peopleID)->name : '';
                }
                $folderID = $value->folder;
                if (isset($folderID)) {
                    $value->folder = $this->folderRepository->findById($folderID);
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
                $value->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            }
            return $this->sendResponse($records, 'Get record list successfully.');
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    function listNoPagination()
    {
        try {
            $records = $this->recordRepository->listRecordsWithNoPaginate();
            foreach ($records as $key => $value) {
                $recordId = $value->id;
                $like = $this->favoriteRepository->findLiked($recordId);
                $value->favorite = !empty($like) ? true : false;
                $hospitalID = $value->hospital;
                if (isset($hospitalID)) {
                    $value->hospital = ($this->hospitalRepository->findByIdHospital($hospitalID)) ? $this->hospitalRepository->findByIdHospital($hospitalID)->name : '';
                }
                $peopleID = $value->people;
                if (isset($peopleID)) {
                    $value->people = ($this->doctorRepository->findByIdDoctor($peopleID)) ? $this->doctorRepository->findByIdDoctor($peopleID)->name : '';
                }
                $folderID = $value->folder;
                if (isset($folderID)) {
                    $value->folder = $this->folderRepository->findById($folderID);
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
                $value->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            }
            return $this->sendResponse($records, 'Get record list successfully.');
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function searchRecord(Request $request)
    {
        try {
            if (isset($request->q)) {
                $recordSearch = $this->recordRepository->Search($request);
                if ($recordSearch) {
                    return $this->sendResponse($recordSearch, 'Search record successfully.');
                } else {
                    return $this->sendResponse($recordSearch, 'No results.');
                }
            } else {
                return $this->sendError('Please enter keyword.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function searchRecords(Request $request)
    {
        try {
            if (isset($request->q)) {
                $recordSearch = $this->recordRepository->searchRecords($request);
                // dd($recordSearch);
                foreach ($recordSearch as $key => $value) {
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
                        $value->folder = $this->folderRepository->findById($folderID) ? $this->folderRepository->findById($folderID)->name : '';
                    }
                    $mediaId = $value->media;
                    if (isset($mediaId)) {
                        $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                    }
                    $medicineId = $value->medicine;
                    if (isset($medicineId)) {
                        $value->medicine = ($this->keywordRepository->findById($medicineId)) ? $this->keywordRepository->findById($medicineId)->name : '';
                    }
                    $recordKeywords = $this->recordKeywordRepository->getRecordKeyWords($recordId);
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
                if ($recordSearch) {
                    return $this->sendResponse($recordSearch, 'Search record successfully.');
                } else {
                    return $this->sendError('No results.', 404);
                }
            } else {
                return $this->sendError('Please enter keyword.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordRequest $request
     */
    public function store(RecordRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['type'] = RECORD_DEFAULT_VALUE;
            $input['begin'] = Carbon::now();
            $input['end'] = Carbon::now();
            $input['user'] = Auth::user()->id;
            $input['visible'] = VISIBLE_INVALID_VALUE;
            $input['chg'] = CHG_INVALID_VALUE;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record = $this->recordRepository->create($input);
            if ($record) {
                return $this->sendResponse($record, 'Create record successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordRequest $request
     */
    public function createRecord(RecordRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();

            $user = $this->userRepository->findById(Auth::id());
            if ($user->plan == FREE_PLAN_VALUE && $request->total_time > TOTAL_TIME_RECORD_FREE_PLAN)
            {
                return $this->sendError("Your recording time has exceeded the limit");
            }
            if ($user->time_record > 0 && $request->total_time <= $user->time_record) {
                $input['title'] = $request->title;
                $input['type'] = RECORD_DEFAULT_VALUE;
                $input['folder'] = ($request->folder) ? $request->folder : NULL;
                $input['begin'] = $request->begin;
                $input['end'] = $request->end;
                $input['user'] = Auth::user()->id;
                $input['visible'] = VISIBLE_VALID_VALUE;
                $input['chg'] = CHG_VALID_VALUE;
                $input['new_by'] = Auth::user()->id;
                $input['new_ts'] = Carbon::now();
                $input['upd_by'] = Auth::user()->id;
                $input['upd_ts'] = Carbon::now();
                $record = $this->recordRepository->create($input);
                $record->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                $recordId = $record->id;
                $recordItems = $request->recordItems;
                $items = str_replace("\n", "", $recordItems);
                $result = json_decode($items, true);

                foreach ($result as $key => $value) {
                    $begin = $value['begin'];
                    $end = $value['end'];
                    $content = $value['content'] ?? "";
                    $timestamp = $value['timestamp'] ?? $value['end'];
                    $recordItem = app('App\Http\Controllers\V1\RecordItemController')->createItem($begin, $end, $content, $recordId, $timestamp);
                }

                $public_path = $this->convertArray($request->audios);
                $media = app('App\Http\Controllers\V1\MediaController')->storeAudio($public_path, $recordId);

                $keywords = $request->keywords;
                $itemKeywords = str_replace("\n", "", $keywords);
                $resultKeywords = json_decode($itemKeywords, true);
                foreach ($resultKeywords as $key => $val) {
                    $keywordId = $val['id'];
                    $existed = $this->recordKeywordRepository->findBy(['record' => $recordId, 'keyword' => $keywordId]);
                    if (isset($existed)) {
                        return $this->sendError("Record $recordId has this keyword $keywordId!");
                    }
                    $recordKeyword = app('App\Http\Controllers\V1\RecordKeywordController')->createRecordKeyword($recordId, $keywordId);
                }
                $mediaData = $media->getData();
                if (isset($mediaData)) {
                    $mediaId = $mediaData->data->id;
                    $lastRecord = $this->recordRepository->updateMedia($recordId, $mediaId);
                }
                $recordDetail = $this->recordRepository->getDetail($recordId);
                $data = ['record' => $recordDetail, 'media' => $media->getData()->data->fpath, 'recordItem' => $result, 'keywords' => $resultKeywords];

                if ($record) {
                    $this->userRepository->update(Auth::id(), ['time_record' => $user->time_record - $request->total_time]);
                    DB::commit();
                    return $this->sendResponse($data, 'Create record successfully.');
                }
            }
            return $this->sendError("Your Account not enough time to record");

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function convertArray($au)
    {
        $audios = str_replace("\r\n", "", $au);
        $audios = str_replace("[", "", $audios);
        $audios = str_replace("]", "", $audios);
        $audios = str_replace(" ", "", $audios);
        $audios = str_replace("'", "", $audios);
        $s = explode(",", $audios);
        $out = [];
        foreach ($s as $n) {
            array_push($out, $n);
        }
        $public_path = concat_audio($out);
        return $public_path;
    }


    /**
     * @param MediaRequest $request
     */
    public function import(MediaRequest $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $request->validated();
            $input = $request->all();
            $input['title'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $input['type'] = RECORD_DEFAULT_VALUE;
            $input['begin'] = Carbon::now();
            $input['end'] = Carbon::now();
            $input['user'] = Auth::user()->id;
            $input['visible'] = VISIBLE_VALID_VALUE;
            $input['chg'] = CHG_VALID_VALUE;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record = $this->recordRepository->create($input);
            $recordId = $record->id;
            $recordItem = app('App\Http\Controllers\V1\RecordItemController')->storeItem($recordId);
            $recordItemId = $recordItem->getData()->data->id;
            $media = app('App\Http\Controllers\V1\MediaController')->storeMedia($file, $recordItemId);
            $mediaId = $media->getData()->data->id;
            $lastRecord = $this->recordRepository->updateMedia($recordId, $mediaId);
            $recordDetail = $this->recordRepository->getDetail($recordId);
            $data = ['record' => $recordDetail, 'recordItem' => $recordItem->getData()->data->content, 'media' => $media->getData()->data->fpath];
            if ($record) {
                DB::commit();
                return $this->sendResponse($data, 'Import record successfully.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param MediaRequest $request
     */
    public function importAudio(MediaRequest $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $request->validated();
            $input = $request->all();
            $input['title'] = 'tests';
            $input['type'] = RECORD_DEFAULT_VALUE;
            $input['begin'] = Carbon::now();
            $input['end'] = Carbon::now();
            $input['user'] = Auth::user()->id;
            $input['visible'] = VISIBLE_VALID_VALUE;
            $input['chg'] = CHG_VALID_VALUE;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record = $this->recordRepository->create($input);
            $recordId = $record->id;
            $recordItem = app('App\Http\Controllers\V1\RecordItemController')->storeItem($recordId);
            $recordItemData = $recordItem->getData();
            if (isset($recordItemData)) {
                $recordItemId = $recordItemData->data->id;
                $lastRecord = $this->recordRepository->updateMedia($recordId, $recordItemId);
            }
            $media = app('App\Http\Controllers\V1\MediaController')->storeMedia($file, $recordItemId);
            $mediaData = $media->getData();
            if (isset($mediaData)) {
                $mediaId = $mediaData->data->id;
                $filePath = $mediaData->data->fname;
            }
            $url = base_path() . '/storage/app/public/audios/' . $filePath;
            $fileImport = MediaFile::open($url);
            if ($fileImport->isAudio()) {
                $audio = $fileImport->getAudio();
                $duration = $audio->getLength() . PHP_EOL;
                $result = str_replace("\r\n", "", $duration);
                $result = (int)$result;
                $endTime = Carbon::now()->addSeconds($result);
            }
            $lastRecord = $this->recordRepository->updateRecord($recordId, $mediaId, $endTime);
            $recordDetail = $this->recordRepository->getDetail($recordId);
            $data = ['record' => $recordDetail, 'recordItem' => $recordItem->getData()->data->content, 'media' => $media->getData()->data->fpath];
            if ($record) {
                DB::commit();
                return $this->sendResponse($data, 'Import record successfully.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordRequest $request
     */
    public function save(RecordRequest $request)
    {
        try {
            $record = $this->recordRepository->findBy(['id' => $request->id]);
            if (!$record) {
                return $this->sendError("Record not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input['end'] = Carbon::now();
            $input['visible'] = VISIBLE_VALID_VALUE;
            $input['chg'] = CHG_VALID_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record->update($input);
            if ($record) {
                return $this->sendResponse($record, 'Update record successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param RecordUpdateRequest $request
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $record = $this->recordRepository->findById($request->id);
            if (!$record) {
                return $this->sendError("Record not found with ID : $request->id!", 404);
            }
            $recordKeyword = $this->recordKeywordRepository->getRecordKeyWords($record->id);
            $input = $request->all();
            if (isset($request->title)) {
                $input['title'] = $request->title;
            }
            if (isset($request->people)) {
                $input['people'] = $request->people;
                $hospitalId = $this->doctorRepository->findById($request->people)->org ?? null;
                $input['hospital'] = $hospitalId;
            }
            if (isset($request->medicine)) {
                $input['medicine'] = json_encode($request->medicine);
                $recordMedicine = [
                    'type' => MEDICINE_KEY_VALUE,
                    'record' => $request->id,
                    'chg' => $record->chg,
                    'new_by' => $record->new_by,
                    'new_ts' => $record->new_ts,
                    'upd_by' => $record->upd_by,
                    'upd_ts' => $record->upd_ts,
                ];
                $recordMedicines = $this->recordKeywordRepository->findRecordMedicine($request->id);
                if (!empty($recordMedicines)) {
                    foreach ($recordMedicines as $record) {
                        $this->recordKeywordRepository->deleteById($record->id);
                    }
                }

                foreach ($request->medicine as $medicine) {
                    $recordMedicine['keyword'] = $medicine;
                    $this->recordKeywordRepository->create($recordMedicine);
                }
            }
            if (isset($request->folder)) {
                $input['folder'] = $request->folder;
            }

            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record = $this->recordRepository->update($request->id, $input);
            DB::commit();
            if ($record) {
                return $this->sendResponse($record, 'Update record successfully.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function updateImage($recordId, $mediaId)
    {
        try {
            $record = $this->recordRepository->findById($recordId);
            if (!$record) {
                return $this->sendError("Record not found with ID : $recordId", 404);
            }
            $input['media'] = $mediaId;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $record = $this->recordRepository->update($recordId, $input);
            if ($record) {
                return $this->sendResponse($record, 'Update record successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function delete(Request $request)
    {
        try {
            $record = $this->recordRepository->findById($request->id);

            if (!$record) {
                return $this->sendError("Record not found with ID : $request->id!", 404);
            }
            $this->recordRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete record successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function hideAndShow($id)
    {
        try {
            $record = $this->recordRepository->findById($id);
            if (!$record) {
                return $this->sendError("Record not found with ID : $id!", 404);
            }
            $record->visible = $record->visible == VISIBLE_VALID_VALUE ? VISIBLE_INVALID_VALUE : VISIBLE_VALID_VALUE;
            $record->save();

            if ($record) {
                return $this->sendResponse($record, 'Get recordItem detail successfully.');
            }
            return $this->sendError("RecordItem not found with ID : $id!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function detail(Request $request)
    {
        try {
            $record = $this->recordRepository->findById($request->id);
            if ($record) {
                $recordItem = $this->recordItemRepository->getItem($request->id);
                $images = $this->mediaRepository->getImages($request->id);
                $recordKeywords = $this->recordKeywordRepository->getRecordKeyWordsNotMedicine($request->id);
                $like = $this->favoriteRepository->findBy(['record' => $request->id, 'user' => Auth::id(), 'chg' => CHG_VALID_VALUE]);
                $record->favorite = !empty($like) ? true : false;
                $audioId = (int)$record->media;
                if (isset($audioId)) {
                    $mediaRepo = $this->mediaRepository->findById($audioId);
                    if (isset($mediaRepo)) {
                        $record->media = $this->mediaRepository->findById($audioId)->fpath;
                    }
                }
                $medicinelId = $record->medicine;
                if (isset($medicinelId)) {
                    $medicineIds = json_decode($medicinelId);
                    $medicines = [];
                    foreach ($medicineIds as $medicineId) {
                        $medicine = $this->keywordRepository->findById($medicineId)->makeHidden(['color', 'user', 'vx01', 'vx02', 'remark', 'type', 'fdisk', 'fext', 'fname', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        array_push($medicines, $medicine);
                    }
                    $record->medicine = $medicines;
                }
                $peopleID = $record->people;
                if (isset($peopleID)) {
                    $record->people = $this->doctorRepository->findByIdDoctor($peopleID);
                    $record->people->makeHidden(['type', 'email', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'mail', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    $hospitalId = $record->people->org;
                    if (isset($hospitalId)) {
                        $record->people->orgName = $this->hospitalRepository->findByIdHospital($hospitalId)->name ?? null;
                    } else {
                        $record->people->orgName = null;
                    }
                }
                $folderID = $record->folder;
                if (isset($folderID)) {
                    $record->folder = $this->folderRepository->findById($folderID);
                    $record->folder->makeHidden(['type', 'pid', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                }
                $record->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                foreach ($recordItem as $key => $value) {
                    if ($record->visible == 'N') {
                        $value->makeHidden(['begin', 'end', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    } else {
                        $value->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    }
                }
                foreach ($images as $key => $img) {
                    $img->makeHidden(['fdisk', 'fext', 'fname', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                }
                $medicineNames = [];
                if (isset($recordKeywords)) {
                    foreach ($recordKeywords as $key => $keyword) {
                        $keywordId = $keyword['keyword'];
                        $tags = $this->keywordRepository->getKeywords($keywordId);
                        if (isset($tags)) {
                            foreach ($tags as $key => $tag) {
                                $tag->makeHidden(['type', 'fdisk', 'user', 'vx01', 'vx02', 'remark', 'fext', 'fname', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                                $medicineNames[] = $tag;
                            }
                        }
                    }
                }
                $record->keywords = $medicineNames;
                $data = ['record' => $record, 'recordItem' => $recordItem, 'medias' => $images];
                if ($record) {
                    return $this->sendResponse($data, 'Import record successfully.');
                }
            }
            return $this->sendError("Record not found with ID : $request->id!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param null
     */
    public function durationInAMonth() // 10 minutes

    {
        try {
            $records = $this->recordRepository->allBy([
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ]);
            $times = [];
            foreach ($records as $key => $value) {
                $start = strtotime($value->begin);
                $finish = strtotime($value->end);
                $duration = $finish - $start;
                $times[] = $duration;
            }
            $total = array_sum($times);
            $now = Carbon::now()->format('Y-m-d');
            $date = Carbon::now();
            $date->addMonth();
            $date->day = 0;
            $last = $date->toDateString();
            $visible = ($now <= $last) ? true : false;
            $data = ['total' => $total, 'visible' => $visible];
            if ($total >= 600 && $visible == true) {
                return $this->sendResponse($data, "You can not record in this Month");
            }
            if ($total <= 600) {
                return $this->sendResponse($data, "You can record in this Month");
            }
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    function listRecordsByHospital(Request $request)
    {
        try {
            $records = $this->recordRepository->listRecordsByHospital($request->hospital);
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
                    $value->folder = $this->folderRepository->findById($folderID);
                }
                $mediaId = $value->media;
                if (isset($mediaId)) {
                    $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                }
                $recordKeywords = $this->recordKeywordRepository->getRecordKeyWords($recordId);
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
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    function listRecordsByDoctor(Request $request)
    {
        // dd($request->doctor);
        try {
            $records = $this->recordRepository->listRecordsByDoctor($request->doctor);
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
                    $value->folder = $this->folderRepository->findById($folderID);
                }
                $mediaId = $value->media;
                if (isset($mediaId)) {
                    $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                }
                $recordKeywords = $this->recordKeywordRepository->getRecordKeyWords($recordId);
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
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    function listRecordsByMedicine(Request $request)
    {
        // dd($request->doctor);
        try {
            $recordByMedicines = $this->recordKeywordRepository->listRecordsByMedicine($request->medicine);
            $records = [];
            if (!empty($recordByMedicines)) {
                foreach ($recordByMedicines as $recordKeyword) {
                    $record = $this->recordRepository->findById($recordKeyword->record);
                    array_push($records, $record);
                }
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
                    $value->folder = $this->folderRepository->findById($folderID);
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
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }


    /**
     * @param Request $request
     */
    function listRecordsByFamily(Request $request)
    {
        // dd($request->doctor);
        try {
            $records = $this->recordRepository->listRecordsByFamily($request->family);
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
                    $value->folder = $this->folderRepository->findById($folderID);
                }
                $mediaId = $value->media;
                if (isset($mediaId)) {
                    $value->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
                }
                $recordKeywords = $this->recordKeywordRepository->getRecordKeyWords($recordId);
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
        } catch (\Exception $e) {
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function speechToText(Request $request)
    {
        $amiVoiceUrl = RECORD_AMI_VOICE_URL;
        $file = $request->file('file');
        $tmp = 'public/tmp';
        $publicPath = Storage::put($tmp, $file);
        $filePath = storage_path('app/' . $publicPath);
        $output = storage_path('app/' . $tmp . '/' . uniqid() . '.mp3');
        $ffmpeg_command = "ffmpeg -i " . $filePath . " -vn -ar 44100 -ac 2 -b:a 192k " . $output;
        shell_exec($ffmpeg_command);
        // shell_exec('rm ' . $filePath);
        $payload = fopen($output, 'r');
        $response = Http::attach(
            'a',
            $payload
        )->post($amiVoiceUrl);
        shell_exec('rm ' . $output);
        $response = $response->json();
        $response['file_url'] = $filePath;
        if(isset($request->begin)) $response['begin'] = $request->begin;
        return $response;
    }
}
