<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>Aaron 的部落格 - 網頁開發與程式設計技術分享</title>
    <meta name="description" content="Aaron 的個人技術部落格，分享網頁開發、程式設計、前端技術、後端開發和軟體工程相關的知識與心得。涵蓋 JavaScript、Vue.js、Laravel、PHP 等技術主題。">
    <meta name="keywords" content="Aaron部落格,網頁開發,程式設計,前端開發,後端開發,JavaScript,Vue.js,Laravel,PHP,軟體工程,技術分享">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Aaron">
    <link rel="canonical" href="{{ config('app.url') }}">
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    {{-- Open Graph Tags --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="Aaron 的部落格 - 網頁開發與程式設計技術分享">
    <meta property="og:description" content="Aaron 的個人技術部落格，分享網頁開發、程式設計和軟體工程相關的知識與心得。">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Aaron 的部落格') }}">
    <meta property="og:locale" content="zh_TW">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-default.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Aaron 的部落格 - 網頁開發與程式設計技術分享">
    <meta name="twitter:description" content="Aaron 的個人技術部落格，分享網頁開發、程式設計和軟體工程相關的知識與心得。">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/twitter-card.jpg">
    <meta name="twitter:creator" content="@aaron_dev">
    
    {{-- DNS Prefetch and Preconnect --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- 字體載入優化 --}}
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Structured Data (JSON-LD) --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Aaron 的部落格",
        "description": "分享網頁開發、程式設計和軟體工程相關的知識與心得",
        "url": "{{ config('app.url') }}",
        "author": {
            "@type": "Person",
            "name": "Aaron",
            "url": "{{ config('app.url') }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Aaron 的部落格",
            "url": "{{ config('app.url') }}"
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ config('app.url') }}/?search={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        },
        "mainEntity": {
            "@type": "Blog",
            "name": "Aaron 的部落格",
            "description": "技術部落格，專注於網頁開發和程式設計",
            "url": "{{ config('app.url') }}",
            "author": {
                "@type": "Person",
                "name": "Aaron"
            }
        }
    }
    </script>
    
    {{-- App Styles and Scripts --}}
    @vite(['resources/js/app.ts'])
</head>
<body>
    <div id="app"></div>
</body>
</html> 