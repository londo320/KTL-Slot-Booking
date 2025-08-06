#!/bin/bash
set -e

echo "=== KTL Slot Booking - Single Container Startup ==="

# Start MySQL
service mysql start
sleep 5

# Set up MySQL database
mysql -e "CREATE DATABASE IF NOT EXISTS ktl_slot_booking;"
mysql -e "CREATE USER IF NOT EXISTS 'ktl_user'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD:-ktl_password123}';"
mysql -e "GRANT ALL PRIVILEGES ON ktl_slot_booking.* TO 'ktl_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Start Redis
service redis-server start

# Clone/update application
if [ ! -d "/var/www/html/app" ]; then
    echo "Cloning application..."
    git clone -b ${GIT_BRANCH:-Bookings} ${GIT_REPO:-https://github.com/londo320/KTL-Slot-Booking.git} /tmp/app
    cp -r /tmp/app/src/* /var/www/html/
    cp /tmp/app/.env.unraid.example /var/www/html/.env
    rm -rf /tmp/app
else
    echo "Application already exists"
fi

# Configure environment
sed -i "s|APP_URL=.*|APP_URL=${APP_URL:-http://localhost}|g" /var/www/html/.env
sed -i "s|DB_HOST=.*|DB_HOST=localhost|g" /var/www/html/.env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=ktl_slot_booking|g" /var/www/html/.env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=ktl_user|g" /var/www/html/.env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${MYSQL_PASSWORD:-ktl_password123}|g" /var/www/html/.env
sed -i "s|REDIS_HOST=.*|REDIS_HOST=localhost|g" /var/www/html/.env

# Install dependencies
cd /var/www/html
composer install --optimize-autoloader --no-dev --no-interaction
npm install --production
npm run build

# Generate app key if needed
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Set permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 storage bootstrap/cache

echo "✅ Application ready!"
echo "🌐 Access at: ${APP_URL:-http://localhost}"

# Start supervisor to manage all services
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf