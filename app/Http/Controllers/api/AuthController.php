<?php

namespace App\Http\Controllers\api;

use App\Helper\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLogin;
use App\Http\Requests\AuthRegister;
use App\Http\Requests\RefreshToken;
use App\Services\AuthServices;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{

    protected $authServices;

    public function __construct(AuthServices $authServices)
    {
        $this->authServices = $authServices;
    }

    /**
     * Function: register
     * @param App\Http\Requests\AuthRegister $request
     * @return Illuminate\Http\JsonResponse
     */

    public function register(AuthRegister $request)
    {
        try {
            $response = $this->authServices->authRegister($request);
            if ($response) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $response, statusCode: self::SUCCESS);
            }
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::FAILED_MESSAGE, statusCode: self::ERROR);
        } catch (Exception $e) {
            Log::error('Exception occured while registering user' . $e->getMessage());
            return ApiResponse::success(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    /**
     * Function: login
     * @param App\Http\Requests\AuthLogin $request
     * @return Illuminate\Http\JsonResponse
     */
    public function login(AuthLogin $request)
    {
        try {
            $response = $this->authServices->authLogin($request);
            if ($response) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $response, statusCode: self::SUCCESS);
            }
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::INVALID_CREDENTIALS, statusCode: self::UNAUTHORIZED);
        } catch (Exception $e) {
            Log::error('Exception occured while logging in user' . $e->getMessage());
            return ApiResponse::success(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    /**
     * Function: userProfile
     * @return Illuminate\Http\JsonResponse
     */
    public function getAutUser()
    {
        try {
            $user = $this->authServices->authUser();
            if ($user) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $user, statusCode: self::SUCCESS);
            }
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::USER_NOT_FOUND, statusCode: self::NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Exception occured while fetching user profile' . $e->getMessage());
            return ApiResponse::success(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    /**
     * Function: logout
     * @param NA
     * @return Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $response = $this->authServices->authLogout();
            if ($response) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, statusCode: self::SUCCESS);
            }
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::USER_NOT_FOUND, statusCode: self::NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Exception occured while logging out user' . $e->getMessage());
            return ApiResponse::success(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }

    /**
     * Function: refreshToken
     * @param Illuminate\Http\Requests\RefreshTokenRequest
     * @return Illuminate\Http\JsonResponse
     */
    public function refreshToken(RefreshToken $request)
    {
        try {
            $response = $this->authServices->refreshToken($request);
            if ($response) {
                return ApiResponse::success(status: self::SUCCESS_STATUS, message: self::SUCCESS_MESSAGE, data: $response, statusCode: self::SUCCESS);
            }
            return ApiResponse::error(status: self::ERROR_STATUS, message: self::INVALID_CREDENTIALS, statusCode: self::UNAUTHORIZED);
        } catch (Exception $e) {
            Log::error('Exception occured while refreshing token' . $e->getMessage());
            return ApiResponse::success(status: self::ERROR_STATUS, message: self::EXCEPTION_MESSAGE, statusCode: self::ERROR);
        }
    }
}
