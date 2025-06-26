<?php

namespace App\Exceptions;

use Exception;

/**
 * 資源正在使用中例外
 * 
 * 當嘗試刪除正在被其他資源引用的資源時拋出此例外
 */
class ResourceInUseException extends BaseException
{
    /**
     * 建立新的例外實例
     *
     * @param string $resourceType 資源類型（如：分類、標籤）
     * @param int $resourceId 資源ID
     * @param string $usedBy 使用該資源的資源類型（如：文章）
     * @param int $usageCount 使用數量
     */
    public function __construct(
        string $resourceType,
        int $resourceId,
        string $usedBy,
        int $usageCount
    ) {
        $message = "無法刪除 {$resourceType}（ID: {$resourceId}），因為仍有 {$usageCount} 篇{$usedBy}正在使用此{$resourceType}";
        
        parent::__construct($message, 409, 'RESOURCE_CONSTRAIN');
    }
} 