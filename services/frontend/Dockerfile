FROM node:22-alpine

# Рабочая директория внутри контейнера (совпадает с volume)
WORKDIR /app

# Устанавливаем зависимости, если вдруг понадобится
# (обычно node_modules монтируются тоже через volume)
# RUN npm ci

RUN apk add --no-cache \
    mc

# Открываем порт 3000 (для Vite)
EXPOSE 3000