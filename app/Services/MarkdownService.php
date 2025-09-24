<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Markdown 渲染服務
 * 
 * 提供 CommonMark 規範的 Markdown 渲染功能
 * 支援 GitHub Flavored Markdown 和語法高亮
 */
class MarkdownService
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        // 建立自訂 CommonMark 環境
        $environment = new Environment([
            'html_input' => 'escape',           // 轉義 HTML 輸入
            'allow_unsafe_links' => false,     // 不允許不安全連結
            'max_nesting_level' => 10,         // 最大巢狀層級
            'heading_permalink' => [           // 標題永久連結
                'html_class' => 'heading-permalink',
                'id_prefix' => 'content',
                'apply_id_to_heading' => true,
            ],
        ]);

        // 新增擴展功能
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $environment->addExtension(new TableOfContentsExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * 渲染 Markdown 內容為 HTML
     *
     * @param string $content Markdown 內容
     * @return string 渲染後的 HTML
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

    /**
     * 從 Markdown 內容中提取標題列表
     *
     * @param string $content Markdown 內容
     * @return array 標題列表 [level => title]
     */
    public function extractHeadings(string $content): array
    {
        $headings = [];
        
        if (empty($content)) {
            return $headings;
        }

        // 使用正規表達式匹配 Markdown 標題
        if (preg_match_all('/^(#{1,6})\s+(.+)$/m', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $level = strlen($match[1]); // # 的數量
                $title = trim($match[2]);
                
                $headings[] = [
                    'level' => $level,
                    'title' => $title,
                    'slug' => $this->generateSlug($title)
                ];
            }
        }

        return $headings;
    }

    /**
     * 生成 URL slug
     *
     * @param string $text 原始文字
     * @return string URL slug
     */
    private function generateSlug(string $text): string
    {
        // 移除 HTML 標籤
        $text = strip_tags($text);
        
        // 轉為小寫並替換空白為連字號
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\u4e00-\u9fff]+/u', '-', $slug);
        
        // 清理多餘的連字號
        $slug = trim($slug, '-');
        $slug = preg_replace('/-+/', '-', $slug);
        
        return $slug;
    }
}