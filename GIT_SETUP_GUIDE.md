# WM Slot Booking - Git Repository Setup & Unraid Deployment

## Step 1: Create Git Repository

### A. On GitHub (or GitLab/Bitbucket)

1. **Go to GitHub.com** and sign in
2. **Click "New Repository"**
3. **Repository Settings**:
   - Name: `wm-slot-booking`
   - Description: `Laravel application for managing warehouse slot bookings`
   - Visibility: Private (recommended) or Public
   - ✅ Initialize with README
   - ✅ Add .gitignore: Choose "Laravel"
   - License: MIT (optional)

### B. Push Your Code

1. **Open terminal** in your project directory
2. **Initialize Git** (if not already):
   ```bash
   git init
   git add .
   git commit -m "Initial commit: WM Slot Booking application"
   ```

3. **Connect to remote repository**:
   ```bash
   git remote add origin https://github.com/yourusername/wm-slot-booking.git
   git branch -M main
   git push -u origin main
   ```

## Step 2: Prepare Repository for Unraid

### A. Ensure Required Files Exist

Your repository should have these files in the root:
- ✅ `docker-compose.unraid.yml` - Docker configuration for Unraid
- ✅ `.env.unraid.example` - Environment template
- ✅ `docker/php/deploy-init.sh` - Auto-setup script
- ✅ `docker/nginx/default.conf` - Nginx configuration

### B. Update Environment Template

Make sure `.env.unraid.example` has production settings:
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=wm_mysql
REDIS_HOST=wm_redis
```

## Step 3: Deploy on Unraid

### Method 1: Community Applications Template (Recommended)

1. **Install Community Applications** plugin on Unraid
2. **Go to Apps tab** → **Previous Apps** → **+ Add Container**
3. **Use this template**:

**Container Settings:**
- **Name**: `WM-Slot-Booking`
- **Repository**: `ghcr.io/linuxserver/code-server:latest` (temporary)
- **Console shell command**: `Bash`
- **Network Type**: `Custom: br0` or `bridge`

**Port Mappings:**
- **Container Port**: `80` → **Host Port**: `8080` (Web Interface)
- **Container Port**: `3306` → **Host Port**: `3306` (MySQL - optional)

**Volume Mappings:**
- **Container Path**: `/config` → **Host Path**: `/mnt/user/appdata/wm-slot-booking`

**Environment Variables:**
- **GIT_REPO**: `https://github.com/yourusername/wm-slot-booking.git`
- **GIT_BRANCH**: `main`

### Method 2: Manual Installation

1. **SSH into Unraid**
2. **Create app directory**:
   ```bash
   mkdir -p /mnt/user/appdata/wm-slot-booking
   cd /mnt/user/appdata/wm-slot-booking
   ```

3. **Clone repository**:
   ```bash
   git clone https://github.com/yourusername/wm-slot-booking.git .
   ```

4. **Setup environment**:
   ```bash
   cp .env.unraid.example src/.env
   nano src/.env  # Edit with your settings
   ```

5. **Deploy with Docker**:
   ```bash
   chmod +x docker/php/deploy-init.sh
   docker-compose -f docker-compose.unraid.yml up -d
   ```

## Step 4: Access Your Application

- **Web Interface**: `http://UNRAID-IP:8080`
- **First-time setup**: The app will automatically:
  - Install dependencies
  - Run database migrations
  - Generate app key
  - Build frontend assets

## Step 5: Updates

To update your application:

```bash
cd /mnt/user/appdata/wm-slot-booking
git pull origin main
docker-compose -f docker-compose.unraid.yml restart
```

## Troubleshooting

### Common Issues

1. **Port conflicts**: Change `8080:80` to another port in docker-compose.unraid.yml
2. **Permission errors**: Run `chmod -R 755 /mnt/user/appdata/wm-slot-booking`
3. **Database connection**: Ensure MySQL container is running first

### Check Logs

```bash
# Application logs
docker logs wm_php

# Database logs  
docker logs wm_mysql

# All services
docker-compose -f docker-compose.unraid.yml logs
```

## Template for Unraid Community Applications

Save this as `wm-slot-booking.xml`:

```xml
<?xml version="1.0"?>
<Container version="2">
  <Name>WM-Slot-Booking</Name>
  <Repository>wm-slot-booking</Repository>
  <Registry>https://github.com/yourusername/wm-slot-booking</Registry>
  <Network>bridge</Network>
  <MyIP/>
  <Shell>bash</Shell>
  <Privileged>false</Privileged>
  <Support>https://github.com/yourusername/wm-slot-booking/issues</Support>
  <Project>https://github.com/yourusername/wm-slot-booking</Project>
  <Overview>Laravel application for managing warehouse time slot bookings. Automatically pulls from Git repository and sets up the complete application stack.</Overview>
  <Category>Productivity:</Category>
  <WebUI>http://[IP]:[PORT:8080]/</WebUI>
  <TemplateURL>https://raw.githubusercontent.com/yourusername/wm-slot-booking/main/unraid-template.xml</TemplateURL>
  <Icon>https://laravel.com/img/logomark.min.svg</Icon>
  <ExtraParams/>
  <PostArgs/>
  <CPUset/>
  <DateInstalled>1691234567</DateInstalled>
  <DonateText/>
  <DonateLink/>
  <Requires>Docker Compose Plugin</Requires>
  <Config Name="Web Port" Target="80" Default="8080" Mode="tcp" Description="Web interface access port" Type="Port" Display="always" Required="true" Mask="false">8080</Config>
  <Config Name="Data Path" Target="/config" Default="/mnt/user/appdata/wm-slot-booking" Mode="rw" Description="Application data and configuration" Type="Path" Display="advanced" Required="true" Mask="false">/mnt/user/appdata/wm-slot-booking</Config>
  <Config Name="Git Repository" Target="GIT_REPO" Default="https://github.com/yourusername/wm-slot-booking.git" Mode="" Description="Git repository URL" Type="Variable" Display="always" Required="true" Mask="false">https://github.com/yourusername/wm-slot-booking.git</Config>
  <Config Name="Git Branch" Target="GIT_BRANCH" Default="main" Mode="" Description="Git branch to deploy" Type="Variable" Display="advanced" Required="false" Mask="false">main</Config>
  <Config Name="MySQL Root Password" Target="MYSQL_ROOT_PASSWORD" Default="" Mode="" Description="MySQL root password (set a secure password)" Type="Variable" Display="always" Required="true" Mask="true"/>
  <Config Name="App URL" Target="APP_URL" Default="http://localhost:8080" Mode="" Description="Full URL where app will be accessible" Type="Variable" Display="always" Required="true" Mask="false">http://localhost:8080</Config>
</Container>
```

That's it! Your Laravel app will now deploy automatically from Git to Unraid just like any other container app.