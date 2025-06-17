#!/bin/bash

# Скрипт установки Laravel через Docker и проверки, установлен ли Laravel

set -e

# Путь к директории с вашим проектом Laravel
LARAVEL_DIR=${1:-/var/www/html}

# Проверка, установлен ли Laravel (наличие artisan)
if [ -f "$LARAVEL_DIR/artisan" ]; then
    echo "Laravel уже установлен в $LARAVEL_DIR"
    exit 0
fi

echo "Laravel не найден. Начинаю установку..."

# Установка Laravel через Composer внутри контейнера Docker
docker run --rm \
    -v "$PWD":"$LARAVEL_DIR" \
    -w "$LARAVEL_DIR" \
    composer create-project laravel/laravel .

echo "Laravel успешно установлен в $LARAVEL_DIR"