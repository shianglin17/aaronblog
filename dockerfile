# Dockerfile for Laravel + Vue 混合專案
# 假設前端已在本地建構完成

# === PHP 執行環境 ===
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

# 複製專案檔案（.dockerignore 會自動排除不需要的檔案）
COPY . .

# 建立必要目錄並設定權限
RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/app/database \
    && chown -R www-data:www-data bootstrap/cache storage

# 安裝 PHP 依賴
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 設定目錄權限（包含新的 database 目錄）
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

# 啟動 PHP-FPM
CMD ["php-fpm"]