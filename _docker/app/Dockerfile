FROM php:8.5-fpm

# Устанавливаем зависимости и необходимые библиотеки
RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      libicu-dev \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      zip unzip \
      git \
      nodejs \
      pkg-config && \
    # Устанавливаем PHP-расширения
    docker-php-ext-install pdo_mysql bcmath gd zip intl && \
    # Очищаем кеш apt для уменьшения размера образа
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Устанавливаем Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Копируем конфигурацию PHP
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Устанавливаем Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Устанавливаем рабочую директорию
WORKDIR /var/www
