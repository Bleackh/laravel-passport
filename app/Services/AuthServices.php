<?php

namespace App\Services;

use App\Repository\AuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthServices
{

    protected $authRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Function: authRegister
     * @param $requeest
     * @return $response
     */
    public function authRegister($request)
    {
        $request             = $request->all();
        $request['password'] = Hash::make($request['password']);

        # Register User
        return $this->authRepository->registerUser($request);
    }

    /**
     * Function: authLogin
     * @param $request
     * @return $response
     */
    public function authLogin($request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return false;
        }

        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'username'      => $request->email,
            'password'      => $request->password,
            'scope'         => '',
        ]);

        $response = app()->handle($tokenRequest);
        $authResponse = json_decode($response->getContent(), true);

        if (! empty($authResponse)) {
            $authUser = Auth::user();

            return [
                'email'         => $authUser->email,
                'token_type'    => $authResponse['token_type'],
                'expires_in'    => $authResponse['expires_in'],
                'access_token'  => $authResponse['access_token'],
                'refresh_token' => $authResponse['refresh_token'],
            ];
        }

        return [];
    }

    /**
     * Function: userProfile
     */
    public function authUser()
    {
        return Auth::user();
    }

    /**
     * Function: userLogout
     * @return boolean
     */
    public function authLogout()
    {
        $authUser = Auth::user();
        if ($authUser) {
            $authUser->token()->revoke();
            return true;
        }
        return false;
    }

    /**
     * Function: refreshToken
     * @param object $request
     * @return array
     */
    public function refreshToken($request)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id'     => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'scope'         => '',
        ]);

        $response = app()->handle($tokenRequest);
        $authResponse = json_decode($response->getContent(), true);

        if (! empty($authResponse)) {
            return [
                'token_type'    => $authResponse['token_type'],
                'expires_in'    => $authResponse['expires_in'],
                'access_token'  => $authResponse['access_token'],
                'refresh_token' => $authResponse['refresh_token'],
            ];
        }

        return [];
    }
}
