# Nginx 配置策略

## 概述

Aaron Blog 採用 Nginx 作為 Web 伺服器和反向代理，負責處理靜態檔案服務、SSL 終止、請求路由和效能優化。配置針對 SPA (Single Page Application) 和 API 路由進行了優化。

## 架構設計

### 服務架構

```
┌─────────────────────────────────────┐
│              Nginx                  │
├─────────────────────────────────────┤
│  Port 80/443 (HTTP/HTTPS)          │
│           ↓                         │
│  ┌─────────────────┐                │
│  │  Static Files   │                │
│  │  - Vue.js SPA   │                │
│  │  - CSS/JS       │                │
│  │  - Images       │                │
│  └─────────────────┘                │
│           ↓                         │
│  ┌─────────────────┐                │
│  │ Reverse Proxy   │                │
│  │  - /api/*       │                │
│  │  - Laravel App  │                │
│  │  - Port 9000    │                │
│  └─────────────────┘                │
└─────────────────────────────────────┘
```

### 路由策略

**靜態檔案路由**
- `/` → Vue.js SPA
- `/assets/*` → 靜態資源
- `/favicon.ico` → 網站圖標

**API 路由**
- `/api/*` → Laravel 後端
- `/sanctum/*` → 認證端點

## 配置檔案結構

### 主配置檔案

**nginx.conf**
```nginx
user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # 日誌格式
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';
    
    access_log /var/log/nginx/access.log main;
    
    # 基本設定
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    
    # Gzip 壓縮
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;
    
    # 包含站點配置
    include /etc/nginx/conf.d/*.conf;
}
```

### 站點配置

**default.conf**
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html;
    index index.html index.htm;
    
    # 安全標頭
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # 靜態檔案快取
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary "Accept-Encoding";
        
        # 處理 CORS
        add_header Access-Control-Allow-Origin "*";
        add_header Access-Control-Allow-Methods "GET, OPTIONS";
        add_header Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept";
        
        # 如果檔案不存在，返回 404
        try_files $uri =404;
    }
    
    # API 路由代理
    location /api/ {
        proxy_pass http://app:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # 超時設定
        proxy_connect_timeout 30s;
        proxy_send_timeout 30s;
        proxy_read_timeout 30s;
        
        # 緩衝設定
        proxy_buffering on;
        proxy_buffer_size 4k;
        proxy_buffers 8 4k;
        proxy_busy_buffers_size 8k;
    }
    
    # Sanctum 認證路由
    location /sanctum/ {
        proxy_pass http://app:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # 認證相關的特殊設定
        proxy_set_header X-Forwarded-Host $host;
        proxy_set_header X-Forwarded-Port $server_port;
    }
    
    # SPA 路由處理
    location / {
        try_files $uri $uri/ /index.html;
        
        # HTML 檔案不快取
        location ~* \.html$ {
            expires -1;
            add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0";
        }
    }
    
    # 健康檢查端點
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }
    
    # 隱藏版本資訊
    server_tokens off;
    
    # 錯誤頁面
    error_page 404 /index.html;
    error_page 500 502 503 504 /50x.html;
    
    location = /50x.html {
        root /usr/share/nginx/html;
    }
}
```

## 效能優化

### 快取策略

**靜態資源快取**
```nginx
# 長期快取（1年）
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Vary "Accept-Encoding";
}

# HTML 檔案不快取
location ~* \.html$ {
    expires -1;
    add_header Cache-Control "no-store, no-cache, must-revalidate";
}

# API 回應快取
location /api/ {
    # 根據 API 回應設定快取
    proxy_cache_valid 200 302 10m;
    proxy_cache_valid 404 1m;
    
    # 快取鍵設定
    proxy_cache_key "$scheme$request_method$host$request_uri";
    
    # 快取條件
    proxy_cache_bypass $http_pragma $http_authorization;
    proxy_no_cache $http_pragma $http_authorization;
}
```

### 壓縮優化

**Gzip 壓縮設定**
```nginx
# 啟用 Gzip 壓縮
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied any;
gzip_comp_level 6;

# 壓縮檔案類型
gzip_types
    text/plain
    text/css
    text/xml
    text/javascript
    application/json
    application/javascript
    application/xml+rss
    application/atom+xml
    image/svg+xml;

# 不壓縮的檔案
gzip_disable "msie6";
```

**Brotli 壓縮（可選）**
```nginx
# 如果有 Brotli 模組
brotli on;
brotli_comp_level 6;
brotli_types
    text/plain
    text/css
    application/json
    application/javascript
    text/xml
    application/xml
    application/xml+rss
    text/javascript;
```

### 連接優化

**Keep-Alive 設定**
```nginx
# HTTP Keep-Alive
keepalive_timeout 65;
keepalive_requests 100;

# 上游連接池
upstream app {
    server app:9000;
    keepalive 32;
}

location /api/ {
    proxy_pass http://app;
    proxy_http_version 1.1;
    proxy_set_header Connection "";
}
```

## 安全配置

### 安全標頭

**基本安全標頭**
```nginx
# 防止點擊劫持
add_header X-Frame-Options "SAMEORIGIN" always;

# 防止 MIME 類型嗅探
add_header X-Content-Type-Options "nosniff" always;

# XSS 防護
add_header X-XSS-Protection "1; mode=block" always;

# 推薦人政策
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# 內容安全政策
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https:; frame-ancestors 'self';" always;
```

### 速率限制

**API 速率限制**
```nginx
# 定義速率限制區域
http {
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
}

server {
    # API 一般限制
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        limit_req_status 429;
        
        proxy_pass http://app:9000;
    }
    
    # 登入 API 嚴格限制
    location /api/login {
        limit_req zone=login burst=3 nodelay;
        limit_req_status 429;
        
        proxy_pass http://app:9000;
    }
}
```

### 存取控制

**IP 白名單（可選）**
```nginx
# 管理介面存取控制
location /admin/ {
    allow 192.168.1.0/24;
    allow 10.0.0.0/8;
    deny all;
    
    proxy_pass http://app:9000;
}

# 開發環境存取控制
location /debug/ {
    allow 127.0.0.1;
    allow ::1;
    deny all;
    
    proxy_pass http://app:9000;
}
```

## SSL/TLS 配置

### HTTPS 設定

**SSL 憑證配置**
```nginx
server {
    listen 443 ssl http2;
    server_name example.com;
    
    # SSL 憑證
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    
    # SSL 設定
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # SSL 優化
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    
    # 其他配置...
}

# HTTP 重定向到 HTTPS
server {
    listen 80;
    server_name example.com;
    return 301 https://$server_name$request_uri;
}
```

### 憑證管理

**Let's Encrypt 自動更新**
```bash
# 使用 Certbot 獲取憑證
certbot --nginx -d example.com

# 自動更新 cron 任務
0 12 * * * /usr/bin/certbot renew --quiet
```

## 監控與日誌

### 存取日誌

**自定義日誌格式**
```nginx
# 詳細日誌格式
log_format detailed '$remote_addr - $remote_user [$time_local] '
                    '"$request" $status $body_bytes_sent '
                    '"$http_referer" "$http_user_agent" '
                    '$request_time $upstream_response_time '
                    '$pipe $connection_requests';

# API 專用日誌
log_format api_log '$remote_addr - $remote_user [$time_local] '
                   '"$request" $status $body_bytes_sent '
                   '$request_time $upstream_response_time '
                   '"$http_user_agent"';

server {
    # 一般存取日誌
    access_log /var/log/nginx/access.log detailed;
    
    # API 專用日誌
    location /api/ {
        access_log /var/log/nginx/api.log api_log;
        proxy_pass http://app:9000;
    }
}
```

### 狀態監控

**Nginx 狀態模組**
```nginx
# 編譯時需要 --with-http_stub_status_module
location /nginx_status {
    stub_status on;
    access_log off;
    
    # 限制存取
    allow 127.0.0.1;
    allow ::1;
    deny all;
}
```

**健康檢查**
```nginx
location /health {
    access_log off;
    return 200 "healthy\n";
    add_header Content-Type text/plain;
}

# 詳細健康檢查
location /health/detailed {
    access_log off;
    
    # 檢查上游服務
    proxy_pass http://app:9000/health;
    proxy_set_header Host $host;
    
    # 超時設定
    proxy_connect_timeout 5s;
    proxy_send_timeout 5s;
    proxy_read_timeout 5s;
}
```

## 容器化配置

### Docker 整合

**Dockerfile**
```dockerfile
FROM nginx:alpine

# 複製配置檔案
COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf

# 複製靜態檔案
COPY dist/ /var/www/html/

# 設定權限
RUN chown -R nginx:nginx /var/www/html

# 暴露端口
EXPOSE 80

# 健康檢查
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

CMD ["nginx", "-g", "daemon off;"]
```

### Docker Compose 整合

**docker-compose.yml**
```yaml
services:
  nginx:
    build: ./nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ./nginx/conf.d:/etc/nginx/conf.d:ro
      - ./dist:/var/www/html:ro
      - ./ssl:/etc/nginx/ssl:ro
    depends_on:
      - app
    restart: unless-stopped
    
  app:
    build: .
    expose:
      - "9000"
    depends_on:
      - redis
    restart: unless-stopped
```

## 效能調優

### 工作程序優化

**Worker 配置**
```nginx
# 根據 CPU 核心數設定
worker_processes auto;

# 工作程序優先級
worker_priority -10;

# 工作程序檔案描述符限制
worker_rlimit_nofile 65535;

events {
    # 每個工作程序的連接數
    worker_connections 1024;
    
    # 使用 epoll (Linux)
    use epoll;
    
    # 一次接受多個連接
    multi_accept on;
}
```

### 緩衝區優化

**緩衝區設定**
```nginx
# 客戶端緩衝區
client_body_buffer_size 16k;
client_header_buffer_size 1k;
client_max_body_size 8m;
large_client_header_buffers 4 16k;

# 代理緩衝區
proxy_buffering on;
proxy_buffer_size 4k;
proxy_buffers 8 4k;
proxy_busy_buffers_size 8k;
proxy_temp_file_write_size 8k;
```

### 檔案快取

**開啟檔案快取**
```nginx
# 檔案描述符快取
open_file_cache max=1000 inactive=20s;
open_file_cache_valid 30s;
open_file_cache_min_uses 2;
open_file_cache_errors on;
```

## 故障排除

### 常見問題

**1. 502 Bad Gateway**
```nginx
# 檢查上游服務
upstream app {
    server app:9000 max_fails=3 fail_timeout=30s;
    
    # 備用服務器
    server app2:9000 backup;
}

# 增加超時時間
proxy_connect_timeout 60s;
proxy_send_timeout 60s;
proxy_read_timeout 60s;
```

**2. 504 Gateway Timeout**
```nginx
# 增加超時設定
proxy_connect_timeout 300s;
proxy_send_timeout 300s;
proxy_read_timeout 300s;

# 緩衝區設定
proxy_buffering off;
```

**3. 靜態檔案 404**
```nginx
# 檢查檔案路徑
location /assets/ {
    alias /var/www/html/assets/;
    try_files $uri =404;
    
    # 除錯日誌
    error_log /var/log/nginx/static_error.log debug;
}
```

### 除錯工具

**日誌分析**
```bash
# 即時查看存取日誌
tail -f /var/log/nginx/access.log

# 分析錯誤日誌
grep "error" /var/log/nginx/error.log | tail -20

# 統計狀態碼
awk '{print $9}' /var/log/nginx/access.log | sort | uniq -c | sort -nr
```

**配置測試**
```bash
# 測試配置檔案
nginx -t

# 重新載入配置
nginx -s reload

# 檢查配置語法
nginx -T
```

## 最佳實踐

### 效能優化建議

1. **啟用 HTTP/2**
   - 提升多重請求效能
   - 減少延遲

2. **合理設定快取**
   - 靜態資源長期快取
   - 動態內容短期快取

3. **壓縮優化**
   - 啟用 Gzip/Brotli
   - 選擇合適的壓縮等級

### 安全建議

1. **隱藏版本資訊**
   - 設定 `server_tokens off`
   - 自定義錯誤頁面

2. **設定安全標頭**
   - 防止 XSS 攻擊
   - 防止點擊劫持

3. **實施速率限制**
   - 防止 DDoS 攻擊
   - 保護 API 端點

### 維護建議

1. **定期更新**
   - 保持 Nginx 版本更新
   - 更新 SSL 憑證

2. **監控日誌**
   - 定期分析存取日誌
   - 監控錯誤日誌

3. **效能測試**
   - 定期進行負載測試
   - 監控回應時間

---

*此 Nginx 配置策略確保了 Aaron Blog 的高效能、安全性和穩定性。* 