<?php

namespace App\Http\Requests\Auth;

use App\Http\Validation\UserRegisterValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return UserRegisterValidation::getHttpRules();
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $inviteCode = $this->input('invite_code');
            
            if ($inviteCode && !UserRegisterValidation::isValidInviteCode($inviteCode)) {
                $validator->errors()->add('invite_code', '邀請碼無效。');
            }
        });
    }
}