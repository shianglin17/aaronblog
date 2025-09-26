<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO Meta Tags --}}
    <title>{{ $seoData['title'] }}</title>
    <meta name="description" content="{{ $seoData['description'] }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seoData['canonical'] }}">

    {{-- Open Graph Tags --}}
    <meta property="og:type" content="{{ $seoData['type'] }}">
    <meta property="og:title" content="{{ $seoData['title'] }}">
    <meta property="og:description" content="{{ $seoData['description'] }}">
    <meta property="og:url" content="{{ $seoData['canonical'] }}">
    <meta property="og:site_name" content="Aaron Blog">
    <meta property="og:locale" content="zh_TW">

    {{-- CSS Styles - 首頁專用樣式（包含文章頁面設計系統） --}}
    @vite(['resources/css/home.css'])

</head>
<body class="dark">
    {{-- 首頁容器 --}}
    <div class="home-container">
        {{-- 導航區域 - 包含品牌和搜尋 --}}
        <nav class="home-nav">
            <div class="nav-container">
                {{-- 品牌標誌 --}}
                <div class="brand-logo">
                    <h1 class="brand-name">
                        <span class="brand-text">Aaron</span><span class="brand-accent">Blog</span>
                    </h1>
                </div>

                {{-- 手機版漢堡選單按鈕 --}}
                <button class="mobile-menu-toggle" type="button" onclick="toggleMobileMenu()">
                    <svg class="hamburger-icon" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>

                {{-- 搜尋表單容器 --}}
                <div class="nav-search-container" id="nav-search-container">
                {{-- 搜尋表單 --}}
                <form class="search-form" method="GET" action="/">
                    <div class="search-wrapper">
                        <div class="search-input-container">
                            <svg class="search-icon" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                            <input
                                type="search"
                                name="search"
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="搜尋文章..."
                                class="search-input"
                            />
                            @if($filters['search'] ?? false)
                                <a href="/" class="clear-button" title="清除搜尋">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>

                        {{-- 篩選按鈕 --}}
                        <button type="button" class="filter-toggle" onclick="toggleFilters()">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                            篩選
                            @if(($filters['category'] ?? false) || ($filters['tags'] ?? false))
                                <span class="filter-indicator"></span>
                            @endif
                        </button>
                    </div>

                    {{-- 篩選面板 --}}
                    <div class="filter-panel" id="filter-panel" style="display: none;">
                        <div class="filter-section">
                            <label class="filter-label">分類</label>
                            <select name="category" class="filter-select">
                                <option value="">所有分類</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['slug'] }}"
                                            {{ ($filters['category'] ?? '') === $category['slug'] ? 'selected' : '' }}>
                                        {{ $category['name'] }} ({{ $category['articles_count'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-section">
                            <label class="filter-label">標籤</label>
                            <div class="tags-container">
                                @foreach($tags as $tag)
                                    <label class="tag-checkbox">
                                        <input type="checkbox"
                                               name="tags[]"
                                               value="{{ $tag['slug'] }}"
                                               {{ in_array($tag['slug'], $filters['tags'] ?? []) ? 'checked' : '' }}>
                                        <span class="tag-label">{{ $tag['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="apply-filters-btn">套用篩選</button>
                            <a href="/" class="clear-filters-btn">清除所有</a>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </nav>

        {{-- 主要內容區域 --}}
        <main class="home-main">
            {{-- 個人資料側邊欄 --}}
            <aside class="profile-sidebar">
                <div class="profile-card">
                    <div class="mobile-profile-toggle" onclick="toggleMobileProfile()">
                        <div class="profile-basic">
                            <img src="{{ asset('images/aaron-avatar.jpg') }}" alt="Aaron 的頭像" class="mobile-avatar" />
                            <div class="mobile-profile-info">
                                <h2 class="mobile-profile-name">Aaron Lei</h2>
                                <p class="mobile-profile-title">Backend Developer</p>
                            </div>
                        </div>
                        <button class="collapse-btn" type="button">
                            <svg class="collapse-icon" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="profile-details" id="profile-details">
                        <div class="desktop-profile-header">
                            <div class="avatar-container">
                                <img src="{{ asset('images/aaron-avatar.jpg') }}" alt="Aaron 的頭像" class="profile-avatar" />
                                <div class="avatar-badge">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8l3.147-3.146zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8l-3.147-3.146z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="profile-info">
                                <h2 class="profile-name">Aaron Lei</h2>
                                <p class="profile-title">Backend Developer</p>
                            </div>
                        </div>

                    <div class="profile-bio">
                        <p>分享程式設計、技術開發和個人學習心得</p>
                    </div>

                    <div class="tech-stack">
                        <h3 class="tech-stack-title">本專案使用技術</h3>
                        <div class="tech-tags">
                            <span class="tech-tag">Laravel</span>
                            <span class="tech-tag">PHP</span>
                            <span class="tech-tag">Vue.js</span>
                            <span class="tech-tag">TypeScript</span>
                            <span class="tech-tag">MySQL</span>
                            <span class="tech-tag">Redis</span>
                            <span class="tech-tag">Docker</span>
                            <span class="tech-tag">Git</span>
                        </div>
                    </div>

                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $stats['totalArticles'] }}</span>
                            <span class="stat-label">文章</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $stats['totalCategories'] }}</span>
                            <span class="stat-label">分類</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $stats['totalTags'] }}</span>
                            <span class="stat-label">標籤</span>
                        </div>
                    </div>
                    </div>
                </div>
            </aside>

            {{-- 文章列表區域 --}}
            <section class="articles-section">
                {{-- 搜尋結果提示 --}}
                @if($filters['search'] ?? false)
                    <div class="search-result-info">
                        搜尋「{{ $filters['search'] }}」找到 {{ $pagination['total_items'] ?? 0 }} 篇文章
                    </div>
                @endif

                {{-- 文章列表 --}}
                @if(count($articles) > 0)
                    <div class="articles-grid">
                        @foreach($articles as $article)
                            <article class="article-card">
                                <header class="article-card-header">
                                    <h3 class="article-card-title">
                                        <a href="/article/{{ $article['slug'] }}">{{ $article['title'] }}</a>
                                    </h3>
                                    <div class="article-card-meta">
                                        <time datetime="{{ $article['created_at'] }}">
                                            {{ \Carbon\Carbon::parse($article['created_at'])->format('Y年n月j日') }}
                                        </time>
                                        <span class="category-tag">{{ $article['category']['name'] }}</span>
                                    </div>
                                </header>

                                @if($article['description'])
                                    <div class="article-card-excerpt">
                                        {{ $article['description'] }}
                                    </div>
                                @endif

                                @if(count($article['tags']) > 0)
                                    <div class="article-card-tags">
                                        @foreach($article['tags'] as $tag)
                                            <span class="tag">{{ $tag['name'] }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    {{-- 分頁 --}}
                    @if($pagination)
                        <nav class="pagination-nav">
                            <div class="pagination-info">
                                顯示第 {{ $pagination['from'] }}-{{ $pagination['to'] }} 項，
                                共 {{ $pagination['total_items'] }} 項
                            </div>

                            @if($pagination['total_pages'] > 1)
                                <div class="pagination-controls">
                                    {{-- 首頁 --}}
                                    @if($pagination['current_page'] > 3)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="pagination-btn">1</a>
                                        @if($pagination['current_page'] > 4)
                                            <span class="pagination-ellipsis">⋯</span>
                                        @endif
                                    @endif

                                    {{-- 上一頁箭頭 --}}
                                    @if($pagination['current_page'] > 1)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}"
                                           class="pagination-btn prev-next" title="上一頁">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- 頁碼 --}}
                                    @for($page = max(1, $pagination['current_page'] - 2); $page <= min($pagination['total_pages'], $pagination['current_page'] + 2); $page++)
                                        @if($page === $pagination['current_page'])
                                            <span class="pagination-btn current" aria-current="page">{{ $page }}</span>
                                        @else
                                            <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                                               class="pagination-btn">{{ $page }}</a>
                                        @endif
                                    @endfor

                                    {{-- 下一頁箭頭 --}}
                                    @if($pagination['current_page'] < $pagination['total_pages'])
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}"
                                           class="pagination-btn prev-next" title="下一頁">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- 尾頁 --}}
                                    @if($pagination['current_page'] < $pagination['total_pages'] - 2)
                                        @if($pagination['current_page'] < $pagination['total_pages'] - 3)
                                            <span class="pagination-ellipsis">⋯</span>
                                        @endif
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['total_pages']]) }}"
                                           class="pagination-btn">{{ $pagination['total_pages'] }}</a>
                                    @endif
                                </div>
                            @endif
                        </nav>
                    @endif
                @else
                    <div class="no-articles">
                        <h2>沒有找到文章</h2>
                        <p>嘗試調整搜尋條件或瀏覽所有文章</p>
                        <a href="/" class="return-home-btn">回到首頁</a>
                    </div>
                @endif
            </section>
        </main>

        {{-- 頁腳 --}}
        <footer class="home-footer">
            <div class="footer-content">
                <p>&copy; {{ date('Y') }} Aaron Blog.</p>
            </div>
        </footer>
    </div>

    {{-- JavaScript 增強功能 --}}
    <script>
        // 切換篩選面板
        function toggleFilters() {
            const panel = document.getElementById('filter-panel');
            const isVisible = panel.style.display !== 'none';
            panel.style.display = isVisible ? 'none' : 'block';
        }

        // 點擊外部關閉篩選面板
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('filter-panel');
            const toggleBtn = document.querySelector('.filter-toggle');

            if (!panel.contains(e.target) && !toggleBtn.contains(e.target)) {
                panel.style.display = 'none';
            }
        });

        // 表單提交前處理標籤數組
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            const checkedTags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked'))
                .map(cb => cb.value);

            // 移除所有現有的 tags[] hidden input
            document.querySelectorAll('input[name="tags"]').forEach(input => input.remove());

            // 如果有選中的標籤，創建一個隱藏的 tags input
            if (checkedTags.length > 0) {
                const tagsInput = document.createElement('input');
                tagsInput.type = 'hidden';
                tagsInput.name = 'tags';
                tagsInput.value = checkedTags.join(',');
                this.appendChild(tagsInput);
            }
        });

        // 手機版個人資料收合功能
        function toggleMobileProfile() {
            const details = document.getElementById('profile-details');
            const icon = document.querySelector('.collapse-icon');
            const isExpanded = details.style.display !== 'none' && details.style.display !== '';

            if (isExpanded) {
                details.style.display = 'none';
                icon.style.transform = 'rotate(180deg)';
            } else {
                details.style.display = 'block';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // 監聽視窗大小變化，重置個人資料狀態
        function handleResponsiveProfileState() {
            const details = document.getElementById('profile-details');
            const icon = document.querySelector('.collapse-icon');

            // 檢查是否為桌面版（寬度大於 768px）
            if (window.innerWidth > 768) {
                // 桌面版：清除 inline style，讓 CSS 控制
                details.style.display = '';
                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                }
            } else {
                // 手機版：如果沒有設定 style，預設為隱藏
                if (!details.style.display) {
                    details.style.display = 'none';
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            }
        }

        // 頁面載入時初始化
        handleResponsiveProfileState();

        // 監聽視窗大小變化
        window.addEventListener('resize', handleResponsiveProfileState);

        // 手機版漢堡選單功能
        function toggleMobileMenu() {
            const searchContainer = document.getElementById('nav-search-container');
            const isVisible = searchContainer.style.display !== 'none' && searchContainer.style.display !== '';

            if (isVisible) {
                searchContainer.style.display = 'none';
            } else {
                searchContainer.style.display = 'block';
            }
        }

        // 點擊外部關閉手機版選單
        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById('nav-search-container');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');

            if (window.innerWidth <= 768 &&
                !searchContainer.contains(e.target) &&
                !toggleBtn.contains(e.target)) {
                searchContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
