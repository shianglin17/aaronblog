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
    
    {{-- Preconnect for fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- CSS Styles --}}
    @vite(['resources/css/app.css'])
    
    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body>
    {{-- 主要內容區域 --}}
    <div class="container">
        {{-- 返回導航 --}}
        <div class="navigation-back">
            <a href="/" class="back-button">
                ← 返回首頁
            </a>
        </div>
        
        {{-- 文章內容 --}}
        <article class="article-detail">
            {{-- 文章標題 --}}
            <header>
                <h1 class="article-title">{{ $article->title }}</h1>
                
                {{-- 文章元資料 --}}
                <div class="article-meta-wrapper">
                    <div class="article-meta">
                        <span class="article-author">
                            <span class="meta-icon">👤</span>
                            {{ $article->author->name }}
                        </span>
                        <span class="article-category">
                            <span class="meta-icon">📁</span>
                            {{ $article->category->name }}
                        </span>
                        <time datetime="{{ $article->created_at->toISOString() }}" class="article-date">
                            <span class="meta-icon">🕒</span>
                            {{ $article->created_at->format('Y年m月d日') }}
                        </time>
                    </div>
                    
                    {{-- 文章標籤 --}}
                    @if($article->tags && $article->tags->count() > 0)
                    <div class="article-tags">
                        <span class="tag-label">
                            <span class="meta-icon">🏷️</span>
                            標籤：
                        </span>
                        @foreach($article->tags as $tag)
                        <span class="article-tag">
                            {{ $tag->name }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </header>
            
            {{-- 文章描述（如果有） --}}
            @if($article->description)
            <div class="article-description">
                <p>{{ $article->description }}</p>
            </div>
            @endif
            
            {{-- 文章內容 --}}
            <div class="article-content">
                <div class="markdown-wrapper">
                    <div class="markdown-body">
                        {!! $renderedContent !!}
                    </div>
                </div>
            </div>
        </article>
        
        {{-- 頁面底部資訊 --}}
        <footer class="article-footer">
            <div class="article-info">
                <p>發布於 {{ $article->created_at->format('Y年m月d日') }}</p>
                @if($article->updated_at != $article->created_at)
                <p>最後更新於 {{ $article->updated_at->format('Y年m月d日') }}</p>
                @endif
            </div>
            
            <div class="back-to-top">
                <a href="#top" class="scroll-to-top">回到頂部 ↑</a>
            </div>
        </footer>
    </div>
    
    {{-- 漸進式增強的 JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 平滑滾動回到頂部
            const backToTopLinks = document.querySelectorAll('.scroll-to-top');
            backToTopLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
            
            // 平滑滾動到錨點
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            // 程式碼區塊增強（如果需要複製功能）
            const codeBlocks = document.querySelectorAll('pre code');
            codeBlocks.forEach(block => {
                // 為程式碼區塊加入語言標籤（如果有）
                const language = block.className.match(/language-(\w+)/);
                if (language) {
                    const pre = block.parentElement;
                    const label = document.createElement('div');
                    label.className = 'code-language';
                    label.textContent = language[1].toUpperCase();
                    pre.insertBefore(label, block);
                }
            });
        });
    </script>
    
    {{-- 結構化樣式 --}}
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 32px 20px;
            background: var(--background-color, #ffffff);
            min-height: 100vh;
            line-height: 1.6;
        }

        .navigation-back {
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-block;
            padding: 8px 16px;
            color: #7d6e5d;
            text-decoration: none;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .back-button:hover {
            background-color: #7d6e5d;
            color: white;
            border-color: #7d6e5d;
        }

        .article-detail {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .article-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 24px;
            line-height: 1.3;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 16px;
        }

        .article-meta-wrapper {
            margin-bottom: 36px;
        }

        .article-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            font-size: 0.9rem;
            color: #666;
            align-items: center;
        }

        .meta-icon {
            margin-right: 6px;
            opacity: 0.8;
        }

        .article-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px;
        }

        .tag-label {
            color: #666;
            font-size: 0.85rem;
        }

        .article-tag {
            background: #f0f0f0;
            color: #7d6e5d;
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 16px;
            display: inline-block;
            border: 1px solid #d0d0d0;
        }

        .article-description {
            background: #f8f9fa;
            padding: 16px 20px;
            border-left: 4px solid #7d6e5d;
            margin-bottom: 32px;
            border-radius: 0 6px 6px 0;
            font-style: italic;
            color: #666;
        }

        .article-content {
            margin-top: 32px;
        }

        .markdown-wrapper {
            background: white;
            border-radius: 8px;
        }

        .markdown-body {
            line-height: 1.75;
            color: #333;
            font-size: 1.05rem;
        }

        .article-footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
        }

        .back-to-top a {
            color: #7d6e5d;
            text-decoration: none;
            padding: 6px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .back-to-top a:hover {
            background-color: #7d6e5d;
            color: white;
        }

        .code-language {
            background: #333;
            color: white;
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 4px 4px 0 0;
            display: inline-block;
            margin-bottom: 0;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            .container {
                padding: 20px 16px;
            }
            
            .article-detail {
                padding: 24px 20px;
            }
            
            .article-title {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
            
            .article-footer {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 16px 12px;
            }
            
            .article-detail {
                padding: 20px 16px;
            }
            
            .article-title {
                font-size: 1.6rem;
                margin-bottom: 16px;
            }
            
            .markdown-body {
                font-size: 1rem;
            }
        }
    </style>
</body>
</html>