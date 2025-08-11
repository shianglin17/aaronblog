<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Validation\UserRegisterValidation;
use App\Http\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 登入
     * 
     * 實現流程：
     * 1. 驗證登入資料（email 和 password）
     * 2. 使用 web guard 嘗試認證用戶
     * 3. 若認證失敗，回傳錯誤訊息
     * 4. 若認證成功，重新產生 session ID 防止 session fixation 攻擊
     * 5. 回傳用戶資料（不再回傳 token）
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
         // 驗證請求已在 LoginRequest 中完成
         $credentials = $request->validated();

         if (!Auth::guard('web')->attempt($credentials, true)) {
             // 登入失敗
             throw ValidationException::withMessages([
                 'email' => ['提供的憑證不正確。'],
             ]);
         }

         // 登入成功，重新產生 session ID 防止 session fixation 攻擊
         $request->session()->regenerate();

         // 取得認證用戶
         /** @var User $user */
         $user = Auth::guard('web')->user();

         // 回傳成功訊息和用戶資料（不回傳 token）
         return ApiResponse::ok([
            'user' => $user
         ]);
    }

    /**
     * 處理登出請求
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // 登出當前用戶的 session
        Auth::guard('web')->logout();

        // 清除 session 資料
        $request->session()->invalidate();

        // 重新產生 CSRF token
        $request->session()->regenerateToken();

        return ApiResponse::ok();
    }

    /**
     * 獲取當前認證用戶資訊
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        // 獲取當前用戶
        /** @var User $user */
        $user = $request->user();

        // 回傳用戶資料
        return ApiResponse::ok($user);
    }

    /**
     * HTTP API 註冊新用戶
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return ApiResponse::ok(['user' => $user]);
    }

    /**
     * CLI 註冊新用戶
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function registerFromCli(string $name, string $email, string $password): array
    {
        $validator = Validator::make(
            compact('name', 'email', 'password'),
            UserRegisterValidation::getCliRules()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return ['user' => $user];
    }
}
