<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::query()->create($validated);

        $token = $user->createToken('main')->plainTextToken;

        $data['user'] = new UserResource($user);
        $data['token'] = $token;

        return $this->sendResponse($data, 'User Registered Successfully');
    } // end of register

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $remember = $validated['remember'] ?? false;

        unset($validated['remember']);

        if (!Auth::attempt($validated, $remember)) {
            return $this->sendError(
                'this credentials doesn\'t match our records',
                'Auth failed',
                422
            );
        }

        $user = auth()->user();
        $token = $user->createToken('main')->plainTextToken;

        $data['user'] = new UserResource($user);
        $data['token'] = $token;

        return $this->sendResponse($data, 'User Logged in Successfully');
    } // end of login

    public function logout()
    {
        $user = auth()->user();

        $user->currentAccessToken()->delete();

        $data['user'] = $user;

        return $this->sendResponse($data, 'User Logged out Successfully');
    } // end of logout
}
