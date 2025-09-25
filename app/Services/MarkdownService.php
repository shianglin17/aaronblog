<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

/**
 * Markdown 渲染服務
 *
 * 提供 CommonMark 規範的 Markdown 渲染功能
 */
class MarkdownService
{
    private CommonMarkConverter $converter;

    public function __construct()
    {
        // 使用預設的 CommonMark 轉換器
        $this->converter = new CommonMarkConverter([
            'html_input' => 'escape',           // 轉義 HTML 輸入
            'allow_unsafe_links' => false,     // 不允許不安全連結
        ]);
    }

    /**
     * 渲染 Markdown 內容為 HTML
     *
     * @param string $content Markdown 內容
     * @return string 渲染後的 HTML
     * @throws CommonMarkException
     */
    public function render(string $content): string
    {
        if (empty($content)) {
            return '';
        }

        try {
            return $this->converter->convert($content)->getContent();
        } catch (\Exception $e) {
            // 記錄錯誤但不中斷服務
            \Log::error('Markdown rendering error: ' . $e->getMessage());

            // 返回轉義後的純文字作為後備
            return '<p>' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    }

    /**
     * 從 Markdown 內容提取純文字摘要
     *
     * @param string $content Markdown 內容
     * @param int $maxLength 最大長度
     * @return string 純文字摘要
     * @throws CommonMarkException
     */
    public function extractText(string $content, int $maxLength = 160): string
    {
        if (empty($content)) {
            return '';
        }

        try {
            // 先轉換為 HTML
            $html = $this->converter->convert($content)->getContent();

            // 移除 HTML 標籤
            $text = strip_tags($html);

            // 清理多餘的空白字符
            $text = preg_replace('/\s+/', ' ', trim($text));

            if (mb_strlen($text) <= $maxLength) {
                return $text;
            }

            // 截取指定長度並確保不會截斷單字
            $truncated = mb_substr($text, 0, $maxLength);
            $lastSpace = mb_strrpos($truncated, ' ');

            if ($lastSpace !== false && $lastSpace > $maxLength * 0.8) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }

            return $truncated . '...';
        } catch (\Exception $e) {
            \Log::error('Markdown text extraction error: ' . $e->getMessage());

            // 後備方案：直接截取原始內容
            $plainText = strip_tags($content);
            return mb_strlen($plainText) > $maxLength
                ? mb_substr($plainText, 0, $maxLength) . '...'
                : $plainText;
        }
    }
}
