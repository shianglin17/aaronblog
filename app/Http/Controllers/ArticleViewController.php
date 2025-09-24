<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleService;
use App\Services\MarkdownService;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * 文章 SSR 視圖控制器
 *
 * 處理文章詳情頁面的伺服器端渲染 (SSR)
 * 提供完整的 SEO 優化和結構化資料
 */
class ArticleViewController extends Controller
{
    public function __construct(
        protected readonly ArticleService $articleService,
        protected readonly MarkdownService $markdownService
    ) {}

    /**
     * 顯示單篇文章詳情頁面 (SSR)
     *
     * @param string $slug 文章 slug
     * @return View
     */
    public function show(string $slug): View
    {
        try {
            // 通過 slug 查找文章
            $article = $this->findArticleBySlug($slug);

            // 渲染 Markdown 內容
            $renderedContent = $this->markdownService->render($article->content);

            // 生成 SEO 資料
            $seoData = $this->generateSeoData($article);

            // 生成結構化資料 (JSON-LD)
            $structuredData = $this->generateStructuredData($article);

            return view('articles.show', [
                'article' => $article,
                'renderedContent' => $renderedContent,
                'seoData' => $seoData,
                'structuredData' => $structuredData,
                'pageTitle' => $seoData['title'],
            ]);

        } catch (ModelNotFoundException $e) {
            // 文章不存在，返回 404
            abort(404, '文章不存在');
        } catch (\Exception $e) {
            // 記錄錯誤並返回 500
            \Log::error('Article SSR Error: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, '頁面載入失敗，請稍後再試');
        }
    }

    /**
     * 通過 slug 查找文章
     *
     * @param string $slug 文章 slug
     * @return Article
     * @throws ModelNotFoundException
     */
    private function findArticleBySlug(string $slug): Article
    {
        return Article::where('slug', $slug)
            ->where('status', Article::STATUS_PUBLISHED)
            ->with(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug'])
            ->firstOrFail();
    }

    /**
     * 生成 SEO meta 資料
     *
     * @param Article $article
     * @return array
     */
    private function generateSeoData(Article $article): array
    {
        // 從 Markdown 內容提取純文字描述
        $description = $article->description ?: $this->markdownService->extractText($article->content, 160);

        // 生成關鍵字
        $keywords = $this->generateKeywords($article);

        // 文章特色圖片 URL（目前專案沒有圖片，先預留）
        $featuredImage = $this->getFeaturedImageUrl($article);

        return [
            'title' => $article->title . ' - Aaron 的部落格',
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => url()->current(),
            'author' => $article->author->name,
            'published_time' => $article->created_at->toISOString(),
            'modified_time' => $article->updated_at->toISOString(),
            'article_section' => $article->category->name,
            'article_tag' => $article->tags->pluck('name')->toArray(),
            'featured_image' => $featuredImage,
            'site_name' => config('app.name', 'Aaron 的部落格'),
        ];
    }

    /**
     * 生成結構化資料 (JSON-LD)
     *
     * @param Article $article
     * @return array
     */
    private function generateStructuredData(Article $article): array
    {
        $seoData = $this->generateSeoData($article);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $article->title,
            'description' => $seoData['description'],
            'image' => $seoData['featured_image'] ? [$seoData['featured_image']] : [],
            'author' => [
                '@type' => 'Person',
                'name' => $article->author->name,
                'url' => config('app.url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $seoData['site_name'],
                'url' => config('app.url'),
            ],
            'datePublished' => $seoData['published_time'],
            'dateModified' => $seoData['modified_time'],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $seoData['canonical'],
            ],
            'articleSection' => $seoData['article_section'],
            'keywords' => implode(', ', $seoData['article_tag']),
            'wordCount' => str_word_count(strip_tags($article->content)),
            'url' => $seoData['canonical'],
        ];
    }

    /**
     * 生成文章關鍵字
     *
     * @param Article $article
     * @return string
     */
    private function generateKeywords(Article $article): string
    {
        $keywords = [];

        // 添加標籤名稱
        $keywords = array_merge($keywords, $article->tags->pluck('name')->toArray());

        // 添加分類名稱
        $keywords[] = $article->category->name;

        // 添加作者名稱
        $keywords[] = $article->author->name;

        // 去重並限制數量
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);

        return implode(', ', $keywords);
    }

    /**
     * 取得文章特色圖片 URL
     *
     * 目前專案沒有圖片功能，預留此方法供未來使用
     *
     * @param Article $article
     * @return string|null
     */
    private function getFeaturedImageUrl(Article $article): ?string
    {
        // TODO: 未來實作圖片功能時，在此返回文章特色圖片 URL

        // 暫時返回預設的網站 logo 或 null
        return null;
    }
}
