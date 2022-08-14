<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\V1\ConfirmPasswordRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;

class ConfirmPasswordController extends BaseController
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AuthController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository) 
    {
        $this->userRepository = $userRepository;
    }

    public function index(ConfirmPasswordRequest $request) 
    {
        try {
            $request->validated();

            $user = $this->userRepository->findById($request->user_id);

            if($user) {
                $userUpdate = $this->userRepository->update(
                    $user->id, [
                        'code' => NULL,
                        'password' => Hash::make($request->password),
                        'status' => USER_AUTHENTICATED_STATUS_KEY_VALUE
                    ]); 

                $userDetail = $this->userRepository->findById($request->user_id);

                return $this->sendResponseGetToken($userDetail, $userDetail->token, 'Update password successfully.');
            }

            return $this->sendError(['error'=>'User not found!'], 404);
        } catch(Exception $error) {
            return $this->sendError(['error'=>'Unauthorised'], 500);
        }
    }
}
