FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set memory limit
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy all files
COPY . .

# Install composer dependencies
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --ignore-platform-reqs

# Build frontend
RUN npm install && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force; php artisan db:seed --force; php artisan serve --host=0.0.0.0 --port=$PORT
