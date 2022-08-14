<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FamilyRequest;
use App\Mail\SendLinkInvite;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\FamilyRepositoryInterface;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use App\Repositories\Interfaces\ShareFamilyRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FamilyController extends BaseController
{
    /**
     * @var FamilyRepositoryInterface
     * @var UserRepositoryInterface
     * @var RecordRepositoryInterface
     */
    protected $familyRepository;
    protected $userRepository;
    protected $recordRepository;
    protected $shareFamilyRepository;

    /**
     * FamilyController constructor.
     * @param FamilyRepositoryInterface $familyRepository
     */
    public function __construct(
        FamilyRepositoryInterface $familyRepository,
        UserRepositoryInterface $userRepository,
        RecordRepositoryInterface $recordRepository,
        RecordItemRepositoryInterface $recordItemRepository,
        MediaRepositoryInterface $mediaRepository,
        HospitalRepositoryInterface $hospitalRepository,
        DoctorRepositoryInterface $doctorRepository,
        RecordKeywordRepositoryInterface $recordKeywordRepository,
        FolderRepositoryInterface $folderRepository,
        KeywordRepositoryInterface $keywordRepository,
        FavoriteRepositoryInterface $favoriteRepository,
        ShareFamilyRepositoryInterface $shareFamilyRepository
    ) {
        $this->familyRepository = $familyRepository;
        $this->userRepository = $userRepository;
        $this->recordRepository = $recordRepository;
        $this->recordItemRepository = $recordItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->doctorRepository = $doctorRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->recordKeywordRepository = $recordKeywordRepository;
        $this->folderRepository = $folderRepository;
        $this->keywordRepository = $keywordRepository;
        $this->favoriteRepository = $favoriteRepository;
        $this->shareFamilyRepository = $shareFamilyRepository;
    }

    /**
     * @param null
     */
    public function index()
    {
        try {
            $family = $this->familyRepository->allBy([
                'type' => FAMILY_KEY_VALUE,
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ]);
            $family->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
            return $this->sendResponse($family, 'Get family list successfully.');
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param null
     */
    public function listFamily()
    {
        try {
            $families = $this->familyRepository->allBy([
                'type' => FAMILY_KEY_VALUE,
                'user' => Auth::user()->id,
                'chg' => CHG_VALID_VALUE,
            ]);
            foreach ($families as $key => $value) {
                $users = $this->userRepository->findByEmail($value->email);
                if (!$users) {
                    return $this->sendError("No user found from $value->email!", 404);
                }
                foreach ($users as $key => $user) {
                    $value->memberId = $user->id;
                }
            }
            $families->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
            return $this->sendResponse($families, 'Get family list successfully.');
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    // /**
    //  * @param Request $request
    //  */
    // public function detail($id)
    // {
    //     try {
    //         $family = $this->familyRepository->getDetail($id);

    //         if($family) {
    //             return $this->sendResponse($family, 'Get family detail successfully.');
    //         }

    //         return $this->sendError("Not found!", 404);
    //     } catch (\Exception $e) {
    //         throw $e;
    //         return $this->sendError("Something when wrong!", 500);
    //     }
    // }

    // /**
    //  *  @param FamilyRequest $request
    //  */
    // public function store(Request $request)
    // {
    //     try {
    //         if(exist($request->ID)) {
    //             $request->validate([
    //                 'title' => 'required|min:3|max:128',
    //                 'id' => 'required|min:3|max:1024',
    //                 'remark' => 'min:3|max:1024',
    //             ]);
    //         }
    //         // $validated = $request->validated();
    //         $validated = $request->accepted('email');
    //         dd($validated);
    //         $request->validated()->except('email');

    //         $input = $request->all();
    //         dd($input);
    //         $input['type'] = FAMILY_KEY_VALUE;
    //         $input['user'] = Auth::user()->id;
    //         $input['new_by'] = Auth::user()->id;
    //         $input['upd_by'] = Auth::user()->id;
    //         $input['upd_ts'] = Carbon::now();

    //         $family = $this->familyRepository->create($input);

    //         if($family) {
    //             return $this->sendResponse($family, 'Create family successfully.');
    //         }
    //     } catch (\Exception $e) {
    //         throw $e;
    //         return $this->sendError("Something when wrong!", 500);
    //     }
    // }

    /**
     *  @param FamilyRequest $request
     */
    public function update(FamilyRequest $request)
    {
        try {
            $hospital = $this->familyRepository->findById($request->id);

            if (!$hospital) {
                return $this->sendError("Hospital not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $hospital = $this->familyRepository->update($request->id, $input);

            if ($hospital) {
                return $this->sendResponse($hospital, 'Update hospital successfully.');
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
            $family = $this->familyRepository->findById($request->id);

            if (!$family) {
                return $this->sendError("Family not found with ID : $request->id!", 404);
            }

            $this->familyRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete family successfully.');
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param FamilyRequest $request
     */
    public function sendRequest(FamilyRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            if ($request->email == Auth::user()->email) {
                return $this->sendError('You cant not sent request to yourself', 402);
            }
            $fam = $this->familyRepository->findBy(['email' => $request->email, 'user' => Auth::id()]);
            if ($fam) {
                if ($fam->chg == REQUEST_STATUS_DENIED_VALUE) {
                    $user = $fam->user;
                    $sentAgain = $this->familyRepository->sendAgain($user, $request->email);
                    DB::commit();
                    return $this->sendResponse($sentAgain, 'Sent request back');
                }
                $fam->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                if ($fam->chg == REQUEST_STATUS_WAIT_VALUE) {
                    return $this->sendResponse($fam, 'Request has been sent');
                }
                if ($fam->chg == CHG_VALID_VALUE) {
                    return $this->sendResponse($fam, 'This people was in family');
                }
                if ($fam->chg == CHG_INVALID_VALUE) {
                    $user = $fam->user;
                    $addAgain = $this->familyRepository->addAgain($user, $request->email);
                    DB::commit();
                    return $this->sendResponse($addAgain, 'This people was added in family again');
                }
            } else {
                $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
                if (!empty($user)) {
                    $input['type'] = FAMILY_KEY_VALUE;
                    $input['name'] = $user->name;
                    $input['email'] = $request->email;
                    $input['user'] = Auth::user()->id;
                    $input['chg'] = REQUEST_STATUS_WAIT_VALUE;
                    $input['new_by'] = Auth::user()->id;
                    $input['new_ts'] = Carbon::now();
                    $input['upd_by'] = Auth::user()->id;
                    $input['upd_ts'] = Carbon::now();
                    $family = $this->familyRepository->create($input);
                    $family->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                    if ($family) {
                        DB::commit();
                        return $this->sendResponse($family, 'Send request');
                    }
                } else {
                    return $this->sendError('Email is not exist', 404);
                }
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param Request $request
     */
    public function remove(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->findById($request->id);
            if (!$user) {
                return $this->sendError("User not found with ID : $request->id!", 404);
            }
            $family1 = $this->familyRepository->findBy(['user' => Auth::id(), 'email' => $user->email, 'chg' => CHG_VALID_VALUE]);
            if (!$family1) {
                return $this->sendError("Family1 not found with ID : $request->id!", 404);
            }
            $family2 = $this->familyRepository->findFamilyValid($request->id, Auth::user()->email);
            if (!$family2) {
                return $this->sendError("Family2 not found with ID : " . Auth::id(), 404);
            }
            $this->familyRepository->removeFamily(Auth::id(), $user->email);
            $this->familyRepository->removeFamily($request->id, Auth::user()->email);
            DB::commit();
            return $this->sendResponse([], 'Remove family successfully.');
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function receivedRequest()
    {
        try {
            $id = Auth::user()->id;
            $toEmail = Auth::user()->email;
            $toName = Auth::user()->name;
            $family = $this->familyRepository->findBy(['email' => $toEmail, 'chg' => REQUEST_STATUS_WAIT_VALUE]);
            if ($family != null) {
                $userId = $family->user;
                $fromUser = $this->userRepository->findById($userId);
                $fromEmail = $fromUser->email;
                $fromName = $fromUser->name;
                $data = ['from' => $fromEmail, 'fromName' => $fromName, 'to' => $toEmail, 'toName' => $toName];
            } else {
                return $this->sendError("No request!", 400);
            }
            if ($family) {
                return $this->sendResponse($data, "You have a request from $fromUser->email");
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function listReceivedRequest()
    {
        try {
            $id = Auth::user()->id;
            $toEmail = Auth::user()->email;
            $toName = Auth::user()->name;
            $families = $this->familyRepository->getListRequests($toEmail);
            $count = 0;
            $info = [];
            if ($families != null) {
                foreach ($families as $key => $family) {
                    $userId = $family->user;
                    $fromUser = $this->userRepository->findById($userId);
                    $fromEmail = $fromUser->email;
                    $fromName = $fromUser->name;
                    $fromId = $fromUser->id;
                    $info[] = ['from' => $fromEmail, 'fromName' => $fromName, 'fromUserId' => $fromId, 'to' => $toEmail, 'toName' => $toName];
                    $count += 1;
                }
                $families->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
            } else {
                return $this->sendError("No request!", 400);
            }
            if ($families) {
                $data = ['infomation' => $info];
                return $this->sendResponse($data, "You have $count request");
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function listReceivedRequestJoin()
    {
        try {
            $id = Auth::user()->id;
            $toEmail = Auth::user()->email;
            $toName = Auth::user()->name;
            $families = $this->familyRepository->getListRequestsJoin($toEmail);
            $count = 0;
            $info = [];
            if (!$families) {
                return $this->sendError("No request!", 404);
            }
            foreach ($families as $key => $family) {
                $userId = $family->user;
                $fromUser = $this->userRepository->findById($userId);
                $fromEmail = $fromUser->email;
                $fromName = $fromUser->name;
                $fromId = $fromUser->id;
                $info[] = ['from' => $fromEmail, 'fromName' => $fromName, 'fromUserId' => $fromId, 'to' => $toEmail, 'toName' => $toName];
                $count += 1;
            }
            $families->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
            $data = ['infomation' => $info];
            return $this->sendResponse($data, "You have $count request");

        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function acceptedRequest()
    {
        try {
            $email = Auth::user()->email;
            $family = $this->familyRepository->findBy(['email' => $email, 'chg' => REQUEST_STATUS_WAIT_VALUE]);
            if ($family != null) {
                $familyId = $family->id;
            } else {
                return $this->sendResponse($family, 'This people was in family.');
            }
            if (!$family) {
                return $this->sendError("Family not found with email : $email!", 404);
            }
            $input['chg'] = CHG_VALID_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            // dd($input);

            $family = $this->familyRepository->accepted($familyId, $input);
            // dd($this->familyRepository);
            if ($family) {
                return $this->sendResponse($family, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function acceptedRequestFamily(Request $request)
    {
        try {
            $email = Auth::user()->email;
            $family = $this->familyRepository->findBy(['email' => $email, 'user' => $request->id, 'chg' => REQUEST_STATUS_WAIT_VALUE]);
            // dd($family);
            if ($family != null) {
                $familyId = $family->id;
            } else {
                return $this->sendResponse($family, 'This people was in family.');
            }
            if (!$family) {
                return $this->sendError("Family not found with email : $email!", 404);
            }
            $input['chg'] = CHG_VALID_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $family = $this->familyRepository->accepted($familyId, $input);
            if ($family) {
                return $this->sendResponse($family, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function deniedRequestFamily(Request $request)
    {
        try {
            $email = Auth::user()->email;
            $family = $this->familyRepository->findBy(['email' => $email, 'user' => $request->id, 'chg' => REQUEST_STATUS_WAIT_VALUE]);
            if (!$family) {
                return $this->sendError("Resquet not found with email : $email!", 404);
            }
            $familyId = $family->id;
            $input['chg'] = REQUEST_STATUS_DENIED_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            $family = $this->familyRepository->accepted($familyId, $input);
            if ($family) {
                return $this->sendResponse($family, 'Request is denied.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     *  @param FamilyRequest $request
     */
    public function sendRequestToJoin(FamilyRequest $request)
    {
        try {
            $request->validated();
            $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
            if (!empty($user)) {
                $userId = $user->id;
                $input['type'] = FAMILY_KEY_VALUE;
                $input['name'] = Auth::user()->name;
                $input['email'] = Auth::user()->email;
                $input['user'] = $userId;
                $input['chg'] = REQUEST_STATUS_JOIN_VALUE;
                $input['new_by'] = Auth::user()->id;
                $input['new_ts'] = Carbon::now();
                $input['upd_by'] = Auth::user()->id;
                $input['upd_ts'] = Carbon::now();
                $family = $this->familyRepository->create($input);
            } else {
                return $this->sendError('Email is not exist', 404);
            }

            if ($family) {
                return $this->sendResponse($family, 'Send request');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function sendJoinRequest(FamilyRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            if ($request->email == Auth::user()->email) {
                return $this->sendError('You cant not sent request to yourself', 402);
            }
            $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
            if (empty($user)) {
                return $this->sendError("User is not exist!", 404);
            }
            $userId = $user->id;
            $fam = $this->familyRepository->findBy(['email' => Auth::user()->email, 'user' => $userId]);
            if ($fam) {
                if ($fam->chg == REQUEST_STATUS_DENIED_VALUE) {
                    $user = $fam->user;
                    $sentAgain = $this->familyRepository->sendAgain($user, $request->email);
                    DB::commit();
                    return $this->sendResponse($sentAgain, 'Sent request back');
                }
                $fam->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                if ($fam->chg == REQUEST_STATUS_WAIT_VALUE) {
                    return $this->sendResponse($fam, 'Request has been sent');
                }
                if ($fam->chg == CHG_VALID_VALUE) {
                    return $this->sendResponse($fam, 'This people was in family');
                }
                if ($fam->chg == CHG_INVALID_VALUE) {
                    $user = $fam->user;
                    $addAgain = $this->familyRepository->addAgain($user, $request->email);
                    DB::commit();
                    return $this->sendResponse($addAgain, 'This people was added in family again');
                }
            } else {
                $userId = $user->id;
                $input['type'] = FAMILY_KEY_VALUE;
                $input['name'] = Auth::user()->name;
                $input['email'] = Auth::user()->email;
                $input['user'] = $userId;
                $input['chg'] = REQUEST_STATUS_JOIN_VALUE;
                $input['new_by'] = Auth::user()->id;
                $input['new_ts'] = Carbon::now();
                $input['upd_by'] = Auth::user()->id;
                $input['upd_ts'] = Carbon::now();
                $family = $this->familyRepository->create($input);
                $family->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts', 'user']);
                if ($family) {
                    DB::commit();
                    return $this->sendResponse($family, 'Send request to join');
                }
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function receivedJoinRequest()
    {
        try {
            $id = Auth::user()->id;
            $family = $this->familyRepository->findBy(['user' => $id, 'chg' => REQUEST_STATUS_ACCEPT_WAITING_VALUE]);
            if (!$family) {
                return $this->sendResponse($family, "No request!");
            }
            $fromEmail = $family ? $family->email : null;
            $fromUser = $this->userRepository->findBy(['email' => $fromEmail, 'chg' => CHG_VALID_VALUE]);
            $fromName = $fromUser->name;
            $userEmail = Auth::user()->email;
            $userName = Auth::user()->name;
            if ($family) {
                $data = ['from' => $fromEmail, 'fromName' => $fromName, 'to' => $userEmail, 'toName' => $userName];
                return $this->sendResponse($data, "You have a request to join family from $fromEmail");
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function acceptedJoinRequest()
    {
        try {
            $id = Auth::user()->id;
            $family = $this->familyRepository->findBy(['user' => $id, 'chg' => REQUEST_STATUS_JOIN_VALUE]);
            if ($family != null) {
                $familyId = $family->id;
            } else {
                return $this->sendResponse($family, 'You were in family.');
            }

            if (!$family) {
                return $this->sendError("Family not found with id : $id!", 404);
            }
            $input['chg'] = CHG_VALID_VALUE;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();
            // dd($input);

            $family = $this->familyRepository->accepted($familyId, $input);
            // dd($family);
            if ($family) {
                return $this->sendResponse($family, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function searchRecordInFamily(Request $request)
    {
        try {
            if (isset($request->keyword)) {
                $recordSearch = $this->recordRepository->searchRecordsByFamily($request);
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
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
    /**
     *  @param FamilyRequest $request
     */
    public function sendRequestInvite(FamilyRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            if ($request->email == Auth::user()->email) {
                return $this->sendError('You cant not sent request to yourself', 402);
            }
            $fam = $this->familyRepository->findBy(['email' => $request->email, 'user' => Auth::id()]);
            if ($fam) {
                if ($fam->chg == REQUEST_STATUS_DENIED_VALUE) {
                    $user = $fam->user;
                    $sentAgain = $this->familyRepository->sendAgain($user, $request->email);
                    DB::commit();
                    return $this->sendResponse($sentAgain, 'Sent request back');
                }
                $fam->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                if ($fam->chg == REQUEST_STATUS_WAIT_VALUE) {
                    return $this->sendResponse($fam, 'Request has been sent');
                }
                if ($fam->chg == CHG_VALID_VALUE) {
                    return $this->sendResponse($fam, 'This people was in family');
                }
                if ($fam->chg == CHG_INVALID_VALUE) {
                    $user = $this->userRepository->findByMail($request->email);
                    if (!$user) {
                        return $this->sendError("User not found!", 404);
                    }
                    $userId = $user->id;
                    $addAgain = $this->familyRepository->addAgain(Auth::id(), $request->email);
                    $addAgain2 = $this->familyRepository->addAgain($userId, Auth::user()->email);
                    DB::commit();
                    return $this->sendResponse([], 'This people was added in family again');
                }
            } else {
                $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
                if (!empty($user)) {
                    $input['type'] = FAMILY_KEY_VALUE;
                    $input['name'] = $user->name;
                    $input['email'] = $request->email;
                    $input['user'] = Auth::user()->id;
                    $input['chg'] = REQUEST_STATUS_WAIT_VALUE;
                    $input['new_by'] = Auth::user()->id;
                    $input['new_ts'] = Carbon::now();
                    $input['upd_by'] = Auth::user()->id;
                    $input['upd_ts'] = Carbon::now();

                    $input2['type'] = FAMILY_KEY_VALUE;
                    $input2['name'] = Auth::user()->name;
                    $input2['email'] = Auth::user()->email;
                    $input2['user'] = $user->id;
                    $input2['chg'] = REQUEST_STATUS_ACCEPT_WAITING_VALUE;
                    $input2['new_by'] = Auth::user()->id;
                    $input2['new_ts'] = Carbon::now();
                    $input2['upd_by'] = Auth::user()->id;
                    $input2['upd_ts'] = Carbon::now();
                    $family1 = $this->familyRepository->create($input);
                    $family2 = $this->familyRepository->create($input2);
                    DB::commit();
                    $data = ['family1' => $family1, 'family2' => $family2];
                    if ($family2 && $family1) {
                        $family1->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        $family2->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        return $this->sendResponse($data, 'Send request');
                    }
                } else {
                    $shareFamilyItem = [
                        'from' => Auth::user()->id,
                        'to' => null,
                        'mail' => $request->email,
                        'chg' => CHG_VALID_VALUE,
                        'new_by' => Auth::user()->id,
                        'new_ts' => Carbon::now(),
                        'upd_by' => Auth::user()->id,
                        'upd_ts' => Carbon::now(),
                    ];
                    $this->shareFamilyRepository->create($shareFamilyItem);
                    DB::commit();

                    Mail::to($request->email)->send(new SendLinkInvite());

                    if (Mail::failures()) {
                        return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                    }
                    return $this->sendResponse(['success' => 'true'], 'Send Mail successfully.');                }
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function acceptedRequestInvite(Request $request)
    {
        DB::beginTransaction();
        try {
            $family1 = $this->familyRepository->findBy(['email' => Auth::user()->email, 'user' => $request->id]);
            if (!$family1) {
                return $this->sendError("Family1 not found with user ID: $request->id ", 404);
            }
            if ($family1->chg == CHG_VALID_VALUE) {
                return $this->sendResponse($family1, 'This people was in family.');
            }
            if ($family1->chg == REQUEST_STATUS_WAIT_VALUE) {
                $family1Id = $family1->id;
                $input['chg'] = CHG_VALID_VALUE;
                $input['upd_by'] = Auth::user()->id;
                $input['upd_ts'] = Carbon::now();
            }
            $user = $this->userRepository->findById($request->id);
            $userEmail = (isset($user)) ? $user->email : $this->sendError("User with id $request->id is not exist", 404);
            $family2 = $this->familyRepository->findFamily(Auth::user()->id, $userEmail);
            if (!$family2) {
                return $this->sendError("Family2 not found with email : $userEmail", 404);
            }
            if ($family2->chg == CHG_VALID_VALUE) {
                return $this->sendResponse($family1, 'This people was in family.');
            }
            if ($family2->chg == REQUEST_STATUS_WAIT_VALUE) {
                $family2Id = $family2->id;
                $input2['chg'] = CHG_VALID_VALUE;
                $input2['upd_by'] = Auth::user()->id;
                $input2['upd_ts'] = Carbon::now();
            }
            if ($family2->chg == REQUEST_STATUS_ACCEPT_WAITING_VALUE) {
                $family2Id = $family2->id;
                $input2['chg'] = CHG_VALID_VALUE;
                $input2['upd_by'] = Auth::user()->id;
                $input2['upd_ts'] = Carbon::now();
            }
            $family1 = $this->familyRepository->accepted($family1Id, $input);
            $family2 = $this->familyRepository->accepted($family2Id, $input2);
            DB::commit();
            $data = ['family1' => $family1, 'family2' => $family2];

            if ($family1 && $family2) {
                return $this->sendResponse($data, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function sendRequestJoin(FamilyRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();
            if ($request->email == Auth::user()->email) {
                return $this->sendError('You cant not sent request to yourself', 402);
            }
            $fam = $this->familyRepository->findBy(['email' => $request->email, 'user' => Auth::id()]);

            if ($fam) {
                $fam->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                switch ($fam->chg) {
                    case REQUEST_STATUS_DENIED_VALUE:
                        $user = $fam->user;
                        $sentAgain = $this->familyRepository->sendAgain($user, $request->email);
                        DB::commit();
                        return $this->sendResponse($sentAgain, 'Sent request back');
                        break;
                    case REQUEST_STATUS_DENIED_VALUE:
                        return $this->sendResponse($fam, 'Request has been sent');
                        break;
                    case CHG_INVALID_VALUE:
                        return $this->sendResponse($fam, 'This people was in family');
                        break;
                    default:
                        return $this->sendResponse($fam, 'This people was in family');
                        break;
                }
            } else {
                $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
                if (!empty($user)) {
                    $input['type'] = FAMILY_KEY_VALUE;
                    $input['name'] = $user->name;
                    $input['email'] = $request->email;
                    $input['user'] = Auth::user()->id;
                    $input['chg'] = REQUEST_STATUS_JOIN_VALUE;
                    $input['new_by'] = Auth::user()->id;
                    $input['new_ts'] = Carbon::now();
                    $input['upd_by'] = Auth::user()->id;
                    $input['upd_ts'] = Carbon::now();

                    $input2['type'] = FAMILY_KEY_VALUE;
                    $input2['name'] = Auth::user()->name;
                    $input2['email'] = Auth::user()->email;
                    $input2['user'] = $user->id;
                    $input2['chg'] = REQUEST_STATUS_ACCEPT_WAITING_VALUE;
                    $input2['new_by'] = Auth::user()->id;
                    $input2['new_ts'] = Carbon::now();
                    $input2['upd_by'] = Auth::user()->id;
                    $input2['upd_ts'] = Carbon::now();
                    $family1 = $this->familyRepository->create($input);
                    $family2 = $this->familyRepository->create($input2);
                    DB::commit();
                    $data = ['family1' => $family1, 'family2' => $family2];
                    if ($family2 && $family1) {
                        $family1->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        $family2->makeHidden(['type', 'org', 'dept', 'post', 'pref', 'pref_code', 'address', 'xaddress', 'remark', 'phone', 'chg', 'new_by', 'new_ts', 'upd_by', 'upd_ts']);
                        return $this->sendResponse($data, 'Send request');
                    }
                } else {
                    return $this->sendError('Email is not exist', 404);
                }
            }
        } catch (\Exception$e) {
            DB::rollback();
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    public function acceptedRequestJoin(Request $request)
    {
        try {
            $family1 = $this->familyRepository->findBy(['email' => Auth::user()->email, 'user' => $request->id]);
            if (!$family1) {
                return $this->sendError("Family1 not found with user ID: $request->id ", 404);
            }
            if ($family1->chg == CHG_VALID_VALUE) {
                return $this->sendResponse($family1, 'This people was in family.');
            }
            if ($family1->chg == REQUEST_STATUS_JOIN_VALUE) {
                $family1Id = $family1->id;
                $input['chg'] = CHG_VALID_VALUE;
                $input['upd_by'] = Auth::user()->id;
                $input['upd_ts'] = Carbon::now();
            }
            $user = $this->userRepository->findById($request->id);
            $userEmail = (isset($user)) ? $user->email : $this->sendError("User with id $request->id is not exist", 404);
            $family2 = $this->familyRepository->findFamily(Auth::user()->id, $userEmail);
            if (!$family2) {
                return $this->sendError("Family2 not found with email : $userEmail", 404);
            }
            if ($family2->chg == CHG_VALID_VALUE) {
                return $this->sendResponse($family1, 'This people was in family.');
            }
            if ($family2->chg == REQUEST_STATUS_WAIT_VALUE || $family2->chg == REQUEST_STATUS_ACCEPT_WAITING_VALUE) {
                $family2Id = $family2->id;
                $input2['chg'] = CHG_VALID_VALUE;
                $input2['upd_by'] = Auth::user()->id;
                $input2['upd_ts'] = Carbon::now();
            }

            $family1 = $this->familyRepository->accepted($family1Id, $input);
            $family2 = $this->familyRepository->accepted($family2Id, $input2);
            $data = ['family1' => $family1, 'family2' => $family2];

            if ($family1 && $family2) {
                return $this->sendResponse($data, 'Request is accepted.');
            }
        } catch (\Exception$e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }
}
