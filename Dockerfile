FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate autoloader and run scripts
RUN composer dump-autoload --optimize

# Install and build npm dependencies
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www 