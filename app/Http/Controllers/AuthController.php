<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Str;


class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try{
            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->apiResponse('Wrong email or password', [], [], [], 401);
            }
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            return $this->apiResponse('Record created', ['token' => $token], new UserResource(Auth::user()), [], 200);
        }catch (\Throwable $th) {
            return $this->apiResponse("Error while logging in", [], [], [], 422);
        }
    }

    public function logout()
    {
        try{
            Auth::user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return $this->apiResponse('Successfully logged out', [], [], [], 200);
        }catch (\Throwable $th) {
            return $this->apiResponse("Error while logging out", [], [], [], 422);
        }
    }

    public function create(UserRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'is_admin' => $request['is_admin']=='true'?true:false,
            ]);
            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->apiResponse('Record not authenticated', [], [], [], 401);
            }

            $token = Auth::user()->createToken('auth_token')->plainTextToken;
            return $this->apiResponse('Record created', ['token' => $token], new UserResource(Auth::user()), [], 201);
        }catch (\Throwable $th) {
            return $th;
            return $this->apiResponse("Error while creating user", [], [], [], 422);
        }
    }

}
