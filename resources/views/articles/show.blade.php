<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $seoData['title'] }}</title>
    <meta name="description" content="{{ $seoData['description'] }}">
    <meta name="keywords" content="{{ $seoData['keywords'] }}">
    <meta name="author" content="{{ $seoData['author'] }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seoData['canonical'] }}">
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    {{-- Open Graph Tags --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $seoData['description'] }}">
    <meta property="og:url" content="{{ $seoData['canonical'] }}">
    <meta property="og:site_name" content="{{ $seoData['site_name'] }}">
    <meta property="og:locale" content="zh_TW">
    @if($seoData['featured_image'])
    <meta property="og:image" content="{{ $seoData['featured_image'] }}">
    <meta property="og:image:alt" content="{{ $article->title }}">
    @endif
    
    {{-- Article specific Open Graph --}}
    <meta property="article:published_time" content="{{ $seoData['published_time'] }}">
    <meta property="article:modified_time" content="{{ $seoData['modified_time'] }}">
    <meta property="article:author" content="{{ $seoData['author'] }}">
    <meta property="article:section" content="{{ $seoData['article_section'] }}">
    @foreach($seoData['article_tag'] as $tag)
    <meta property="article:tag" content="{{ $tag }}">
    @endforeach
    
    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ $seoData['description'] }}">
    @if($seoData['featured_image'])
    <meta name="twitter:image" content="{{ $seoData['featured_image'] }}">
    @endif
    
    {{-- CSS Styles --}}
    @vite(['resources/css/article.css'])
    
    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body class="dark">
    {{-- 文章容器 --}}
    <div class="article-container">
        {{-- 導航區域 --}}
        <nav class="article-nav">
            <a href="/" class="nav-button fade-in">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                返回首頁
            </a>
        </nav>
        
        {{-- 文章主體 --}}
        <main class="article-main">
            <article class="article-card fade-in">
                {{-- 文章標題區域 --}}
                <header class="article-header">
                    <h1 class="article-title">
                        {{ $article->title }}
                    </h1>
                    
                    {{-- 文章元數據 --}}
                    <div class="article-meta">
                        <div class="meta-item">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4a4 4 0 100 8 4 4 0 000-8zM6 8a6 6 0 1112 0A6 6 0 016 8zM12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z"/>
                            </svg>
                            {{ $article->author->name }}
                        </div>
                        
                        <div class="meta-item">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM4 8v10h16V8H4z"/>
                                <path d="M6 6h2v2H6zM10 6h2v2h-2zM14 6h2v2h-2z"/>
                            </svg>
                            {{ $article->category->name }}
                        </div>
                        
                        <time datetime="{{ $article->created_at->toISOString() }}" class="meta-item">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                <path d="M12 7v5l3.25 1.63-.75 1.37L11 13V7h1z"/>
                            </svg>
                            {{ $article->created_at->format('Y年n月j日') }}
                        </time>
                        
                        <div class="meta-item">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                <path d="M12 6v6l4 2-1 1.73-5-3V6h2z"/>
                            </svg>
                            約 {{ ceil(mb_strlen(strip_tags($renderedContent), 'UTF-8') / 400) }} 分鐘閱讀
                        </div>
                    </div>
                    
                    {{-- 文章標籤 --}}
                    @if($article->tags && $article->tags->count() > 0)
                    <div class="article-tags">
                        <span class="tags-label">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 2 2 2h11c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                            </svg>
                            標籤
                        </span>
                        @foreach($article->tags as $tag)
                        <span class="tag">
                            {{ $tag->name }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </header>
                
                {{-- 文章描述 --}}
                @if($article->description)
                <div class="article-description">
                    {{ $article->description }}
                </div>
                @endif
                
                {{-- 文章內容 --}}
                <div class="article-content">
                    <div class="prose">
                        {!! $renderedContent !!}
                    </div>
                </div>
                
                {{-- 頁面底部 --}}
                <footer class="article-footer">
                    <div class="footer-content">
                        <div class="footer-info">
                            <div>發布於 {{ $article->created_at->format('Y年n月j日') }}</div>
                            @if($article->updated_at->gt($article->created_at))
                            <div>最後更新於 {{ $article->updated_at->format('Y年n月j日') }}</div>
                            @endif
                        </div>
                        
                        <div class="footer-actions">
                            <button onclick="shareArticle()" class="action-button secondary">
                                分享文章
                            </button>
                            <button onclick="scrollToTop()" class="action-button">
                                回到頂部
                            </button>
                        </div>
                    </div>
                </footer>
            </article>
        </main>
    </div>
    
    {{-- JavaScript --}}
    <script>
        // 回到頂部功能
        function scrollToTop() {
            window.scrollTo({ 
                top: 0, 
                behavior: 'smooth' 
            });
        }
        
        // 分享文章功能
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $article->title }}',
                    text: '{{ $article->description ?? "值得一讀的技術文章" }}',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // 複製網址
                navigator.clipboard.writeText(window.location.href).then(() => {
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '已複製連結';
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 2000);
                });
            }
        }
        
        // 主題初始化 - 讀取首頁設定的主題偏好
        function initTheme() {
            const body = document.body;
            // 從 localStorage 獲取主題設置，預設為深色
            const savedTheme = localStorage.getItem('theme') || 'dark';
            setTheme(savedTheme);
        }

        function setTheme(theme) {
            const body = document.body;
            if (theme === 'light') {
                body.classList.remove('dark');
                body.classList.add('light');
            } else {
                body.classList.remove('light');
                body.classList.add('dark');
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

        // 頁面載入完成後的增強功能
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化主題
            initTheme();
            
            // 啟用跨頁籤主題同步
            watchThemeChanges();
            // 平滑滾動到錨點
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // 鍵盤快捷鍵
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + K: 分享
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    shareArticle();
                }
                
                // Home 鍵：回到頂部
                if (e.key === 'Home') {
                    e.preventDefault();
                    scrollToTop();
                }
            });
        });
    </script>
</body>
</html>