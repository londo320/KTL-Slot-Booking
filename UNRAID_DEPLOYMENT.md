# WM Slot Booking - Unraid Git Deployment Guide

## Overview

Deploy the WM Slot Booking Laravel application directly from Git repository to Unraid using Docker containers.

## Prerequisites

- Unraid server with Docker support enabled
- SSH access to Unraid server
- Git installed on Unraid (via Nerd Pack plugin)
- Docker Compose installed (via Nerd Pack plugin)

## Quick Start (Automated)

1. **SSH into your Unraid server**

2. **Run the deployment script**:
   ```bash
   curl -sSL https://raw.githubusercontent.com/yourusername/wm-slot-booking/main/Scripts/deploy-unraid.sh | bash -s -- https://github.com/yourusername/wm-slot-booking.git
   ```

3. **Edit environment variables** when prompted:
   ```bash
   nano /mnt/user/appdata/wm_slot_booking/src/.env
   ```
   - Set `APP_URL=http://YOUR_UNRAID_IP:8080`
   - Set secure database passwords
   
4. **Access your application** at `http://YOUR_UNRAID_IP:8080`

## Manual Installation

### Step 1: Install Dependencies

Install required packages via **Nerd Pack Plugin**:
- Git
- Docker Compose

### Step 2: Clone Repository

```bash
cd /mnt/user/appdata/
git clone https://github.com/yourusername/wm-slot-booking.git wm_slot_booking
cd wm_slot_booking
```

### Step 3: Environment Configuration

```bash
# Copy environment template
cp .env.unraid.example src/.env

# Edit environment file
nano src/.env
```

**Required Changes**:
- `APP_URL=http://YOUR_UNRAID_IP:8080`
- `MYSQL_PASSWORD=your_secure_password`
- `MYSQL_ROOT_PASSWORD=your_secure_root_password`

### Step 4: Deploy with Docker

```bash
# Make scripts executable
chmod +x Scripts/deploy-unraid.sh
chmod +x docker/php/deploy-init.sh

# Deploy the application
docker-compose -f docker-compose.unraid.yml up -d --build
```

The deployment script automatically:
- Installs Composer dependencies
- Generates application key
- Runs database migrations
- Builds frontend assets
- Sets proper file permissions

## Application Access

- **Main Application**: http://YOUR_UNRAID_IP:8080
- **Database Management** (if enabled): http://YOUR_UNRAID_IP:8081

## Updates and Maintenance

### Updating from Git

```bash
cd /mnt/user/appdata/wm_slot_booking

# Pull latest changes
git pull origin main

# Restart containers to apply updates
docker-compose -f docker-compose.unraid.yml down
docker-compose -f docker-compose.unraid.yml up -d --build
```

### Database Backups

```bash
# Create database backup
docker exec wm_mysql mysqldump -u wm_user -p wm_slot_booking > backup_$(date +%Y%m%d).sql
```

## CA (Community Applications) Template

Add this template to your Unraid for easy management:

```xml
<?xml version="1.0"?>
<Container version="2">
  <Name>WM-Slot-Booking</Name>
  <Repository>nginx:alpine</Repository>
  <Registry>https://hub.docker.com/_/nginx</Registry>
  <Branch>
    <Tag>alpine</Tag>
  </Branch>
  <Network>bridge</Network>
  <MyIP/>
  <Shell>sh</Shell>
  <Privileged>false</Privileged>
  <Support/>
  <Project>https://github.com/yourusername/wm-slot-booking</Project>
  <Overview>WM Slot Booking System - Laravel application for managing time slot bookings</Overview>
  <Category>Productivity:</Category>
  <WebUI>http://[IP]:[PORT:8080]/</WebUI>
  <TemplateURL/>
  <Icon>https://laravel.com/img/favicon/favicon.ico</Icon>
  <ExtraParams>--restart unless-stopped</ExtraParams>
  <PostArgs/>
  <CPUset/>
  <DateInstalled>1609459200</DateInstalled>
  <DonateText/>
  <DonateLink/>
  <Requires>Git via Nerd Pack Plugin</Requires>
  <Config Name="HTTP Port" Target="80" Default="8080" Mode="tcp" Description="Web interface port" Type="Port" Display="always" Required="true" Mask="false">8080</Config>
  <Config Name="App Data" Target="/var/www/html" Default="/mnt/user/appdata/wm_slot_booking/src" Mode="rw" Description="Application files" Type="Path" Display="always" Required="true" Mask="false">/mnt/user/appdata/wm_slot_booking/src</Config>
</Container>
```

## Production Considerations

### Security
- Change all default passwords
- Remove database management containers in production
- Use HTTPS with reverse proxy (nginx-proxy-manager recommended)
- Set `APP_DEBUG=false` in `.env`

### Performance
- Enable Redis caching
- Configure queue workers for background jobs
- Set up log rotation

### Backups
- Schedule regular database backups
- Backup `/mnt/user/appdata/wm_slot_booking/` directory
- Consider using Unraid's built-in backup solutions

### Monitoring
- Monitor container logs: `docker logs wm_php`
- Check application logs in `src/storage/logs/`
- Monitor database performance

## Troubleshooting

### Container Issues
```bash
# Check container status
docker-compose -f docker-compose.unraid.yml ps

# View logs
docker logs wm_php
docker logs wm_mysql
docker logs wm_nginx
```

### Permission Issues
```bash
# Fix storage permissions
docker exec wm_php chown -R www-data:www-data /var/www/html/storage
docker exec wm_php chmod -R 775 /var/www/html/storage
```

### Database Connection Issues
```bash
# Test database connection
docker exec wm_php php artisan tinker
# Then run: DB::connection()->getPdo();
```

### Performance Issues
```bash
# Clear application cache
docker exec wm_php php artisan config:clear
docker exec wm_php php artisan cache:clear
docker exec wm_php php artisan view:clear
```

## Updating the Application

1. **Stop containers**:
   ```bash
   docker-compose -f docker-compose.unraid.yml down
   ```

2. **Backup current installation**

3. **Replace application files**

4. **Run migrations**:
   ```bash
   docker exec wm_php php artisan migrate --force
   ```

5. **Clear caches and restart**:
   ```bash
   docker exec wm_php php artisan config:clear
   docker-compose -f docker-compose.unraid.yml up -d
   ```

## Support

For issues specific to this deployment:
1. Check Unraid system logs
2. Check Docker container logs
3. Review Laravel logs in `src/storage/logs/`
4. Ensure all environment variables are properly set

## File Structure on Unraid

```
/mnt/user/appdata/wm_slot_booking/
├── src/                          # Laravel application
├── docker/                       # Docker configuration
├── docker-compose.unraid.yml     # Unraid Docker Compose
├── .env.unraid.example          # Environment template
├── UNRAID_DEPLOYMENT.md         # This file
├── database_backups/            # Database backups/imports
└── Scripts/                     # Utility scripts
```