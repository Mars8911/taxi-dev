# 第一階段：使用 Node 環境編譯前端 (Vite)
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY . .
RUN npm install && npm run build

# 第二階段：使用 PHP 8.4 環境執行 Laravel
FROM php:8.4-fpm-alpine

# 安裝必要系統套件與 PHP 擴展
RUN apk add --no-cache nginx wget git unzip \
    && docker-php-ext-install bcmath pdo_mysql

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# 從第一階段把編譯好的前端資源抓過來
COPY --from=frontend-builder /app/public/build ./public/build

# 強制安裝 Composer 套件（無視平台要求，確保 Laravel 12 能跑）
RUN composer install --ignore-platform-reqs --no-interaction --optimize-autoloader

# 設定資料夾權限
RUN chown -R www-data:www-data storage bootstrap/cache

# 暴露 8000 埠口並啟動服務
EXPOSE 8000
CMD ["sh", "-c", "php artisan config:clear && php artisan migrate --force ; php artisan serve --host=0.0.0.0 --port=8000"]
# 修正新檔案名稱
