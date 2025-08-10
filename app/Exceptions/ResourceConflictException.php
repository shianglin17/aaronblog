<?php

namespace App\Exceptions;

use App\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * 資源衝突異常
 * 
 * 當嘗試刪除被其他資源使用的資源時拋出
 */
class ResourceConflictException extends ApiException
{
    public function __construct(string $resourceType, int $resourceId, int $usageCount)
    {
        $message = "無法刪除 {$resourceType}（ID: {$resourceId}），因為仍有 {$usageCount} 篇文章正在使用此{$resourceType}";
        parent::__construct($message, 409);
    }

    public function toResponse(): JsonResponse
    {
        return ApiResponse::conflict($this->getMessage());
    }
}