# Nginx 架構配置

## 概述

Aaron Blog 採用 **Laravel Monolith + Nginx** 架構，Nginx 作為 Web 伺服器處理靜態資源並將 PHP 請求轉發給 Laravel。針對 Cloudflare 代理環境和 GCP e2-micro 資源限制進行優化。

## 架構設計

### Laravel Monolith 架構

```
┌─────────────────────────────────────┐
│            Nginx Layer              │
├─────────────────────────────────────┤
│  🌐 靜態資源服務                     │
│  ├── /build/* (Vite 資源, 1年快取)  │
│  ├── CSS/JS/Images (1月快取)        │
│  └── 檔案上傳與媒體檔案              │
├─────────────────────────────────────┤
│  🔄 PHP 請求處理                     │
│  ├── 所有路由 → Laravel (app:9000)  │
│  ├── FastCGI 轉發到 PHP-FPM        │
│  └── Laravel 統一處理前後端         │
└─────────────────────────────────────┘
```

### 環境配置策略

**生產環境 (default.conf)**
- Cloudflare 代理優化: 信任 `CF-Connecting-IP` 標頭
- 安全防護: 封鎖直接 IP 存取，僅允許指定域名
- 域名限制: `aaronlei.com`, `www.aaronlei.com`

**開發環境 (default.dev.conf)**  
- 本地開發友好: 允許 `localhost`, `127.0.0.1` 存取
- CORS 寬鬆設定: 支援跨域開發調試
- 除錯標頭: `X-Debug-Environment: development`

## 效能優化策略

### 快取架構設計

**靜態資源快取分層**
- **Vite 建構資源** (`/build/*`): 1年長期快取 + `access_log off`
- **一般靜態資源** (CSS/JS/Images): 1月快取 + `access_log off`  
- **PHP 動態內容**: 無快取，每次重新生成

**效能優化配置**
- **檔案上傳限制**: 10MB (`client_max_body_size`)
- **FastCGI 優化**: 直接連接 `app:9000` PHP-FPM
- **版本隱藏**: `server_tokens off` + `fastcgi_hide_header X-Powered-By`

## 安全架構

### 多層安全防護

**網路層安全 (生產環境)**
- **直接 IP 封鎖**: 預設伺服器回傳 `444` (關閉連接)
- **域名白名單**: 僅允許 `aaronlei.com`, `www.aaronlei.com`
- **Cloudflare 整合**: 信任 `CF-Connecting-IP` 真實來源 IP

**應用層安全**
- **敏感檔案保護**: 禁止存取 `.env`, `.git`, `.htaccess` 等
- **目錄存取控制**: 封鎖 `vendor/`, `storage/`, `config/` 等目錄
- **PHP 注入防護**: `try_files $uri =404` 防止代碼注入

**HTTP 安全標頭**
- `X-Frame-Options: DENY` - 防止點擊劫持
- `X-Content-Type-Options: nosniff` - 防止 MIME 嗅探  
- `X-XSS-Protection: 1; mode=block` - XSS 防護
- `Referrer-Policy: strict-origin-when-cross-origin` - 來源資訊控制

## 容器化整合

### Docker 環境整合

**服務連接**
- **Container**: `nginx:alpine` 基礎映像
- **網路連接**: 內部網路連接 `app:9000` PHP-FPM 容器
- **Volume 掛載**: 配置檔案 + `/var/www/public` 靜態資源

**環境差異化**
- **開發環境**: 使用 `default.dev.conf` 支援 localhost 存取
- **生產環境**: 使用 `default.conf` 強化安全與 Cloudflare 整合
- **記憶體限制**: 128MB container limit (GCP e2-micro 適配)

---

*Nginx 配置針對 Laravel Monolith 架構進行優化，提供靜態資源服務與 PHP-FPM 代理功能，兼顧效能與安全性。*
