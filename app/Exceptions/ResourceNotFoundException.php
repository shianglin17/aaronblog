<?php

namespace App\Exceptions;

/**
 * 資源不存在異常
 * 當請求的資源不存在時拋出此異常
 */
class ResourceNotFoundException extends BaseException
{
    /**
     * 建構子
     *
     * @param string $resourceName 資源名稱
     * @param int|null $resourceId 資源ID（可選）
     */
    public function __construct(string $resourceName, ?int $resourceId = null)
    {
        $message = $resourceId 
            ? "{$resourceName}（ID: {$resourceId}）不存在"
            : "{$resourceName}不存在";

        parent::__construct(
            message: $message,
            httpCode: 404,
            errorCode: 'RESOURCE_NOT_FOUND'
        );
    }
} 