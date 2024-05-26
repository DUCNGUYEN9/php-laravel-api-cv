<?php

namespace App\Http\Controllers\Api\V1\AuthControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    /**
     * Register
     * @param $request input info register
     */
    public function register(Request $request)
    {
        try {
            //register new
            $ValidateUser = Validator::make($request->all(), [
                'name' => 'required|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
                'role' => 'required',
            ]);
            /**
             * check validate
             * Validation error code - 422
             */
            if ($ValidateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $ValidateUser->errors()
                ], 422);
            }
            /**
             * create user new
             * success - 200
             */

            $User = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
            ]);
            return response()->json([
                'user_id' => $User->id,
                'role' => $User->role,
                'status' => true,
                'message' => 'User created successfully',
                'token' => $User->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    //register end
    /**
     * Login
     * @param $request : email & password
     * Unauthorized error code - 401
     * Validation error code - 422
     */
    public function login(Request $request)
    {
        try {
            $ValidateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($ValidateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'error' => $ValidateUser->errors()
                ], 401);
            }
            //check Email & password:
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password not correct !',
                ], 422);
            }
            /**
             * - handle user login
             * - check role
             */
            $User = User::where('email', $request->email)->first();
            $role = $User->role;
            switch ($role) {
                case 'candidate':
                    return response()->json([
                        'user_id' => $User->id,
                        'role' => $User->role,
                        'status' => true,
                        'message' => 'User logged in successfully',
                        'token' => $User->createToken("API TOKEN")->plainTextToken
                    ], 200);
                case 'company':
                    return response()->json([
                        'user_id' => $User->id,
                        'role' => $User->role,
                        'status' => true,
                        'message' => 'User logged in successfully',
                        'token' => $User->createToken("API TOKEN")->plainTextToken
                    ], 200);
                default:
                    return response()->json(['error' => 'Unknown role !'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' =>  $th->getMessage(),
            ], 500);
        }
    }
    //login end
    /**
     * Logout
     *
     */
    public function logout()
    {
        $user = Auth::user();
        if ($user instanceof user) {
            /**
             * Revoke all tokens for the user
             */
            $user->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'User logged out',
                'data' => [],
            ], 200);
        } else {
            /**
             * Handle Error: Not logged in or guard did not return a User object.
             */
            return response()->json([
                'status' => false,
                'message' => 'Error logging out: User not logged in',
            ], 401);
        }
    }
    //logout end

}
