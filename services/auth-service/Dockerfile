FROM php:8.2-fpm-alpine

# Устанавливаем необходимые зависимости и расширения
RUN apk add --no-cache \
    git \
    icu-dev \
    zlib-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    redis

# PHP-расширения для Laravel
RUN docker-php-ext-install intl pdo pdo_mysql mbstring zip xml gd

# Устанавливаем Composer (забираем из официального образа)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Рабочая директория (куда монтируется твой volume)
WORKDIR /var/www

EXPOSE 9000

# Запуск php-fpm
CMD ["php-fpm"]
