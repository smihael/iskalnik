FROM php:7.4-apache

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		git \
		unzip \
		libicu-dev \
		libonig-dev \
		libzip-dev \
		libxml2-dev \
	&& docker-php-ext-install \
		intl \
		mbstring \
		mysqli \
		pdo \
		pdo_mysql \
		xml \
		zip \
	&& rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite

# Enable AllowOverride for /var/www/html to allow .htaccess
RUN sed -i '/<Directory \/var\/www\/html>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Keep Apache defaults; code is mounted at runtime via docker-compose.
