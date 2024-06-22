# Sử dụng image PHP-FPM với phiên bản PHP 8.0
FROM php:8.0-fpm

# Cài đặt các tiện ích hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy tất cả các file vào container
COPY . .

# Cài đặt các gói PHP
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền ghi cho các thư mục cần thiết
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Lệnh khởi động dịch vụ PHP-FPM
CMD ["php-fpm"]
