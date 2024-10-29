<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|max:15|unique:users',
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User registration failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {

        $request->headers->set('Accept', 'application/json');

        $credentials = $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'password' => 'required|string|min:4',
        ]);

        try {
            $user = User::where('email', $request->email)
                ->orWhere('phone', $request->phone)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials
                    '
                ], 401);
            }

            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while logging in. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function getUser(Request $request)
    {

        try {
            // Check if token is passed in the Authorization header
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization token not provided.',
                ], 401);
            }

            // Validate token and retrieve the user associated with it
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving user data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
