FROM php:8.2-fpm

# Установка необходимых расширений PHP и системных пакетов
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Установка переменных окружения
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Никакие файлы не копируются! Код монтируется через volumes в docker-compose.yml

# Открываем порт для сервера (если нужно)
EXPOSE 9001

CMD ["php-fpm"]