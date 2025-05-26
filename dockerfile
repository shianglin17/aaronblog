# Dockerfile for Laravel + Vue 混合專案
# 採用多階段建構：Node.js 建構前端，PHP 運行後端

# === 第一階段：前端建構 ===
FROM node:20-alpine as node_builder

WORKDIR /app

# 複製前端相關檔案
COPY package*.json vite.config.js ./
COPY resources ./resources

# 安裝依賴並建構前端資源
RUN npm install && npm run build

# === 第二階段：後端執行環境 ===
FROM php:8.2-fpm

# 安裝系統依賴與 PHP 擴充
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    sqlite3 \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# 設定工作目錄
WORKDIR /var/www

# 複製專案檔案
COPY . .

# 複製前端建構結果
COPY --from=node_builder /app/public/build ./public/build

# 安裝 PHP 依賴
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 設定目錄權限
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

# 啟動 PHP-FPM
CMD ["php-fpm"]