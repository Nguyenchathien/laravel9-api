<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\V1\AppleLoginRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AppleController extends BaseController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(AppleLoginRequest $request)
    {
        try {
            $appleParams = $request->input();
        } catch (Exception $e) {
            return $this->sendError(['error'=>$e->getMessage()], 500);
        }

        return empty($appleParams['identity_token'] || $appleParams['authorization_code'])
            ? $this->sendError(["error" => "Authorization Apple ID returned an invalid parameters"], 404)
            : $this->loginOrCreateAccount($appleParams);
    }

    public function loginOrCreateAccount($appleParams)
    {
        try {
            $verifyApple = $this->verifyWithApple($appleParams);
            if (!isset($verifyApple->access_token)) {
                return $this->sendError(['error'=>'Authorization with Apple ID failed'], 409);
            }
            $payload = explode('.', $verifyApple->id_token)[1];
            $payload = json_decode(base64_decode($payload));
            $user = $this->userRepository->findByAppleId($payload->sub);

            if($user && $user->type === LOGIN_MAIL_TYPE_VALUE) {
                return $this->sendError(['error'=>'The email was registered.'], 409);
            }

            if($user) {
                $input = [
                    'temail' => $payload->email,
                    'apple_id'=> $payload->sub,
                    'token' => $verifyApple->access_token,
                    'upd_ts' => Carbon::now()
                ];

                $this->userRepository->update($user->id, $input);

                Auth::login($user);
                $success_token = $user->createToken('Personal Access Token')->plainTextToken;

                $this->userRepository->update(
                    $user->id, [
                    'token' => $success_token,
                ]);

                $userDetail = $this->userRepository->findById($user->id);

                return $this->sendResponseGetToken($userDetail, $success_token, 'User login successfully.');
            } else {
                $input = [
                    'type' => LOGIN_APPLE_TYPE_VALUE,
                    'name' => $appleParams['name'],
                    'email' => $payload->email,
                    'temail' => $payload->email,
                    'password' => Hash::make(USER_PASSWORD_DEFAULT_VALUE),
                    'key' => '',
                    'token' => $verifyApple->access_token,
                    'plan' => FREE_PLAN_VALUE,
                    'gender' => '',
                    'progress' => '',
                    'status' => USER_AUTHENTICATED_STATUS_KEY_VALUE,
                    'apple_id'=> $payload->sub,
                    'new_by' => 'Admin',
                    'upd_by' => 'Admin',
                    'upd_ts' => Carbon::now()
                ];

                // Create a new user
                $user = $this->userRepository->create($input);

                if ($user) {
                    Auth::login($user);
                    $success_token = $user->createToken('Personal Access Token')->plainTextToken;

                    $this->userRepository->update(
                        $user->id, [
                        'token' => $success_token,
                    ]);

                    $userDetail = $this->userRepository->findById($user->id);

                    return $this->sendResponseGetToken($userDetail, $success_token, 'User register successfully.');
                }
            }


        } catch(Exception $error) {
            return $this->sendError(['error'=>'Unauthorized'], 500);
        }

    }

    function verifyWithApple($appleParams)
    {
        $id_token = $appleParams['identity_token'];
        $client_authorization_code = $appleParams['authorization_code'];
        $teamId = TEAM_ID ;
        $clientId = CLIENT_ID ;
        $privKey = file_get_contents(base_path()."/public_key.p8");
        $keyID = KEY_ID ;

        $apple_jwk_keys = json_decode(file_get_contents(URL_VERIFY_KID), null, 512, JSON_OBJECT_AS_ARRAY) ;
        $keys = [] ;
        foreach($apple_jwk_keys['keys'] as $key)
            $keys[] = (array)$key ;
        $jwks = ['keys' => $keys];

        $header_base_64 = explode('.', $id_token)[0];
        $kid = JWT::jsonDecode(JWT::urlsafeB64Decode($header_base_64));
        $kid = $kid->kid;

        $public_key = JWK::parseKeySet($jwks);
        $public_key = $public_key[$kid];

        $payload = array(
            "iss" => $teamId,
            'aud' => URL_AUD_APPLE,
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $clientId
        );

        $client_secret = JWT::encode($payload, $privKey, 'ES256', $keyID);

        $post_data = [
            'client_id' => $clientId,
            'grant_type' => 'authorization_code',
            'code' => $client_authorization_code,
            'client_secret' => $client_secret
        ];

        return $this->http(URL_AUTH_APPLE, $post_data);
    }

    function http($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/x-www-form-urlencoded',
            'User-Agent: curl',  //Apple requires a user agent header at the token endpoint
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $curl_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($curl_response);
    }

}
