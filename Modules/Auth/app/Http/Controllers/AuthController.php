<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\loginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Jobs\LoginJob;
use Modules\Auth\Jobs\RegisterJob;
use Modules\Auth\Repository\AuthRepository;
use Modules\Auth\Transformers\UserInfoResource;
use Modules\Otp\Http\Requests\RegisterVerificationOtpRequest;
use Modules\Otp\Http\Requests\ResendOtpRequest;
use Modules\Otp\Models\Otp;

class AuthController extends Controller
{
    private $authRepo;
    // php artisan queue:work

    public function __construct(AuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function Register(RegisterRequest $request)
    {

        $user = $this->authRepo->register($request);
        if ($user) {
            RegisterJob::dispatch($user);

            return response()->json(['message' => 'Registration successful, please check your email for verification code.'], 201);
        }

        return response()->json(['message' => __('messages.user.auth.register.failed')], 404);
    }

    public function checkingTwoFactorLogin(Request $request)
    {
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->email)->first();

        if (is_null($request->username) && is_null($request->password)) {
            return response()->json(['message' => 'نام کاربری و رمز عبور وارد نشده است'], 400);
        }

        // if (is_null($request->username)) {
        //     return response()->json(['message' => 'نام کاربری وارد نشده است'], 400);
        // }

        if (is_null($request->password)) {
            return response()->json(['message' => 'رمز عبور وارد نشده است'], 400);
        }

        if (! $user) {
            return response()->json(['message' => 'کاربر یافت نشد'], 404);
        }

        if ($user->twofactor == false) {
            return $this->authRepo->login($request);
        }

        if ($user->twofactor == 1) {
            $twoFactorResponse = $this->authRepo->TwoFactorLoginEmail($request);
            if ($twoFactorResponse) {
                LoginJob::dispatch($user);

                return response()->json(['message' => 'Login successful, please check your email for verification code.'], 201);
            } else {
                return response()->json(['message' => 'Failed to initiate two-factor authentication.'], 500);
            }
        }
    }

    public function login(LoginRequest $request)
    {
        $accessToken = $this->authRepo->login($request);
        if ($accessToken) {
            return response()->json([
                'message' => __('messages.user.auth.login.success'),
                // 'token_name' => $accessToken['token_name'],
                '__token__' => $accessToken['__token__'],
            ], 200);
        }

        return response()->json(['message' => __('messages.user.auth.login.failed')], 403);
    }

    public function verify_twofactor_code(RegisterVerificationOtpRequest $request)
    {
        $code = $request->code;
        $otpRecord = Otp::where('otp', $code)->first();

        if (! $otpRecord) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($otpRecord->expire_time < Carbon::now()) {
            return response()->json(['message' => 'کد منقضی شده است. لطفاً کد جدید درخواست کنید.'], 410);
        }

        $user = User::find($otpRecord->user_id);

        if (! $user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($code != $otpRecord->otp) {
            return response()->json(['error' => 'code is incorrect.'], 404);

        }
        $accessToken = $this->authRepo->TwoFactorLogin($request);
        if ($accessToken) {
            return response()->json([
                'message' => __('messages.user.auth.login.success'),
                // 'token_name' => $accessToken['token_name'],
                '__token__' => $accessToken['__token__'],
            ], 200);
        }

        return response()->json(['message' => 'کد نامعتبراست'], 404);
    }

    public function verify_otp_code(RegisterVerificationOtpRequest $request)
    {
        $code = $request->code;
        $otpRecord = Otp::where('otp', $code)->first();

        if (! $otpRecord) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($otpRecord->expire_time < Carbon::now()) {
            return response()->json(['message' => 'کد منقضی شده است. لطفاً کد جدید درخواست کنید.'], 410);
        }

        $user = User::find($otpRecord->user_id);

        if (! $user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($code === $otpRecord->otp) {
            $user->update(['email_verified_at' => Carbon::now()]);
            $otpRecord->delete();

            return response()->json(['message' => __('code.verified.successfully.')], 200);
        }

        return response()->json(['message' => 'کد نامعتبراست'], 404);
    }

    public function ResendCode(ResendOtpRequest $request)
    {

        $error = $this->authRepo->ResendCode($request);

        if ($error === null) {
            return response()->json(['message' => 'messages.Code.Resend.successful'], 200);
        }

        return response()->json(['message' => __('messages.Code.Resend.failed')], 404);
    }

    public function logout()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        $error = $this->authRepo->logout();
        if ($error === null) {
            return response()->json(['message' => __('messages.user.auth.logout.success')], 200);
        }

        return response()->json(['message' => __('messages.user.auth.logout.failed')], 403);
    }

    public function user()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        return new UserInfoResource($user);

    }
}
