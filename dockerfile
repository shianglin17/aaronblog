# Dockerfile for Laravel + Vue 混合專案
# 採用多階段建構，確保正式映像輕量且安全

# === 第一階段：Node.js，負責前端 build ===
# 使用 Node.js 20 輕量映像
FROM node:20-alpine as node_builder

# 設定工作目錄
WORKDIR /app

# 複製前端依賴描述檔
COPY package*.json ./
# 複製 Vite 設定檔
COPY vite.config.js ./
# 複製前端原始碼
COPY resources ./resources
# 安裝前端依賴
RUN npm install
# 執行前端 build，產生 public/build 靜態檔案
RUN npm run build

# === 第二階段：PHP，負責後端與整合 ===
# 使用 PHP 8.2 FPM 映像
FROM php:8.2-fpm

# 安裝系統套件與 PHP 擴充
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    # 這裡可以加其他 PHP extension 需要的系統函式庫
    # 可以把 apt-get install 想像成「安裝作業系統的底層工具」，
    # docker-php-ext-install 則是「安裝 PHP 的功能插件」。
    && docker-php-ext-install pdo_mysql
# 上述套件說明：
# libpng-dev         # 處理圖片用
# libonig-dev        # 處理字串用
# libxml2-dev        # 處理 XML 用
# zip                # 壓縮/解壓縮
# unzip              # 解壓縮
# git                # 版本控制
# curl               # 網路下載工具
# 上述最後一行就是安裝 pdo_mysql

# 安裝 Composer（PHP 套件管理工具）
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# 設定工作目錄為 /var/www
WORKDIR /var/www

# 複製專案所有檔案到容器
COPY . .

# 複製前端 build 後的檔案到 public（只複製 build 結果，Node.js 不會進入正式映像）
COPY --from=node_builder /app/public/build ./public/build

# 安裝 Laravel 依賴
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 設定 storage 和 bootstrap/cache 目錄權限，讓 Laravel 可以寫入
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 預設啟動 php-fpm（FastCGI Process Manager，供 Nginx 代理 PHP 請求）
CMD ["php-fpm"]