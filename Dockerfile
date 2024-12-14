FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev \
    libmagickwand-dev \
    sendmail \
    zip \
    unzip \
    git \
    curl && \
    docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd && \
    pecl install imagick && \
    docker-php-ext-enable imagick

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www/html

# Копирование проекта
COPY . .

# Убедитесь, что права настроены правильно
RUN chown -R www-data:www-data /var/www/html

# Запуск sendmail при старте контейнера
CMD ["php-fpm"]
