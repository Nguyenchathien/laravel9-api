<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\KeywordRequest;
use App\Http\Requests\V1\KeywordsRequest;
use App\Http\Requests\V1\KeywordUpdateRequest;
use App\Http\Requests\V1\MutipleRequest;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Interfaces\MediaKeywordRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;
use Validator;
use URL;

class KeywordController extends BaseController
{
    /**
     * @var KeywordRepositoryInterface
     */
    private $keywordRepository;

    /**
     * @var MediaKeywordRepositoryInterface
     */
    private $mediaKeywordRepository;

    /**
     * KeywordController constructor.
     * @param KeywordRepositoryInterface $keywordRepository
     * @param MediaKeywordRepositoryInterface $mediaKeywordRepository
     */
    public function __construct(
        KeywordRepositoryInterface $keywordRepository,
        MediaKeywordRepositoryInterface $mediaKeywordRepository
    ) {
        $this->keywordRepository = $keywordRepository;
        $this->mediaKeywordRepository = $mediaKeywordRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $keywords = $this->keywordRepository->allBy([
                'type' => MEDICINE_KEY_VALUE,
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ], ['*'], ['mediaKeyword']);

            return $this->sendResponse($keywords, 'Get data list successfully.');
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
            $keyword = $this->keywordRepository->findById($id);
            $keyword->makeHidden(['type', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
            $media = $this->mediaKeywordRepository->findBy(['keyword' => $id])->fpath;
            $data = ['keyword' => $keyword, 'media' => $media];
            if ($keyword) {
                return $this->sendResponse($data, 'Get medicine detail successfully.');
            }

            return $this->sendError("Not found!", 404);
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param KeywordRequest $request
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'file' => 'image:jpeg,png,jpg,gif,svg|max:10000',
                'name' => 'required|max:128',
                'vx01' => 'max:128',
                'vx02' => 'max:128',
                'remark' => 'max:1024',
            ]);

            if ($validator->fails()) {
                return $this->sendError(['error' => $validator->errors()], 401);
            }

            $input = $request->all();
            $input['type'] = MEDICINE_KEY_VALUE;
            $input['color'] = $request->input('color') ? $request->input('color') : COLOR_DEFAULT_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $keyword = $this->keywordRepository->create($input);

            if ($file = $request->file('file') && $keyword) {
                $path = Storage::disk('public')->putFile('medicines', $request->file('file'));
                $name = $request->file->getClientOriginalName();
                $mine = $request->file->getClientmimeType();
                $ext = $request->file->getExtension();

                //store your file into directory and db
                $input_media['keyword'] = $keyword->id;
                $input_media['fpath'] = URL::to('/') . '/storage/' . $path;
                $input_media['fname'] = $name;
                $input_media['fdisk'] = $path;
                $input_media['name'] = $name;
                $input_media['mime'] = $mine;
                $input_media['fext'] = $ext;
                $input_media['new_by'] = Auth::user()->id;
                $input_media['upd_by'] = Auth::user()->id;
                $input_media['upd_ts'] = Carbon::now();
                $mediaKeyword = $this->mediaKeywordRepository->create($input_media);
            }

            DB::commit();
            return $this->sendResponse($keyword, 'Create medicine successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param KeywordRequest $request
     */
    public function update(KeywordRequest $request)
    {
        try {
            $keyword = $this->keywordRepository->findById($request->id);
            if (!$keyword) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $keyword = $this->keywordRepository->update($request->id, $input);
            if ($keyword) {
                return $this->sendResponse($keyword, 'Update keyword successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

        /**
     *  @param KeywordRequest $request
     */
    public function updateMedicine(KeywordUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $keyword = $this->keywordRepository->findBy(['id' => $request->id, 'type' => MEDICINE_KEY_VALUE]);
            if (!$keyword) {
                return $this->sendError("Medicine not found with ID : $request->id!", 404);
            }
            $request->validated();
            $input = $request->all();
            $input['name'] = (isset($input['name'])) ? $request->name : $keyword->name;
            $input['vx01'] = (isset($input['vx01'])) ? $request->vx01 : $keyword->vx01;
            $input['vx02'] = (isset($input['vx02'])) ? $request->vx02 : $keyword->vx02;
            $input['remark'] = (isset($input['remark'])) ? $request->remark : $keyword->remark;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $keyword = $this->keywordRepository->update($request->id, $input);
            if (isset($input['file'])) {
                $file = $request->file('file');
                $fileMime = $file->extension();
                $fileName = time() . '.' . $fileMime;
                $input['mime'] = MIME_IMAGE_VALUE;
                $input['record'] = $request->id;
                $filePath = $file->storeAs('keywords', $fileName, 'public');
                $input['fdisk'] = URL::to('/') . '/storage/' . '/' . 'keywords/';
                $input['fname'] = $fileName;
                $input['fext'] = $fileMime;
                $input['vname'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $input['fpath'] = URL::to('/') . '/storage/' . '/' . $filePath;
                $keywordMedia = $this->mediaKeywordRepository->findBy(['keyword' => $request->id, 'chg' => CHG_VALID_VALUE,]);
                if ($keywordMedia) {
                    $keywordMediaID = $keywordMedia->id;
                    $keywordMedia = $this->mediaKeywordRepository->update($keywordMediaID, $input);
                } else {
                    $input_media = [
                        'keyword' => $request->id,
                        'fpath' => $input['fpath'],
                        'fname' => $fileName,
                        'fdisk' => $input['fdisk'],
                        'vname' => $input['vname'],
                        'mime' => $input['mime'],
                        'fext' => $fileMime,
                        'new_by' => Auth::user()->id,
                        'upd_by' => Auth::user()->id,
                        'upd_ts' => Carbon::now(),
                    ];
                    $this->mediaKeywordRepository->create($input_media);
                }

            }
            DB::commit();
            return $this->sendResponse($keyword, 'Update keyword successfully.');
        } catch (\Exception $e) {
            DB::rollback();
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
            $medicine = $this->keywordRepository->findById($request->id);
            if (!$medicine) {
                return $this->sendError("Medicine not found with ID : $request->id!", 404);
            }
            $this->keywordRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete medicine successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function deleteKeyword($idKeyword)
    {
        try {
            $medicine = $this->keywordRepository->findById($idKeyword);
            if (!$medicine) {
                return $this->sendError("Medicine not found with ID : $idKeyword!", 404);
            }
            $this->keywordRepository->deleteById($idKeyword);
            return $this->sendResponse([], 'Delete medicine successfully.');
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
            $arrayKeywords = $request->array;
            $keywords = str_replace("\n", "", $arrayKeywords);
            $result = json_decode($keywords, true);
            foreach ($result as $key => $val) {
                $keywordId = $val['id'];
                if (!$keywordId) {
                    return $this->sendError("Folder not found with ID : $keywordId!", 404);
                }
                $deleteFolder = app('App\Http\Controllers\V1\KeywordController')->deleteKeyword($keywordId);
            }
            return $this->sendResponse([], 'Delete folder successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

       /**
     *  @param KeywordsRequest $request
     */
    public function createKey(KeywordsRequest $request)
    {
        try {
            $request->validated();
            $input = $request->all();
            $input['type'] = KEY_WORD_KEY_VALUE;
            $input['user'] = Auth::user()->id;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $keyword = $this->keywordRepository->create($input);
            if($keyword) {
                return $this->sendResponse($keyword, 'Create keyword successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function deleteKey(Request $request)
    {
        try {
            $medicine = $this->keywordRepository->findById($request->id);
            if (!$medicine) {
                return $this->sendError("Medicine not found with ID : $request->id!", 404);
            }
            $this->keywordRepository->deleteById($request->id);
            return $this->sendResponse([], 'Delete medicine successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $requestt
     */
    public function updateKey(Request $request)
    {
        try {
            $keyword = $this->keywordRepository->findById($request->id);
            if (!$keyword) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }
            // dd($request->name);
            // $request->validated();
            $input = $request->all();
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $keyword = $this->keywordRepository->update($request->id, $input);
            if ($keyword) {
                return $this->sendResponse($keyword, 'Update keyword successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

     /**
     * @param null
     */
    public function listKey()
    {
        try {
            $keywords = $this->keywordRepository->allBy([
                'type' => KEY_WORD_KEY_VALUE,
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ]);
            //  dd($keywords);
            if ($keywords != null) {
                foreach ($keywords as $key => $value) {
                    $id = $value->id;
                    $media = $this->mediaKeywordRepository->findBy(['keyword' => $id]);
                    $value->media = $media;
                }
            }
            $keywords->makeHidden(['chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
            return $this->sendResponse($keywords, 'Get data list successfully.');
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
