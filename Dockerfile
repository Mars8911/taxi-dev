FROM php:8.4-fpm-alpine

# 安裝系統相依套件與 PHP 擴展
RUN apk add --no-cache nginx wget git unzip \
    && docker-php-ext-install bcmath pdo_mysql

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# 強制安裝，無視版本
RUN composer install --ignore-platform-reqs --no-interaction --optimize-autoloader
RUN npm install && npm run build

# 設定權限
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
