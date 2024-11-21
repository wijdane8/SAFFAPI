<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): Response
    {
        // validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|max:255'
        ]);

        // create user
        $user = $this->authService->register($request);

        // create access token
        $token = $user->createToken('auth')->plainTextToken;

        // return
        return response([
            'message' => __('app.registration_success_verify'),
            'results' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ], 201);
    }

    public function login(Request $request)
{
    // Validate request
    $validatedData = $request->validate([
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6|max:255',
    ]);

    // Attempt login
    if (!Auth::attempt($validatedData)) {
        return response()->json([
            'message' => __('auth.failed'), // Translatable error message
        ], 401);
    }

    // Get authenticated user
    $user = Auth::user();

    // Generate API token
    $token = $user->createToken('authToken')->plainTextToken;

    // Check if the user is verified (optional)
    $emailVerificationMessage = $user->email_verified_at 
        ? __('app.login_success') 
        : __('app.login_success_verify'); // Supports localization for different statuses

    // Return response
    return response()->json([
        'message' => $emailVerificationMessage,
        'user' => $user, // You can wrap this in a UserResource if needed
        'token' => $token,
    ], 200);
}



    public function logout(Request $request): Response
    {
        // logout
        $request->user()->currentAccessToken()->delete();

        // return
        return response(['message' => __('app.logout_success')]);
    }

    public function otp(Request $request): Response
    {
        // get user
        $user = auth()->user();

        // generate otp
        $otp = $this->authService->otp($user);

        // return
        return response([
            'message' => __('app.otp_sent_success')
        ]);
    }

    public function verify(Request $request): Response
    {
        // validate the request
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // get user
        $user = auth()->user();

        // verify otp
        $user = $this->authService->verify($user, $request);

        // return
        return response([
            'message' => __('app.verification_success'),
            'results' => [
                'user' => new UserResource($user)
            ]
        ]);
    }

    public function resetOtp(Request $request): Response
    {
        // validate request
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email'
        ]);

        // get user
        $user = $this->authService->getUserByEmail($request->email);

        // generate otp
        $otp = $this->authService->otp($user, 'password-reset');

        // return
        return response([
            'message' => __('app.otp_sent_success')
        ]);
    }

    public function resetPassword(Request $request): Response
    {
        // validate request
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|max:255|confirmed',
            'password_confirmation' => 'required|min:6|max:255'
        ]);

        // get user
        $user = $this->authService->getUserByEmail($request->email);

        // reset password
        $user = $this->authService->resetPassword($user, $request);

        // return
        return response([
            'message' => __('app.password_reset_success')
        ]);
    }
}
