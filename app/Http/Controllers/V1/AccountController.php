<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\AccountRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Http\Requests\V1\AccountRequest;
use App\Http\Requests\V1\AvatarRequest;
use App\Http\Requests\V1\ConfirmCodeRequest;
use App\Http\Controllers\BaseController;
use App\Mail\NotificationMail;
use Mail;
use App\Models\User;
use Carbon\Carbon;
use DB;
use URL;

class AccountController extends BaseController
{
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;
    protected $userRepository;

    /**
     * AccountController constructor.
     * @param AccountRepositoryInterface $accountRepository
     */
    public function __construct(AccountRepositoryInterface $accountRepository, UserRepositoryInterface $userRepository)
    {
        // dd('dđ');
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     */
    public function detail($id)
    {
        try {
            $account = $this->accountRepository->findById($id);

            if ($account) {
                return $this->sendResponse($account, 'Get account detail successfully.');
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
    public function searchAccount(Request $request)
    {
        try {
            // dd($request->all());
            $account = $this->accountRepository->Search($request);

            if ($account) {
                return $this->sendResponse($account, 'Search account successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }

    /**
     * @param AccountRequest $request
     */
    public function update(AccountRequest $request)
    {
        try {
            $account = $this->accountRepository->findById($request->id);

            $code = generate_unique_code();

            if (!$account) {
                return $this->sendError("Account not found with ID : $request->id!", 404);
            }
            $request->validated();

            $input = $request->all();
            $input['code'] = $code;
            $input['new_by'] = Auth::user()->id;
            $input['upd_by'] = Auth::user()->id;
            $input['upd_ts'] = Carbon::now();

            $account = $this->accountRepository->update($request->id, $input);

            if ($account) {
                //send mail to email update email
                Mail::to($request->temail)->send(new NotificationMail($code));

                if (Mail::failures()) {
                    return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                }
            }

            if ($account) {
                return $this->sendResponse($account, 'Update account successfully.');
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


    public function confirmCode(ConfirmCodeRequest $request)
    {
        try {
            $request->validated();
            $account = $this->accountRepository->findBy(['code' => $request->code, 'chg' => CHG_VALID_VALUE]);

            if ($account) {
                $this->userRepository->update(
                    $account->id
                );
            }

            return $this->sendError(['error' => 'User not found!'], 404);
        } catch (\Exception $error) {
            return $this->sendError(['error' => 'Unauthorised'], 500);
        }
    }

    public function updateTEmail(Request $request)
    {
        try {
            $account = $this->accountRepository->findBy(['code' => $request->code, 'chg' => CHG_VALID_VALUE]);
            if (!$account) {
                return $this->sendError(['error' => 'Code not matching!'], 404);
            }
            if ($account) {
                $input['temail'] = $request->temail;
                $user = $this->userRepository->update(
                    $account->id,
                    $input
                );
                return $this->sendResponse($user, 'Update account successfully.');
            }
        } catch (\Exception $error) {
            return $this->sendError(['error' => 'Unauthorised'], 500);
        }
    }

    /**
     * @param Request $request
     */
    public function delete(Request $request)
    {
        try {
            $account = $this->accountRepository->findById($request->id);
            if (!$account) {
                return $this->sendError("Account not found with ID : $request->id!", 404);
            }

            $userCurrentId = Auth::user()->id;
            if ($request->id != $userCurrentId) {
                return $this->sendError("You don't have permission to delete this account", 403);
            }

            $this->accountRepository->deleteById($request->id);

            return $this->sendResponse([], 'Delete account successfully.');

        } catch (\Exception $e) {
            throw $e;
            return $this->sendError("Something when wrong!", 500);
        }
    }


    /**
     * @param Request $request
     * @param AvatarRequest $requestAvatar
     */
    public function updateAvatar(AvatarRequest $requestAvatar)
    {
        // dd(Auth::user()->id);
        DB::beginTransaction();
        try {
            $account = $this->accountRepository->findBy(['id' => Auth::id(), 'chg' => CHG_VALID_VALUE]);
            if (!$account) {
                return $this->sendError(['error' => 'Account is not exist!'], 404);
            }
            $requestAvatar->validated();
            $file = $requestAvatar->file('file');
            $fileMime = $file->extension();
            $fileName = time() . '.' . $fileMime;
            $filePath = $file->storeAs('profiles', $fileName, 'public');
            $input['profile_photo_path'] = URL::to('/') . '/storage/' . $filePath;
            $user = $this->userRepository->update($account->id, $input);
            $info = $this->userRepository->findBy(['id' => Auth::id(), 'chg' => CHG_VALID_VALUE]);
            DB::commit();
            return $this->sendResponse($info->profile_photo_path, 'Update avatar successfully.');
        } catch (\Exception $error) {
            DB::rollback();
            return $this->sendError(['error' => 'Unauthorised'], 500);
        }
    }
}
