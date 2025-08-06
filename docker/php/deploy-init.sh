#!/bin/bash
set -e

echo "Starting deployment initialization..."

# Wait for MySQL to be ready
echo "Waiting for database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "Database not ready, waiting 5 seconds..."
    sleep 5
done

echo "Database connection established!"

# Install/update Composer dependencies
echo "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Generate app key if needed
if ! grep -q "APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and rebuild caches
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install NPM dependencies and build assets
echo "Building frontend assets..."
npm ci --production
npm run build

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Deployment initialization complete!"

# Start PHP-FPM
exec php-fpm