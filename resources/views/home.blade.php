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

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

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

                        {{-- 主題偏好設定 --}}
                        <div class="filter-section">
                            <label class="filter-label">檢視偏好</label>
                            <div class="preference-item">
                                <span class="preference-label">主題模式</span>
                                <button id="theme-toggle" class="theme-toggle" title="切換主題">
                                    <span class="theme-icon light-icon">☀️</span>
                                    <span class="theme-icon dark-icon">🌙</span>
                                    <span class="theme-text">
                                        <span class="light-text">亮色</span>
                                        <span class="dark-text">深色</span>
                                    </span>
                                </button>
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
                {{-- 社交媒體連結 --}}
                <div class="social-media">
                    <div class="social-links">
                        <a href="https://www.instagram.com/lei171717/" 
                           aria-label="Instagram" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="social-link">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                            </svg>
                        </a>
                        
                        <a href="https://www.linkedin.com/in/%E7%BF%94%E9%BA%9F-%E9%9B%B7-350597199/" 
                           aria-label="LinkedIn" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="social-link">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                            </svg>
                        </a>
                        
                        <a href="https://www.threads.com/@lei171717?hl=zh-tw" 
                           aria-label="Threads" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="social-link">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm2.5 3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1H8.5v4.5a.5.5 0 0 1-1 0V5.5H5a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                {{-- 版權資訊 --}}
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} Aaron 的個人部落格</p>
                    <p class="version" id="version-info">v1.0.0</p>
                </div>
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

        // 獲取版本資訊
        async function fetchVersionInfo() {
            try {
                const response = await fetch('https://api.github.com/repos/shianglin17/aaronblog/tags', {
                    headers: {
                        'Accept': 'application/vnd.github.v3+json',
                        'User-Agent': 'AaronBlog-Frontend'
                    }
                });

                if (response.ok) {
                    const tags = await response.json();
                    if (tags && tags.length > 0) {
                        const latestTag = tags[0];
                        document.getElementById('version-info').textContent = latestTag.name;
                    }
                }
            } catch (error) {
                console.warn('無法獲取版本資訊:', error);
            }
        }

        // 頁面載入完成後獲取版本
        fetchVersionInfo();

        // 主題切換功能
        function initTheme() {
            const themeToggle = document.getElementById('theme-toggle');
            if (!themeToggle) return; // 如果找不到按鈕就退出
            
            const body = document.body;
            
            // 從 localStorage 獲取主題設置，預設為深色
            const savedTheme = localStorage.getItem('theme') || 'dark';
            setTheme(savedTheme);
            
            // 點擊切換主題
            themeToggle.addEventListener('click', () => {
                const currentTheme = body.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }

        function setTheme(theme) {
            const body = document.body;
            const themeToggle = document.getElementById('theme-toggle');
            
            if (theme === 'light') {
                body.classList.remove('dark');
                body.classList.add('light');
                if (themeToggle) {
                    themeToggle.setAttribute('title', '切換到深色模式');
                }
            } else {
                body.classList.remove('light');
                body.classList.add('dark');
                if (themeToggle) {
                    themeToggle.setAttribute('title', '切換到亮色模式');
                }
            }
        }

        // 跨頁籤主題同步 - 監聽其他頁籤的主題變更
        function watchThemeChanges() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'theme' && e.newValue) {
                    setTheme(e.newValue);
                }
            });
        }

        // 初始化主題
        initTheme();
        
        // 啟用跨頁籤主題同步
        watchThemeChanges();
    </script>
</body>
</html>
