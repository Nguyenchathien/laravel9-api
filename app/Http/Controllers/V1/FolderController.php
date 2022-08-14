<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FolderRequest;
use App\Http\Requests\V1\MutipleRequest;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class FolderController extends BaseController
{
    /**
     * @var FolderRepositoryInterface
     * @var RecordRepositoryInterface
     * @var RecordItemRepositoryInterface
     * @var MediaRepositoryInterface
     * @var HospitalRepositoryInterface
     * @var DoctorRepositoryInterface
     * @var RecordKeywordRepositoryInterface
     * @var KeywordRepositoryInterface
     * @var FavoriteRepositoryInterface
     */
    protected $folderRepository;
    protected $recordRepository;
    protected $doctorRepository;
    protected $recordItemRepository;
    protected $hospitalRepository;
    protected $mediaRepository;
    protected $recordKeywordRepository;
    protected $keywordRepository;
    protected $favoriteRepository;

    /**
     * FolderController constructor.
     * @param FolderRepositoryInterface $folderRepository
     */
    public function __construct(
        FolderRepositoryInterface $folderRepository,
        RecordRepositoryInterface $recordRepository,
        RecordItemRepositoryInterface $recordItemRepository,
        MediaRepositoryInterface $mediaRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        RecordKeywordRepositoryInterface $recordKeywordRepository,
        KeywordRepositoryInterface $keywordRepository,
        FavoriteRepositoryInterface $favoriteRepository
    ) {
        $this->folderRepository = $folderRepository;
        $this->recordRepository = $recordRepository;
        $this->recordItemRepository = $recordItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->doctorRepository = $doctorRepository;
        $this->recordKeywordRepository = $recordKeywordRepository;
        $this->keywordRepository = $keywordRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $folders = $this->folderRepository->allBy([
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ], ['*'], ['records']);

            return $this->sendResponse($folders, 'Get folder list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function getFolderDetail($id)
    {
        try {
            $folder = $this->folderRepository->findById($id);
            if($folder) {
                return $this->sendResponse($folder, 'Get folder detail successfully.');
            }
            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function getListRecordsInFolder($id)
    {
        try {
            $folder = $this->folderRepository->findById($id);
            if (empty($folder)) {
                return $this->sendError("Folder $id does not exsit.");
            }
            $folder->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            $records = $this->recordRepository->listRecordsByFolder($id);
            if (isset($records)) {
                foreach ($records as $key => $value) {
                    $recordId = $value->id;
                    $like = $this->favoriteRepository->findBy(['record' => $recordId, 'user' => Auth::id()]);
                    $value->favorite = !empty($like) ? true : false;
                    $hospitalID = $value->hospital;
                    if (isset($hospitalID)) {
                        $value->hospital = $this->hospitalRepository->findById($hospitalID);
                        $value->hospital->makeHidden(['type', 'post', 'pref','pref_code','address','xaddress','remark','phone','mail', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    }
                    $peopleID = $value->people;
                    if (isset($peopleID)) {
                        $value->people = $this->doctorRepository->findById($peopleID);
                        $value->people->makeHidden(['type', 'email', 'org','dept','post', 'pref','pref_code','address','xaddress','remark','phone','mail', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    }
                    // $folderID = $value->folder;
                    // if (isset($folderID)) {
                    //     $value->folder = $this->folderRepository->findById($folderID);
                    // }
                    $mediaId = $value->media;
                    if (isset($mediaId)) {
                        $value->media = $this->mediaRepository->findById($mediaId)->fpath;
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
            }
            $data = ['folder' => $folder, 'records' => $records];
            if ($folder) {
                return $this->sendResponse($data, 'Get folder detail successfully.');
            }
            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param Request $request
     */
    public function searchRecordsInFolder(Request $request)
    {
        try {
            if (isset($request->keyword)) {
                $recordSearch = $this->recordRepository->searchRecordsByFolder($request);
                foreach ($recordSearch as $key => $value) {
                    $recordId = $value->id;
                    $like = $this->favoriteRepository->findLiked($recordId);
                    $value->favorite = !empty($like) ? true : false;
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
            } else {
                return $this->sendError('Please enter keyword.');
            }
            if ($recordSearch) {
                return $this->sendResponse($recordSearch, 'Search record successfully.');
            } else {
                return $this->sendResponse($recordSearch, 'No results.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param FolderRequest $request
     */
    public function store(FolderRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['type'] = FOLDER_TYPE_KEY_VALUE;
            $input['color'] = $request->input('color') ? $request->input('color') : COLOR_DEFAULT_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $folder = $this->folderRepository->create($input);
            if ($folder) {
                return $this->sendResponse($folder, 'Create folder successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param FolderRequest $request
     */
    public function update(FolderRequest $request)
    {
        try {
            $folder = $this->folderRepository->findById($request->id);
            if (!$folder) {
                return $this->sendError("Folder not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $folder = $this->folderRepository->update($request->id, $input);
            if ($folder) {
                return $this->sendResponse($folder, 'Update folder successfully.');
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
            $folder = $this->folderRepository->findById($request->id);
            $records = $this->recordRepository->listRecordsByFolder($request->id);
            if (isset($records)) {
                foreach ($records as $key => $value) {
                    $recordId = $value->id;
                    $this->recordRepository->deleteById($recordId);
                }
            }
            if (!$folder) {
                return $this->sendError("Folder not found with ID : $request->id!", 404);
            }
            $this->folderRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete folder successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function deleteFolder($idFolder)
    {
        try {
            $folder = $this->folderRepository->findById($idFolder);
            $records = $this->recordRepository->listRecordsByFolder($idFolder);
            if (isset($records)) {
                foreach ($records as $key => $value) {
                    $recordId = $value->id;
                    $this->recordRepository->deleteById($recordId);
                }
            }
            if (!$folder) {
                return $this->sendError("Folder not found with ID : $idFolder!", 404);
            }
            $this->folderRepository->deleteById($idFolder);
            return $this->sendResponse([], 'Delete folder successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    /**
     *  @param MutipleRequest $request
     */
    public function deleteMultiple(MutipleRequest $request)
    {
        try {
            $arrayFolders = $request->array;
            $folders = str_replace("\n", "", $arrayFolders);
            $result = json_decode($folders, true);
            foreach ($result as $key => $val) {
                $folderId = $val['id'];
                if (!$folderId) {
                    return $this->sendError("Folder not found with ID : $folderId!", 404);
                }
                $deleteFolder = app('App\Http\Controllers\V1\FolderController')->deleteFolder($folderId);
            }
            return $this->sendResponse([], 'Delete folder successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function uploadPdf(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                    'file' => 'required|mimes:pdf|max:2048',
            ]);

            if($validator->fails()) {

                return response()->json(['error'=>$validator->errors()], 401);
            }

            if ($file = $request->file('file')) {
                $path = $file->store('public/files/'.date('YmdHis'));
                $name = $file->getClientOriginalName();

                return response()->json([
                    "success" => true,
                    "path" => "File successfully uploaded",
                    "file" =>  str_replace('public', 'storage', $path)
                ]);

            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function concat(Request $request)
    {

        $audios = str_replace("\r\n", "", $request->audios);
        $audios = str_replace("[", "", $audios);
        $audios = str_replace("]", "", $audios);
        $audios = str_replace(" ", "", $audios);
        $audios = str_replace("'", "", $audios);
        $s = explode(",",$audios);
        $out = [];
        foreach($s as $n) {
            array_push($out, $n);
        }
        $public_path = concat_audio($out);
        dd($public_path);
    }
}
