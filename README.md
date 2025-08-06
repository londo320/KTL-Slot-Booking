# WM Slot Booking System

Laravel application for managing warehouse time slot bookings with Docker deployment support for Unraid.

## 🚀 Quick Deploy to Unraid

### Method 1: Community Applications (Recommended)

1. **Install Community Applications** plugin on your Unraid server
2. **Search for "WM Slot Booking"** in Apps tab
3. **Click Install** and configure:
   - Set your Git repository URL
   - Change default passwords
   - Set your Unraid IP in APP_URL
4. **Deploy** - The app auto-installs from Git!

### Method 2: Manual Template

1. **Add Container** in Unraid Docker tab
2. **Template**: Use `unraid-template.xml` from this repository
3. **Configure** variables and deploy

## 📋 Requirements

### Unraid Server
- Docker support enabled
- Community Applications plugin (for easy install)
- Ports available: 8080 (web), 3306 (database), 8081 (admin)

### Repository Setup
1. Fork/clone this repository
2. Push your customizations to your Git repository
3. Use your repository URL in the Unraid template

## 🔧 Configuration

### Environment Variables (Unraid Template)
- **GIT_REPO**: Your Git repository URL
- **APP_URL**: `http://YOUR-UNRAID-IP:8080`
- **MYSQL_ROOT_PASSWORD**: Secure database root password
- **MYSQL_PASSWORD**: Secure database user password

### First-Time Setup
The application automatically:
- ✅ Clones from Git repository
- ✅ Installs PHP dependencies
- ✅ Generates application key
- ✅ Runs database migrations
- ✅ Builds frontend assets
- ✅ Sets file permissions

## 🌐 Access Points

After deployment:
- **Web Application**: `http://YOUR-UNRAID-IP:8080`
- **Database Admin**: `http://YOUR-UNRAID-IP:8081` (Adminer)

## 🔄 Updates

The container automatically pulls updates from your Git repository on restart. To manually update:

```bash
# In Unraid console
cd /mnt/user/appdata/wm-slot-booking
docker-compose -f docker-compose.unraid.yml restart
```

## 🏗️ Development

### Local Development
See `CLAUDE.md` for detailed development setup instructions.

### Docker Stack
- **nginx**: Web server (Alpine Linux)
- **php**: Laravel application (PHP 8.2-FPM)
- **mysql**: Database server (MySQL 8.0)
- **redis**: Cache and sessions
- **adminer**: Database management interface

## 📁 Project Structure

```
├── src/                    # Laravel application
├── docker/                 # Docker configuration
│   ├── nginx/             # Nginx config
│   ├── php/               # PHP-FPM config
│   └── entrypoint.sh      # Auto-deployment script
├── docker-compose.unraid.yml  # Unraid Docker Compose
├── unraid-template.xml    # Unraid CA template
└── .env.unraid.example    # Environment template
```

## 🐛 Troubleshooting

### Check Container Logs
```bash
# All services
docker-compose -f docker-compose.unraid.yml logs

# Specific service
docker logs wm_php
docker logs wm_mysql
```

### Common Issues
- **Port conflicts**: Change ports in docker-compose.unraid.yml
- **Permission errors**: Check file ownership in `/mnt/user/appdata/`
- **Database connection**: Ensure MySQL container starts first

### Reset Application
```bash
cd /mnt/user/appdata/wm-slot-booking
docker-compose -f docker-compose.unraid.yml down -v
docker-compose -f docker-compose.unraid.yml up -d
```

## 🔒 Security Notes

- Change all default passwords in the template
- Use HTTPS in production (reverse proxy recommended)
- Set `APP_DEBUG=false` for production
- Regularly backup your database

## 📖 Documentation

- **Setup Guide**: `GIT_SETUP_GUIDE.md`
- **Development**: `CLAUDE.md`
- **Deployment**: `UNRAID_DEPLOYMENT.md`

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to your branch
5. Create a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

**Ready to deploy?** Just point the Unraid template to your Git repository and go! 🚀