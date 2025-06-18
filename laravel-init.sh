#!/bin/bash

# Массив имён контейнеров (auth_service, user_service и т.д.)
CONTAINERS=("auth_service" "user_service" "media_service")
WORKDIR="/var/www"  # Корень, куда монтируется volume внутри контейнера

for CONTAINER in "${CONTAINERS[@]}"
do
  # Проверяем, есть ли уже проект (по наличию artisan)
  if docker exec -i "$CONTAINER" [ -f "$WORKDIR/artisan" ]; then
    echo "Laravel уже установлен в $CONTAINER, пропускаем..."
  else
    echo "Создаём новый Laravel проект в контейнере $CONTAINER"
    docker exec -i "$CONTAINER" composer create-project laravel/laravel "$WORKDIR"
    echo "✅ Laravel создан в $CONTAINER"
  fi
done
