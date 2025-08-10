<?php

namespace App\Exceptions;

use App\Http\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * API 異常基礎類別
 * 
 * 簡潔的異常處理，專注於部落格 API 需求
 */
class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 400
    ) {
        parent::__construct($message);
    }

    /**
     * 轉換為 API 回應
     */
    public function toResponse(): JsonResponse
    {
        return ApiResponse::error($this->getMessage(), $this->statusCode);
    }
}