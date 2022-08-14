<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\V1\ConfirmCodeRequest;
use App\Http\Requests\V1\SendMailRequest;
use App\Http\Requests\V1\UserLoginRequest;
use App\Mail\NotificationMail;
use App\Models\User;
use App\Repositories\Interfaces\FamilyRepositoryInterface;
use App\Repositories\Interfaces\ShareFamilyRepositoryInterface;
use App\Repositories\Interfaces\ShareRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;

class AuthController extends BaseController
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    protected $shareFamilyRepository;
    protected $familyRepository;

    /**
     * AuthController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        ShareFamilyRepositoryInterface $shareFamilyRepository,
        FamilyRepositoryInterface $familyRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->shareFamilyRepository = $shareFamilyRepository;
        $this->familyRepository = $familyRepository;
    }

    public function login(UserLoginRequest $request)
    {
        try {
            $request->validated();

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $success_token = $user->createToken('Personal Access Token')->plainTextToken;

                //Update tokens
                $this->userRepository->update($user->id, ['token' => $success_token]);

                return $this->sendResponseGetToken($user, $success_token, 'User login successfully.');
            } else {
                return $this->sendError(['error' => 'Wrong username or password!'], 401);
            }
        } catch (Exception $error) {
            return $this->sendError(['error' => 'Unauthorized'], 500);
        }
    }

    public function register(SendMailRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated();

            $user = $this->userRepository->findBy([
                'email' => $request->input('email'),
                'chg' => CHG_VALID_VALUE
                // 'status' => USER_AUTHENTICATED_STATUS_KEY_VALUE
            ]);

            //get random code
            $code = generate_unique_code();

            if($user) {
                if($user->status === USER_AUTHENTICATED_STATUS_KEY_VALUE) {
                    return $this->sendError(['error'=>'The email was registered.'], 409);
                } else {
                    //send mail to email register
                    Mail::to($request->email)->send(new NotificationMail($code));

                    if (Mail::failures()) {
                        return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                    }

                    $this->userRepository->update(
                        $user->id, [
                            'code' => $code
                        ]);

                    DB::commit();
                    return $this->sendResponse(['success' => 'true'], 'Send Mail successfully.');
                }
            } else {
                $input = $request->all();
                $input['type'] = LOGIN_MAIL_TYPE_VALUE;
                $input['name'] = USER_NAME_DEFAULT_VALUE;
                $input['temail'] = $request->email;
                $input['gender'] = '';
                $input['password'] = Hash::make(PASSWORD_DEFAULT_VALUE);
                $input['key'] = '';
                $input['token'] = '';
                $input['code'] = $code;
                $input['plan'] = FREE_PLAN_VALUE;
                $input['progress'] = '';
                $input['status'] = USER_WAITING_STATUS_KEY_VALUE;
                $input['new_by'] = NEW_USER_DEFAULT_VALUE;
                $input['upd_by'] = NEW_USER_DEFAULT_VALUE;
                $input['upd_ts'] = Carbon::now();

                // Create a new user
                $user = $this->userRepository->create($input);

                if ($user) {
                    $shareInviteFamily = $this->shareFamilyRepository->findBy([
                        'mail' => $request->input('email'),
                    ]);
                    if ($shareInviteFamily) {
                        $this->shareFamilyRepository->update($shareInviteFamily->id, ['to' => $user->id]);
                        $userShared = $this->userRepository->findById((int) $shareInviteFamily->from);
                        $input['type'] = FAMILY_KEY_VALUE;
                        $input['name'] = $user->name;
                        $input['email'] = $shareInviteFamily->mail;
                        $input['user'] = $shareInviteFamily->from;
                        $input['chg'] = REQUEST_STATUS_WAIT_VALUE;
                        $input['new_by'] = $shareInviteFamily->from;
                        $input['new_ts'] = Carbon::now();
                        $input['upd_by'] = $shareInviteFamily->from;
                        $input['upd_ts'] = Carbon::now();

                        $input2['type'] = FAMILY_KEY_VALUE;
                        $input2['name'] = $userShared->name;
                        $input2['email'] = $userShared->email;
                        $input2['user'] = $user->id;
                        $input2['chg'] = REQUEST_STATUS_ACCEPT_WAITING_VALUE;
                        $input2['new_by'] = $shareInviteFamily->from;
                        $input2['new_ts'] = Carbon::now();
                        $input2['upd_by'] = $shareInviteFamily->from;
                        $input2['upd_ts'] = Carbon::now();
                        $this->familyRepository->create($input);
                        $this->familyRepository->create($input2);
                    }
                    //send mail to email register
                    Mail::to($request->email)->send(new NotificationMail($code));

                    if (Mail::failures()) {
                        return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                    }
                }

                DB::commit();
                return $this->sendResponse(['success' => 'true'], 'Send Mail successfully.');
            }
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 500);
        }
    }

    public function confirmCode(ConfirmCodeRequest $request)
    {
        try {
            $request->validated();
            $user = $this->userRepository->findBy(['code' => $request->code, 'chg' => CHG_VALID_VALUE]);

            if ($user) {
                $success_token = $user->createToken('Personal Access Token')->plainTextToken;

                $this->userRepository->update(
                    $user->id, [
                        'token' => $success_token,
                        'code' => '',
                        'status' => USER_AUTHENTICATED_STATUS_KEY_VALUE
                    ]);

                $user->makeHidden(['token', 'code', 'new_ts', 'upd_by', 'upd_ts']);

                return $this->sendResponseGetToken($user, $success_token, 'Verify code successfully.');
            }

            return $this->sendError(['error' => 'User not found!'], 404);
        } catch (Exception $error) {
            return $this->sendError(['error' => 'Unauthorized'], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::user()->token()->delete();
        return $this->sendResponse(['success' => 'true'], 'You have been successfully logged out!');
    }

    public function forgotPassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->findBy(['email' => $request->email, 'chg' => CHG_VALID_VALUE]);
            if ($user) {
                $userId = $user->id;
                //get random code
                $code = generate_unique_code();
                $input['code'] = $code;
                $input['status'] = USER_WAITING_STATUS_KEY_VALUE;
                $input['upd_ts'] = Carbon::now();
                // Update code
                $user = $this->userRepository->update($userId, $input);
                //send mail to email forgot password
                DB::commit();
                Mail::to($request->email)->send(new NotificationMail($code));
                if (Mail::failures()) {
                    return $this->sendError('Bad gateway.', ['error' => 'Bad gateway'], 502);
                } else {
                    return $this->sendResponse(['success' => 'true'], 'Send Mail successfully.');
                }
            } else {
                return $this->sendError('Email dose not exist successfully.');
            }
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 500);
        }
    }
}
