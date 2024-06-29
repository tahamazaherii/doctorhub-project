<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



/**
 * @OA\Info(title="Laravel Swagger API", version="1.0")
 * * @OA\SecurityScheme(

* type="http",

* securityScheme="bearerAuth",

* scheme="bearer",

* bearerFormat="JWT"

* )
*/
class AuthController extends Controller
{
    use ApiResponser;
        /**
        * @OA\Post(
        * path="/api/register",
        * operationId="Register",
        * tags={"auth"},
        * summary="User Register",
        * description="User Register here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"name","email", "password"},
        *               @OA\Property(property="name", type="text", example="ali"  , description="enter your name"),
        *               @OA\Property(property="email", type="email" , example="ali@gmail"  , description="enter your email"),
        *               @OA\Property(property="password", type="password" , example="123456789"  , description="enter your password"),
        *
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function register(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            return $this->errorResponse('before you register plese go to login', 401);
        }

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

    /**
        * @OA\Post(
        * path="/api/login",
        * operationId="authLogin",
        * tags={"auth"},
        * summary="User Login",
        * description="Login User Here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email", "password"},
        *               @OA\Property(property="email", type="email", example="ali@gmail"  , description="enter your email"),
        *               @OA\Property(property="password", type="password" , example="123456789"  , description="enter your password")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */

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



      /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *      tags={"auth"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     *
     * )
     */

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([ 'message' => 'logged out'], 200);
    }
}
