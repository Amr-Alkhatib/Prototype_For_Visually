<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'tum_id' => 'nullable|string|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tum_id' => $request->tum_id,
                'is_tum_verified' => false,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }

    /**
     * Verify with TUM Online API
     */
    public function verifyTumOnline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tum_id' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->verifyWithTumOnlineApi(
                $request->tum_id,
                $request->password
            );

            if ($response['success']) {
                $user = $request->user();
                $user->update([
                    'tum_id' => $request->tum_id,
                    'is_tum_verified' => true,
                ]);

                return response()->json([
                    'message' => 'TUM verification successful',
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'error' => 'TUM verification failed'
            ], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify credentials with TUM Online API
     */
    private function verifyWithTumOnlineApi($tumId, $password)
    {
        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->post(config('services.tum_online.api_url') . '/authenticate', [
                'json' => [
                    'client_id' => config('services.tum_online.client_id'),
                    'client_secret' => config('services.tum_online.client_secret'),
                    'username' => $tumId,
                    'password' => $password,
                ]
            ]);

            return [
                'success' => $response->getStatusCode() === 200,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
