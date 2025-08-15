<?php

namespace App\Http\Validation;

class UserRegisterValidation
{
    /**
     * 基本註冊驗證規則
     * 
     * @return array
     */
    public static function getBaseRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * HTTP API 註冊驗證規則（包含邀請碼）
     * 
     * @return array
     */
    public static function getHttpRules(): array
    {
        return array_merge(self::getBaseRules(), [
            'invite_code' => ['required', 'string', 'max:100'],
        ]);
    }

    /**
     * CLI 註冊驗證規則
     * 
     * @return array
     */
    public static function getCliRules(): array
    {
        return self::getBaseRules();
    }

    /**
     * 驗證邀請碼是否有效
     * 
     * @param string $inviteCode
     * @return bool
     */
    public static function isValidInviteCode(string $inviteCode): bool
    {
        $validCode = env('INVITE_CODE');
        
        // 防止時序攻擊，使用 hash_equals
        return $validCode && hash_equals($validCode, $inviteCode);
    }
}