FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www/html

# Копирование проекта
COPY . .

# Убедитесь, что права настроены правильно
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]
