<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Response\ResponseMaker;

/**
 * 基礎異常類別
 * 所有業務異常都應該繼承此類別
 */
abstract class BaseException extends Exception
{
    /** Http 狀態碼 @var int */
    protected int $httpCode;

    /** 錯誤代碼 @var string */
    protected string $errorCode;

    /** 錯誤訊息 @var string */
    protected $message;

    /**
     * 建構子
     *
     * @param string $message 錯誤訊息
     * @param int $httpCode HTTP 狀態碼
     * @param string $errorCode 錯誤代碼
     */
    public function __construct(string $message, int $httpCode = 400, string $errorCode = 'BUSINESS_ERROR')
    {
        parent::__construct($message);
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->errorCode = $errorCode;
    }

    /**
     * 取得 HTTP 狀態碼
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * 取得錯誤代碼
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * 轉換為 JSON 回應
     *
     * @return JsonResponse
     */
    public function toJSONResponse(): JsonResponse
    {
        return ResponseMaker::error(
            message: $this->getMessage(),
            code: $this->getHttpCode(),
            meta: ['error_code' => $this->getErrorCode()]
        );
    }
}
