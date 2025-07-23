<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,clientHr,clientEmployee',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Mulai transaction
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            // email verification
            event(new Registered($user));

            // commit if all data inputed is success
            DB::commit();

            return response()->json([
                'message' => 'Registration successful. Please verify your email.',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {

            // Rollback
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // validation input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Data Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Ups Your Email or Password is wrong!'
            ], 401);
        }

        $user = auth()->user(); // Atau JWTAuth::user();

        return response()->json([
            'status' => true,
            'message' => 'Login Successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function me() { return response()->json(auth('api')->user()); }

    public function logout() {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
