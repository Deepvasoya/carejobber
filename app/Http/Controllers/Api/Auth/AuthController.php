<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Models\Company;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * User Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'user_type' => 'required|in:seeker,company',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $userType = $request->user_type;

        // Authenticate based on user type
        if ($userType === 'seeker') {
            // Authenticate against users table
            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::guard('web')->user();
                
                // Generate plain API token
                $token = Str::random(60);

                // Save hashed version to DB for security
                $user->forceFill([
                    'api_token' => hash('sha256', $token),
                ])->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'user_type' => 'seeker',
                    ]
                ]);
            }
        } elseif ($userType === 'company') {
            // Authenticate against companies table
            if (Auth::guard('company')->attempt($credentials)) {
                $company = Auth::guard('company')->user();
                
                // Generate API token for company (consistent with seeker)
                $token = Str::random(60);
                
                // Save hashed version to DB for security
                $company->forceFill([
                    'api_token' => hash('sha256', $token),
                ])->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Company login successful',
                    'token' => $token,
                    'user' => [
                        'id' => $company->id,
                        'name' => $company->name,
                        'email' => $company->email,
                        'user_type' => 'company',
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * User Registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if email already exists in users table
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already registered'
            ], 422);
        }

        // Generate 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(30); // Code expires in 30 minutes

        // Create user with verification code (initially inactive and unverified)
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => $expiresAt,
            'is_email_verified' => 0,
            'email_verification_attempts' => 0,
            'is_active' => 0,  // Initially inactive until email verification
            'verified' => 0,   // Initially unverified until email verification
        ]);

        // Send verification email
        try {
            Mail::send('emails.verification-code', [
                'name' => $user->name,
                'verification_code' => $verificationCode,
                'expires_at' => $expiresAt->format('M d, Y H:i')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Email Verification Code - Jobs Portal');
            });

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email for verification code.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_email_verified' => false,
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log the error but don't delete the user for testing
            \Log::error('Email sending failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email for verification code.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_email_verified' => false,
                ]
            ], 201);
        }
    }

    /**
     * User Logout
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            // Clear the API token
            if ($user) {
                $user->update(['api_token' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change Password
     */
    public function change_password(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check old password
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Check if new password is same as old
            if (Hash::check($request->new_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'New password must be different from current password'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot Password
     */
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Password::sendResetLink($request->only('email'));

            if ($response == Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send reset link'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Email Code
     */
    public function verifyEmailCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if already verified
        if ($user->is_email_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        // Check verification attempts (max 5 attempts)
        if ($user->email_verification_attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Too many verification attempts. Please request a new code.'
            ], 429);
        }

        // Check if code matches and is not expired
        if ($user->email_verification_code !== $request->verification_code) {
            $user->increment('email_verification_attempts');
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        if (Carbon::now()->gt($user->email_verification_code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired'
            ], 400);
        }

        // Verify the user and set as active
        $user->update([
            'is_email_verified' => 1,
            'email_verified_at' => Carbon::now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
            'email_verification_attempts' => 0,
            'is_active' => 1,  // Set user as active
            'verified' => 1,   // Set user as verified
        ]);

        // Generate API token for login
        $token = Str::random(60);
        $user->update(['api_token' => hash('sha256', $token)]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_email_verified' => true,
            ]
        ]);
    }

    /**
     * Resend Verification Code
     */
    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if already verified
        if ($user->is_email_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(30);

        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => $expiresAt,
            'email_verification_attempts' => 0,
        ]);

        // Send verification email
        try {
            Mail::send('emails.verification-code', [
                'name' => $user->name,
                'verification_code' => $verificationCode,
                'expires_at' => $expiresAt->format('M d, Y H:i')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Email Verification Code - Jobs Portal');
            });

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh Token
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            
            // Generate new API token
            $token = Str::random(60);
            
            // Update user's API token
            $user->update(['api_token' => hash('sha256', $token)]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type ?? 'jobseeker',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot Password - Send Reset Code
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found'
            ], 404);
        }

        // Generate 6-digit code
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(30);

        // Store reset code
        $user->update([
            'password_reset_code' => $resetCode,
            'password_reset_code_expires_at' => $expiresAt
        ]);

        // Send email
        try {
            Mail::send('emails.password-reset-code', [
                'name' => $user->name,
                'code' => $resetCode,
                'expires_at' => $expiresAt->format('M d, Y H:i')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Password Reset Code - Jobs Portal');
            });

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Reset Code
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found'
            ], 404);
        }

        if (!$user->password_reset_code || 
            $user->password_reset_code !== $request->reset_code ||
            !$user->password_reset_code_expires_at ||
            Carbon::now()->gt($user->password_reset_code_expires_at)) {
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully'
        ]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found'
            ], 404);
        }

        // Verify code again
        if (!$user->password_reset_code || 
            $user->password_reset_code !== $request->reset_code ||
            !$user->password_reset_code_expires_at ||
            Carbon::now()->gt($user->password_reset_code_expires_at)) {
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }

        // Update password and clear reset code
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_code' => null,
            'password_reset_code_expires_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}