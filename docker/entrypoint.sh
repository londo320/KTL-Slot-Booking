#!/bin/bash
set -e

echo "=== WM Slot Booking - Unraid Auto-Deploy ==="

# Environment defaults
GIT_REPO=${GIT_REPO:-"https://github.com/yourusername/wm-slot-booking.git"}
GIT_BRANCH=${GIT_BRANCH:-"main"}
APP_DIR="/config/wm-slot-booking"
AUTO_UPDATE=${AUTO_UPDATE:-"true"}

echo "Git Repository: $GIT_REPO"
echo "Branch: $GIT_BRANCH"
echo "App Directory: $APP_DIR"

# Create directory if it doesn't exist
mkdir -p /config

# Clone or update repository
if [ -d "$APP_DIR/.git" ]; then
    echo "Repository exists, updating..."
    cd "$APP_DIR"
    if [ "$AUTO_UPDATE" = "true" ]; then
        git fetch origin
        git reset --hard "origin/$GIT_BRANCH"
        git pull origin "$GIT_BRANCH"
        echo "Repository updated to latest $GIT_BRANCH"
    else
        echo "Auto-update disabled, using existing code"
    fi
else
    echo "Cloning repository..."
    git clone --branch "$GIT_BRANCH" "$GIT_REPO" "$APP_DIR"
    echo "Repository cloned successfully"
fi

# Change to app directory
cd "$APP_DIR"

# Create environment file from template
if [ ! -f "src/.env" ]; then
    echo "Creating environment file..."
    cp .env.unraid.example src/.env
    
    # Update environment with container variables
    if [ ! -z "$APP_URL" ]; then
        sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" src/.env
    fi
    if [ ! -z "$MYSQL_ROOT_PASSWORD" ]; then
        sed -i "s|MYSQL_ROOT_PASSWORD=.*|MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD|g" src/.env
    fi
    if [ ! -z "$MYSQL_PASSWORD" ]; then
        sed -i "s|MYSQL_PASSWORD=.*|MYSQL_PASSWORD=$MYSQL_PASSWORD|g" src/.env
        sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$MYSQL_PASSWORD|g" src/.env
    fi
    if [ ! -z "$APP_ENV" ]; then
        sed -i "s|APP_ENV=.*|APP_ENV=$APP_ENV|g" src/.env
    fi
    if [ ! -z "$APP_DEBUG" ]; then
        sed -i "s|APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|g" src/.env
    fi
    
    echo "Environment file configured"
fi

# Set proper permissions
chown -R $PUID:$PGID /config
chmod +x docker/php/deploy-init.sh 2>/dev/null || true

# Check if Docker Compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose not found, installing..."
    curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

# Start the application stack
echo "Starting application stack..."
docker-compose -f docker-compose.unraid.yml down 2>/dev/null || true
docker-compose -f docker-compose.unraid.yml up -d

echo "Waiting for services to start..."
sleep 30

# Check if services are running
if docker-compose -f docker-compose.unraid.yml ps | grep -q "Up"; then
    echo ""
    echo "✅ WM Slot Booking deployed successfully!"
    echo "🌐 Access your application at: $APP_URL"
    echo "🔧 Database admin: ${APP_URL//:8080/:8081}"
    echo ""
else
    echo "❌ Some services failed to start. Check logs with:"
    echo "   docker-compose -f docker-compose.unraid.yml logs"
fi

# Keep container running
echo "Setup complete. Keeping container alive..."
tail -f /dev/null