# WM Slot Booking - Development Setup

## Multi-Machine Development Solutions

⚠️ **OneDrive Issues**: OneDrive corrupts `vendor/` and `node_modules/` directories causing I/O errors and file corruption.

**Recommended approach**: Use Git for version control instead of OneDrive sync.

### Project Structure
- **Laravel Backend**: `/src` directory
- **Database**: MySQL via Docker (excluded from sync)
- **Frontend**: Vite + Tailwind CSS

### Environment Configuration

1. **Docker Development** (Default):
   - Uses `docker-compose.yml` 
   - Database: MySQL container (`wm_mysql`)
   - Access: http://localhost:9080

2. **Local Development**:
   - Copy `.env.local.example` to `.env.local`
   - Uses SQLite database
   - Run: `cd src && php artisan serve`

### OneDrive Sync Exclusions

The `.gitignore` file excludes:
- Database files (`database/`, `*.sqlite`)
- Dependencies (`node_modules/`, `vendor/`)
- Environment files (`.env`, `.env.local`)
- Cache/log files
- Machine-specific files (`autostart`, `desktop.ini`)

### Quick Start Scripts

**🚀 RECOMMENDED: Git-Based Development**
```bash
# Setup Git repository (run once)
setup-git.bat

# On other machines: 
# 1. git clone YOUR_REPOSITORY_URL
# 2. setup.bat
```

**🔧 OneDrive Alternatives:**
```bash
# Option 1: Local development (OneDrive-free)
setup-local.bat

# Option 2: Symlink dependencies (Advanced)
setup-symlink.bat (Run as Administrator)

# Option 3: Original OneDrive (Not recommended)
setup.bat
```

**⚡ Daily Development:**
```bash
# Windows
start-dev.bat

# Linux/Mac  
./start-dev.sh
```

**🔄 Database Sync:**
```bash
# Windows
sync-db.bat

# Linux/Mac
./sync-db.sh
```

### Manual Commands (if needed)
```bash
# Docker setup
docker-compose up -d

# Local setup (from src directory)
composer install
npm install
php artisan migrate
php artisan serve

# Development with hot reload
npm run dev
```

### Database Management
- **phpMyAdmin**: http://localhost:6081
- **Adminer**: http://localhost:6080
- **Local SQLite**: Located at `src/database/database.sqlite`

### Multi-Machine Workflow

**Code Sync:** Automatic via OneDrive
**Database Sync:** Manual using `sync-db` script

**Setting up a new machine:**
1. Wait for OneDrive sync to complete
2. Run `setup.bat` (Windows) or `./setup.sh` (Linux/Mac)
3. Start developing with `start-dev.bat` or `./start-dev.sh`

**Syncing database changes:**
1. **Export on main machine:** Run `sync-db` → option 1 (Export)
2. **Import on other machines:** Run `sync-db` → option 2 (Import)
3. SQL backups sync via OneDrive in `database_backups/` folder

**Each machine maintains independently:**
- Database state (until manually synced)
- Environment configuration
- Dependencies (vendor/, node_modules/)
- Generated assets

### Troubleshooting

**Dependencies Issues (OneDrive Sync Corruption):**
```bash
# If you get vendor/autoload.php errors:
docker-compose exec wm_php rm -rf vendor composer.lock
docker-compose exec wm_php composer install --no-scripts --no-interaction

# If composer fails with I/O errors, restart containers:
docker-compose down
docker-compose up -d
# Wait 30 seconds, then retry composer install
```

**General Setup:**
- Run `composer install` and `npm install` on each new machine
- Copy `.env.example` to `.env` and configure for your setup
- For Docker: Ensure ports 9080, 6080, 6081, 6082 are available

**OneDrive Sync Issues:**
- The `vendor/` directory is excluded from sync (in .gitignore)
- Dependencies must be reinstalled on each machine
- If you see file corruption errors, restart Docker containers