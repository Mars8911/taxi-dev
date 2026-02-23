FROM php:8.4-fpm-alpine

# 安裝系統套件、PHP 擴展以及 Node.js (含 npm)
RUN apk add --no-cache nginx wget git unzip nodejs npm \
    && docker-php-ext-install bcmath pdo_mysql

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# 強制安裝 Composer 套件（無視平台要求）
RUN composer install --ignore-platform-reqs --no-interaction --optimize-autoloader

# 安裝 NPM 套件並編譯前端資源 (Vite)
RUN npm install && npm run build

# 設定資料夾權限
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
