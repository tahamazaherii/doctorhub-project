<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('myApp')->plainTextToken;

        return response()->json([ 'user' => $user,'token' => $token], 201);


    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('user not found', 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('password is incorrect', 401);
        }

        $token = $user->createToken('myApp')->plainTextToken;


        return response()->json([ 'user' => $user,'token' => $token], 200);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([ 'message' => 'logged out'], 200);
    }
}
