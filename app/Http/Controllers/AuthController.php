<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthPasswordRequest;
use App\Http\Requests\AuthPasswordResetRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Transformers\UserTransformer;
use App\Models\User;
use App\Services\UsersService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    /**
     * @var UsersService
     */
    private UsersService $_service;

    /**
     * @var UserTransformer
     */
    private UserTransformer $_transformer;

    /**
     * AuthController constructor.
     * @param UsersService $service
     * @param UserTransformer $transformer
     */
    public function __construct(UsersService $service, UserTransformer $transformer)
    {
        $this->_service = $service;
        $this->_transformer = $transformer;
    }

    /**
     * User sing-in
     * @param AuthLoginRequest $request
     * @return mixed
     */
    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->validated();
        /**
         * @var User
         */
        $user = User::findByEmail($credentials['email']);
        if (!Hash::check($credentials['password'], $user->getAuthPassword())) {
            return response()->errorMessage(__('Invalid credentials'), 401);
        }

        $token = $this->_service->login($user);

        return response()->success($this->_transformer->transform($user, ['token' => $token]));

    }

    /**
     * User sign-up
     * @param AuthRegisterRequest $request
     * @return mixed
     */
    public function register(AuthRegisterRequest $request)
    {
        $user = $this->_service->register($request->validated());
        $token = $this->_service->login($user);
        return response()->success($this->_transformer->transform($user, ['token' => $token]));
    }

    /**
     * Request a password reset
     * @param AuthPasswordRequest $request
     * @return mixed
     */
    public function forgotPassword(AuthPasswordRequest $request)
    {
        $data = $request->validated();
        /**
         * @var User
         */
        $user = User::findByEmail($data['email']);
        if ($user) {
            Password::sendResetLink($data);
        }

        return response()->message(__('Please check your email account for a link to reset your password.'));
    }

    /**
     * Set a new password for the user
     * @param AuthPasswordResetRequest $request
     * @return mixed
     */
    public function resetPassword(AuthPasswordResetRequest $request)
    {
        $data = $request->validated();
        $status = $this->_service->resetPassword($data);

        if ($status === Password::PASSWORD_RESET) {
            return response()->message(__('The password has been changed successfully.'));
        }

        return response()->errorMessage(__('The reset password link you have clicked has expired or is invalid. Please request a new link.'));
    }
}
