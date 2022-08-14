<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ShareRequest;
use App\Mail\NotificationInvite;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use App\Repositories\Interfaces\ShareRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;

class ShareController extends BaseController
{
    /**
     * @var ShareRepositoryInterface
     */
    private $shareRepository;
    private $userRepository;
    private $recordRepository;
    protected $recordItemRepository;
    protected $mediaRepository;
    protected $hospitalRepository;
    protected $doctorRepository;
    protected $recordKeywordRepository;
    protected $folderRepository;
    protected $keywordRepository;
    protected $favoriteRepository;

    /**
     * ShareController constructor.
     * @param ShareRepositoryInterface $shareRepository
     */
    public function __construct(
        ShareRepositoryInterface $shareRepository,
        UserRepositoryInterface $userRepository,
        RecordRepositoryInterface $recordRepository,
        RecordItemRepositoryInterface $recordItemRepository,
        MediaRepositoryInterface $mediaRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        RecordKeywordRepositoryInterface $recordKeywordRepository,
        FolderRepositoryInterface $folderRepository,
        KeywordRepositoryInterface $keywordRepository,
        FavoriteRepositoryInterface $favoriteRepository
    ) {
        $this->shareRepository = $shareRepository;
        $this->userRepository = $userRepository;
        $this->recordRepository = $recordRepository;
        $this->recordItemRepository = $recordItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->doctorRepository = $doctorRepository;
        $this->recordKeywordRepository = $recordKeywordRepository;
        $this->folderRepository = $folderRepository;
        $this->keywordRepository = $keywordRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * @param null
     */
    public function getListSharing($id)
    {
        try {
            $listSharing = $this->shareRepository->getListSharing($id);
            $recordList = [];
            if ($listSharing) {
                foreach ($listSharing as $key => $value) {
                    $recordId = $value->record;
                    $record = $this->recordRepository->findById($recordId);
                    if ($record) {
                        $like = $this->favoriteRepository->findLiked($recordId);
                        $record->favorite = !empty($like) ? true : false;
                        $hospitalID = $record->hospital;
                        if (isset($hospitalID)) {
                            $record->hospital = ($this->hospitalRepository->findById($hospitalID)) ? $this->hospitalRepository->findById($hospitalID)->name : '';
                        }
                        $peopleID = $record->people;
                        if (isset($peopleID)) {
                            $record->people = ($this->doctorRepository->findById($peopleID)) ? $this->doctorRepository->findById($peopleID)->name : '';
                        }
                        $folderID = $record->folder;
                        if (isset($folderID)) {
                            $record->folder = $this->folderRepository->findById($folderID);
                        }
                        $mediaId = $record->media;
                        if (isset($mediaId)) {
                            $record->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
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
                                    $record->keyword = $medicineNames;
                                }
                            }
                        }
                        $record->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                        $recordList[] = $record;
                    }
                }
                return $this->sendResponse($recordList, 'Get share list successfully.');
            } else {
                return $this->sendError("No data!", 404);
            }

        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function getListSharedRecords(Request $request)
    {
        try {
            $listSharing = $this->shareRepository->getListSharedRecords($request->from, $request->to);
            $recordList = [];
            if ($listSharing) {
                foreach ($listSharing as $key => $value) {
                    $recordId = $value->record;
                    $record = $this->recordRepository->findById($recordId);
                    if ($record) {
                        $like = $this->favoriteRepository->findLiked($recordId);
                        $record->favorite = !empty($like) ? true : false;
                        $hospitalID = $record->hospital;
                        if (isset($hospitalID)) {
                            $record->hospital = ($this->hospitalRepository->findById($hospitalID)) ? $this->hospitalRepository->findById($hospitalID)->name : '';
                        }
                        $peopleID = $record->people;
                        if (isset($peopleID)) {
                            $record->people = ($this->doctorRepository->findById($peopleID)) ? $this->doctorRepository->findById($peopleID)->name : '';
                        }
                        $folderID = $record->folder;
                        if (isset($folderID)) {
                            $record->folder = $this->folderRepository->findById($folderID);
                        }
                        $mediaId = $record->media;
                        if (isset($mediaId)) {
                            $record->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
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
                                    $record->keyword = $medicineNames;
                                }
                            }
                        }
                        $record->makeHidden(['visible', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                        $recordList[] = $record;
                    }
                }
                return $this->sendResponse($recordList, 'Get share list successfully.');
            } else {
                return $this->sendError("No data!", 404);
            }

        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param ShareRequest $request
     */
    public function sendShareRequest(ShareRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            $userTo = $this->userRepository->findById($request->to);
            if (!$userTo) {
                return $this->sendError("User not found by $request->to!", 404);
            }
            $record = $this->recordRepository->findById($request->record);
            if (!$record) {
                return $this->sendError("Record not found by $request->record!", 404);
            }
            $share = $this->shareRepository->findBy(['user' => Auth::id(), 'to' => $request->to, 'record' => $request->record]);
            if ($share) {
                if ($share->status == STATUS_REQUEST_VALUE || $share->status == STATUS_ACCEPT_VALUE) {
                    return $this->sendError("Duplicate!", 404);
                }
            }
            $input = $request->all();
            $input['user'] = Auth::user()->id;
            $input['to'] = $request->to;
            $input['record'] = $request->record;
            $input['new_by'] = Auth::user()->id;
            $input['new_ts'] = Carbon::now();
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $input['status'] = STATUS_REQUEST_VALUE;
            $share = $this->shareRepository->create($input);
            if ($share) {
                DB::commit();
                return $this->sendResponse($share, 'Send request share record with family successfully.');
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function receivedShareRequest()
    {
        try {
            $id = Auth::user()->id;
            $toEmail = Auth::user()->email;
            $toName = Auth::user()->name;
            $share = $this->shareRepository->findBy(['to' => Auth::user()->id, 'status' => STATUS_REQUEST_VALUE]);
            if (!$share) {
                return $this->sendResponse($share, "You have no request sharing record");
            }
            $userId = $share->user;
            $fromUser = $this->userRepository->findById($userId);
            if (!$fromUser) {
                return $this->sendError("User not found by ID: $userId!", 500);
            }
            $fromEmail = $fromUser->email;
            $fromName = $fromUser->name;
            $data = ['from' => $fromEmail, 'fromName' => $fromName, 'to' => $toEmail, 'toName' => $toName];

            if ($share) {
                return $this->sendResponse($data, "You have a request share record from $fromUser->email");
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

     public function receivedSharedRecord()
     {
         try {
             $toEmail = Auth::user()->email;
             $toName = Auth::user()->name;
             $shares = $this->shareRepository->allBy([
                'status' => STATUS_REQUEST_VALUE,
                'to' => Auth::id(),
                'chg' => CHG_VALID_VALUE
            ], ['id', 'record', 'user', 'to'], ['records', 'users']);
             if ($shares->isEmpty()) {
                 return $this->sendResponse([], "Nothing to share!");
             }

             $result = [];
             foreach ($shares as $share) {
                 $userId = $share->user;
                 $fromUser = $this->userRepository->findById($userId);
                 $fromEmail = $fromUser->email;
                 $fromName = $fromUser->name;
                 $recordId = $share->record;
                 $record = $this->recordRepository->findById($recordId);
                 if ($record) {
                     $like = $this->favoriteRepository->findLikeRecordShared($recordId, $userId);
                     $record->favorite = !empty($like);
                     $hospitalID = $record->hospital;
                     if (isset($hospitalID)) {
                         $record->hospital = ($this->hospitalRepository->findById($hospitalID)) ? $this->hospitalRepository->findById($hospitalID)->name : '';
                     }
                     $peopleID = $record->people;
                     if (isset($peopleID)) {
                         $record->people = ($this->doctorRepository->findById($peopleID)) ? $this->doctorRepository->findById($peopleID)->name : '';
                     }
                     $folderID = $record->folder;
                     if (isset($folderID)) {
                         $record->folder = $this->folderRepository->findById($folderID);
                     }
                     $mediaId = $record->media;
                     if (isset($mediaId)) {
                         $record->media = ($this->mediaRepository->findById($mediaId)) ? $this->mediaRepository->findById($mediaId)->fpath : '';
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
                                 $record->keyword = $medicineNames;
                             }
                         }
                     }
                     $record->makeHidden(['visible', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                 }
                 $data = ['from' => $fromEmail, 'fromName' => $fromName, 'to' => $toEmail, 'toName' => $toName, 'record' => $record];
                 array_push($result, $data);
             }

             return $this->sendResponse($result, "You have request share records");
         } catch (\Exception$e) {
             throw $e;
             return $this->sendError("Something when wrong!", 500);
         }
     }

//    public function receivedSharedRecord()
//    {
//        try {
//            $shares = $this->shareRepository->allBy([
//                'status' => STATUS_REQUEST_VALUE,
//                'to' => Auth::id(),
//                'chg' => CHG_VALID_VALUE
//            ], ['id', 'record', 'user', 'to'], ['records', 'users']);
//            if (!$shares) {
//                return $this->sendResponse([], "Nothing to share!");
//            }
//            return $this->sendResponse($shares, "You have a request share record from ".Auth::user()->email);
//        } catch (\Exception$e) {
//            throw $e;
//            return $this->sendError("Something when wrong!", 500);
//        }
//    }

    public function acceptedShareRequest(Request $request)
    {
        try {
            $id = Auth::user()->id;
            $share = $this->shareRepository->findBy(['to' => $id, 'status' => STATUS_REQUEST_VALUE]);
            if (!$share) {
                return $this->sendError("Sharing not found with id : $id!", 404);
            }
            $recordId = $share->record;
            $record = $this->recordRepository->findBy(['id' => $recordId]);
            if (!$record) {
                return $this->sendError("Record not found!", 404);
            }
            $shareId = $share->id;
            if ($record->chg == CHG_DELETE_VALUE || $record->chg == CHG_INVALID_VALUE) {
                $input['chg'] = CHG_DELETE_VALUE;
                $share = $this->shareRepository->accepted($shareId, $input);
                return $this->sendResponse($share, 'Invalid');
            }
            $input['status'] = STATUS_ACCEPT_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $share = $this->shareRepository->accepted($shareId, $input);
            if ($share) {
                return $this->sendResponse($share, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


    public function acceptedShareRecord(Request $request)
    {
        try {
            $share = $this->shareRepository->findBy(['to' => Auth::user()->id, 'record' => $request->id, 'status' => STATUS_REQUEST_VALUE]);
            if (!$share) {
                return $this->sendError("Sharing not found with id user: " . Auth::user()->id . "!", 404);
            }
            $record = $this->recordRepository->findBy(['id' => $request->id]);
            if (!$record) {
                return $this->sendError("Record not found!", 404);
            }
            $shareId = $share->id;
            if ($record->chg == CHG_DELETE_VALUE || $record->chg == CHG_INVALID_VALUE) {
                $input['chg'] = CHG_DELETE_VALUE;
                $share = $this->shareRepository->accepted($shareId, $input);
                return $this->sendResponse($share, 'Invalid');
            }
            $input['status'] = STATUS_ACCEPT_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $share = $this->shareRepository->accepted($shareId, $input);
            if ($share) {
                return $this->sendResponse($share, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    /**
     *  @param ShareRequest $request
     */
    public function share(ShareRequest $request)
    {
        try {
            $request->validated();
            $status = generate_status();
            $input = $request->all();
            $input['user'] = Auth::id();
            $input['record'] = $request->record;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $input['to'] = $request->to;
            $input['status'] = $request->to ? STATUS_ACCEPT_VALUE : STATUS_REQUEST_VALUE;
            $share = $this->shareRepository->create($input);
            if ($share->to) {
                $email = $share->people->email;
            }
            if ($share->mail) {
                $email = $share->mail;
            }
            if ($share) {
                //send mail to email accept
                Mail::to($email)->send(new NotificationInvite($status));
                if (Mail::failures()) {
                    return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                }
            }
            if ($share->to) {
                return $this->sendResponse($share, 'Share member with share successfully.');
            } else {
                return $this->sendResponse($share, 'Send Mail successfully.');
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
            $share = $this->shareRepository->findById($request->id);

            if (!$share) {
                return $this->sendError("Share not found with ID : $request->id!", 404);
            }

            $this->shareRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete share successfully.');

        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
