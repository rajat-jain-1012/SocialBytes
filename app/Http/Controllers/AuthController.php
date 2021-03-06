<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRegistrationRequest;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $userData = $request->all();
        $userData['password'] = bcrypt($request->password);
        $user = Users::create($userData);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $user = Users::where('email', $request->email)->first();
        if (empty($user)) {
            return response()->json(['message' => "The given user doesn't exist"]);
        }
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['token' => $token];
            return response($response, 200);
        }
        return response()->json(['message' => 'The given password is incorrect']);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'You have been successfully logged out']);
    }
}
