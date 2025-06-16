# PhotoHub Platform

📸 PhotoHub — это микросервисная платформа на Laravel + Docker для загрузки, обработки и управления изображениями. Поддержка MinIO, RabbitMQ, Redis и AI-функциональности.

## Сервисы
- `auth-service`: регистрация и авторизация
- `media-service`: загрузка фото, очереди, MinIO
- `ml-service`: NSFW, auto-tags, обработка изображений (опц.)
- `nginx`: API Gateway

## Стек
- PHP 8.2 / Laravel 11
- Docker & Docker Compose
- MySQL, Redis, RabbitMQ
- MinIO (S3)
