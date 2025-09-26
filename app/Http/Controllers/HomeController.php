<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Services\CategoryService;
use App\Services\TagService;
use App\Transformer\ArticleTransformer;
use App\Transformer\CategoryTransformer;
use App\Transformer\TagTransformer;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 首頁 SSR 控制器
 *
 * 處理首頁的伺服器端渲染，重用 Service 層和 Transformer
 * 提供與 Home.vue 相同的功能：搜尋、篩選、分頁
 */
class HomeController extends Controller
{
    public function __construct(
        protected readonly ArticleService $articleService,
        protected readonly CategoryService $categoryService,
        protected readonly TagService $tagService,
        protected readonly ArticleTransformer $articleTransformer,
        protected readonly CategoryTransformer $categoryTransformer,
        protected readonly TagTransformer $tagTransformer
    ) {}

    /**
     * 顯示首頁
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        try {
            // 解析搜尋和篩選參數
            $filters = $this->parseFilters($request);

            // 獲取文章列表（重用 ArticleService 和快取）
            $articlesData = $this->getArticlesData($filters);

            // 獲取分類和標籤（用於篩選器）
            $categoriesData = $this->getCategoriesData();
            $tagsData = $this->getTagsData();

            // 計算統計數據
            $stats = $this->calculateStats($articlesData, $categoriesData, $tagsData);

            return view('home', [
                'articles' => $articlesData['data'],
                'pagination' => $articlesData['meta']['pagination'],
                'categories' => $categoriesData,
                'tags' => $tagsData,
                'stats' => $stats,
                'filters' => $filters,
                'pageTitle' => $this->generatePageTitle($filters),
                'seoData' => $this->generateHomeSeoData($filters),
            ]);

        } catch (\Exception $e) {
            \Log::error('Home SSR Error: ' . $e->getMessage(), [
                'filters' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // 降級到基本首頁
            return $this->renderFallbackHome();
        }
    }

    /**
     * 解析請求中的篩選參數
     */
    private function parseFilters(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'tags' => $request->get('tags') ? explode(',', $request->get('tags')) : null,
            'page' => max(1, (int) $request->get('page', 1)),
            'per_page' => min(50, max(5, (int) $request->get('per_page', 10))),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
            'status' => 'published' // 固定為已發布
        ];
    }

    /**
     * 獲取文章列表數據（重用 Service 層）
     */
    private function getArticlesData(array $filters): array
    {
        // 使用 ArticleService 獲取分頁文章（含快取）
        $articles = $this->articleService->getArticles($filters);

        // 使用 ArticleTransformer 轉換數據（重用 API 層邏輯）
        $transformedArticles = $articles->map(function ($article) {
            return $this->articleTransformer->transform($article);
        });

        return [
            'data' => $transformedArticles->toArray(),
            'meta' => [
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'per_page' => $articles->perPage(),
                    'total_items' => $articles->total(),
                    'total_pages' => $articles->lastPage(),
                    'has_more_pages' => $articles->hasMorePages(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                ]
            ]
        ];
    }

    /**
     * 獲取所有分類數據
     */
    private function getCategoriesData(): array
    {
        $categories = $this->categoryService->getAllCategories();

        return $categories->map(function ($category) {
            return $this->categoryTransformer->transform($category);
        })->toArray();
    }

    /**
     * 獲取所有標籤數據
     */
    private function getTagsData(): array
    {
        $tags = $this->tagService->getAllTags();

        return $tags->map(function ($tag) {
            return $this->tagTransformer->transform($tag);
        })->toArray();
    }

    /**
     * 計算統計數據
     */
    private function calculateStats(array $articlesData, array $categoriesData, array $tagsData): array
    {
        return [
            'totalArticles' => $articlesData['meta']['pagination']['total_items'] ?? 0,
            'totalCategories' => count($categoriesData),
            'totalTags' => count($tagsData),
        ];
    }

    /**
     * 生成頁面標題
     */
    private function generatePageTitle(array $filters): string
    {
        $title = 'Aaron 的部落格';

        if ($filters['search']) {
            $title = "搜尋「{$filters['search']}」 - Aaron 的部落格";
        } elseif ($filters['category']) {
            $title = "分類「{$filters['category']}」 - Aaron 的部落格";
        }

        if ($filters['page'] > 1) {
            $title .= " - 第 {$filters['page']} 頁";
        }

        return $title;
    }

    /**
     * 生成首頁 SEO 資料
     */
    private function generateHomeSeoData(array $filters): array
    {
        $description = '分享程式設計、技術開發和個人學習心得的技術部落格';

        if ($filters['search']) {
            $description = "搜尋「{$filters['search']}」的相關文章 - " . $description;
        }

        return [
            'title' => $this->generatePageTitle($filters),
            'description' => $description,
            'canonical' => url()->current(),
            'type' => 'website'
        ];
    }

    /**
     * 渲染降級版本的首頁
     */
    private function renderFallbackHome(): View
    {
        return view('home', [
            'articles' => [],
            'pagination' => null,
            'categories' => [],
            'tags' => [],
            'stats' => ['totalArticles' => 0, 'totalCategories' => 0, 'totalTags' => 0],
            'filters' => [],
            'pageTitle' => 'Aaron 的部落格',
            'seoData' => [
                'title' => 'Aaron 的部落格',
                'description' => '技術部落格',
                'canonical' => url('/'),
                'type' => 'website'
            ],
        ]);
    }
}
