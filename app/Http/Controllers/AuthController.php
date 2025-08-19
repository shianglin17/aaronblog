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
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    /**
     * 用戶登入
     * 
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="login",
     *     tags={"Authentication"},
     *     summary="用戶登入",
     *     description="使用電子郵件和密碼進行登入，成功後建立 Session",
     *     security={{"csrfToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="登入憑證",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="電子郵件地址"),
     *             @OA\Property(property="password", type="string", example="password123", description="密碼"),
     *             @OA\Property(property="remember", type="boolean", example=false, description="是否記住登入狀態（可選，預設為 false）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="登入成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Aaron"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="登入失敗或驗證錯誤",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="提供的憑證不正確。"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="請求過於頻繁",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=429),
     *             @OA\Property(property="message", type="string", example="Too Many Requests")
     *         )
     *     )
     * )
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
         // 驗證請求已在 LoginRequest 中完成
         $validated = $request->validated();
         
         // 提取認證憑證（移除 remember 參數）
         $credentials = [
             'email' => $validated['email'],
             'password' => $validated['password'],
         ];
         
         // 動態處理記住我功能
         $remember = $validated['remember'] ?? false;

         if (!Auth::guard('web')->attempt($credentials, $remember)) {
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
     * 用戶登出
     * 
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     summary="用戶登出",
     *     description="登出當前用戶，清除 Session（保持 CSRF token 不變以避免 SPA 中的同步問題）",
     *     security={{"sessionAuth": {}, "csrfToken": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="登出成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="未授權",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
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

        // 注意：不重新產生 CSRF token，避免前端 SPA 中的 token 不同步問題
        // 用戶已登出且 session 已失效，重新產生 CSRF token 的安全價值很低

        return ApiResponse::ok();
    }

    /**
     * 獲取當前用戶資訊
     * 
     * @OA\Get(
     *     path="/api/auth/user",
     *     operationId="getCurrentUser",
     *     tags={"Authentication"},
     *     summary="獲取當前用戶資訊",
     *     description="獲取當前已認證用戶的基本資訊",
     *     security={{"sessionAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="成功獲取用戶資訊",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Aaron"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="未授權",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
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
     * 用戶註冊
     * 
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="register",
     *     tags={"Authentication"},
     *     summary="用戶註冊",
     *     description="註冊新用戶帳號，需要邀請碼",
     *     security={{"csrfToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="註冊資料",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "invite_code"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Aaron", description="用戶姓名"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="user@example.com", description="電子郵件地址（唯一）"),
     *             @OA\Property(property="password", type="string", minLength=8, maxLength=255, example="password123", description="密碼（至少8字元）"),
     *             @OA\Property(property="invite_code", type="string", maxLength=100, example="INVITE2024", description="邀請碼")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="註冊成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Aaron"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T14:30:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="驗證失敗",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="電子郵件已被使用")),
     *                 @OA\Property(property="invite_code", type="array", @OA\Items(type="string", example="邀請碼無效。"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="請求過於頻繁",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="code", type="integer", example=429),
     *             @OA\Property(property="message", type="string", example="Too Many Requests")
     *         )
     *     )
     * )
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
