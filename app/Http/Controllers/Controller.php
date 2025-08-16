<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="Aaron Blog API 文檔",
 *     version="1.0.0",
 *     description="Aaron 個人博客系統的 API 文檔，提供文章、分類、標籤等功能的 RESTful API 介面。",
 *     @OA\Contact(
 *         email="admin@aaronblog.com",
 *         name="Aaron Blog 技術支援"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Aaron Blog API 伺服器"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sessionAuth",
 *     type="apiKey",
 *     in="cookie",
 *     name="laravel_session",
 *     description="使用 Laravel Session Cookie 進行身份驗證"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="csrfToken",
 *     type="apiKey",
 *     in="header", 
 *     name="X-XSRF-TOKEN",
 *     description="CSRF 防護 Token，用於 POST/PUT/DELETE 等寫入操作"
 * )
 * 
 * @OA\Tag(
 *     name="Articles",
 *     description="文章相關 API"
 * )
 * 
 * @OA\Tag(
 *     name="Categories", 
 *     description="分類相關 API"
 * )
 * 
 * @OA\Tag(
 *     name="Tags",
 *     description="標籤相關 API"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="身份驗證相關 API"
 * )
 * 
 * @OA\Tag(
 *     name="Admin",
 *     description="後台管理相關 API（需要認證）"
 * )
 */
abstract class Controller
{
    //
}
