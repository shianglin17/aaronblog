<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Response\ResponseMaker;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
         return ResponseMaker::success([
            'user' => $user
         ], message: '登入成功');
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

        return ResponseMaker::success(message: '登出成功');
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
        return ResponseMaker::success($user, message: '取得用戶資料成功');
    }

    /**
     * 註冊新用戶
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function register(string $name, string $email, string $password): array
    {
        // 驗證 email 格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'email' => ['Email 格式不正確。'],
            ]);
        }

        // 檢查 email 是否已存在
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email 已被註冊。'],
            ]);
        }

        // 驗證密碼長度
        if (strlen($password) < 6) {
            throw ValidationException::withMessages([
                'password' => ['密碼長度至少 6 碼。'],
            ]);
        }

        // 建立新用戶
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return [
            'user' => $user,
        ];
    }
}
