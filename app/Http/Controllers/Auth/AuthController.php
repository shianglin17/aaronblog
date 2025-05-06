<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
         // 驗證請求已在 LoginRequest 中完成
         $credentials = $request->validated();

         // 嘗試登入
         if (!Auth::attempt($credentials)) {
             // 登入失敗
             throw ValidationException::withMessages([
                 'email' => ['提供的憑證不正確。'],
             ]);
         }
 
         // 登入成功，取得用戶
         $user = Auth::user();
         
         // 刪除所有現有的 token
         // 這裡的 tokens() 來自 HasApiTokens Trait 的方法，在 User 有引用
         $user->tokens()->delete();
         
         // 建立新的 token
         $token = $user->createToken('auth-token')->plainTextToken;
         
         // 回傳成功訊息、用戶資料和 token
         return response()->json([
             'message' => '登入成功',
             'user' => $user,
             'token' => $token
         ]);
    }

    /**
     * 處理登出請求
     */
    public function logout(Request $request)
    {
        // 刪除當前用戶的 token
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => '登出成功'
        ]);
    }

    /**
     * 獲取當前認證用戶資訊
     */
    public function user(Request $request)
    {
        // 獲取當前用戶
        $user = $request->user();
        
        // 回傳用戶資料
        return response()->json([
            'user' => $user
        ]);
    }
}
