# 🚀 Laravel Docker Production Setup

Setup Docker **production-ready** untuk Laravel dengan Vite yang berjalan **sekali jalan otomatis**.

## 🎯 Features Production

✅ **Auto Setup Lengkap** - Migrate, seed, cache optimization  
✅ **Database Driver** - Session, cache, queue menggunakan database  
✅ **OPcache & JIT** - PHP optimization untuk performa maksimal  
✅ **Redis Support** - Ready untuk cache & session redis  
✅ **Queue Worker** - Supervisor untuk background jobs (optional)  
✅ **MySQL Optimized** - Custom MySQL config untuk production  
✅ **Nginx Tuned** - Gzip, cache headers, security headers  
✅ **PHP-FPM Tuned** - Process manager optimized  
✅ **Security Hardened** - File permissions, nginx security, PHP security  

## 📋 Prasyarat

- Docker & Docker Compose terinstall
- Port 80 dan 3306 tersedia
- Minimal 2GB RAM
- 10GB disk space

## 🎯 Quick Start (Satu Command!)

```bash
# 1. Copy dan edit .env
cp .env.example .env
nano .env  # WAJIB edit password!

# 2. Build dan run (SEKALI JALAN!)
docker-compose up --build -d

# 3. Tunggu ~60 detik, lalu cek logs
docker-compose logs -f app
```

## ✅ Apa yang Otomatis Terjadi?

1. ✅ **Build Vite Assets** → Compile ke `public/build`
2. ✅ **Install Composer** → Dependencies vendor
3. ✅ **Create .env** → Dari .env.example (jika belum ada)
4. ✅ **Generate APP_KEY** → Laravel encryption key
5. ✅ **Wait Database** → Tunggu MySQL healthy
6. ✅ **Create Tables** → session, cache, queue tables
7. ✅ **Run Migrations** → `php artisan migrate --force`
8. ✅ **Storage Link** → Symbolic link storage
9. ✅ **Cache Optimize** → Config, route, view cache
10. ✅ **Set Permissions** → Storage & cache folders
11. ✅ **OPcache Ready** → PHP bytecode cache enabled
12. ✅ **Health Check** → Auto-restart jika unhealthy

## 🌐 Akses Aplikasi

```
http://localhost
```

## 📁 Struktur File Production

```
project/
├── docker/
│   ├── mysql/
│   │   └── my.cnf                 # MySQL optimization
│   ├── nginx/
│   │   └── default.conf           # Nginx config
│   ├── php/
│   │   ├── custom.ini             # PHP settings
│   │   └── opcache.ini            # OPcache optimization
│   ├── php-fpm/
│   │   └── www.conf               # PHP-FPM pool config
│   ├── supervisor/
│   │   └── laravel-worker.conf    # Queue worker (optional)
│   ├── entrypoint.sh              # Startup script
│   ├── wait-for-db.sh             # DB readiness check
│   └── check-env.sh               # ENV validation
├── Dockerfile                      # Multi-stage production build
├── docker-compose.yml              # Orchestration
├── .dockerignore                   # Build exclusions
├── .env.example                    # Environment template
├── setup.sh                        # Setup automation
└── Makefile                        # Helper commands
```

## ⚙️ Konfigurasi Production

### Environment Variables (.env)

**WAJIB diubah untuk production:**

```env
# Security - GANTI PASSWORD!
MYSQL_ROOT_PASSWORD=your_strong_root_password_here
MYSQL_PASSWORD=your_strong_user_password_here

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database drivers untuk performa
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

### Database Configuration

File: `docker/mysql/my.cnf`
- Optimized untuk production
- Character set UTF8MB4
- InnoDB buffer pool 512MB
- Slow query log enabled
- Max connections: 200

### PHP Configuration

**custom.ini** - Memory, upload, timezone, security
**opcache.ini** - Bytecode cache + JIT compiler
**www.conf** - Process management (pm=dynamic, max_children=50)

### Nginx Configuration

- Gzip compression enabled
- Cache headers untuk static assets
- Security headers (XSS, Frame, Content-Type)
- PHP execution prevented di storage
- 50MB max upload size

## 📊 Monitoring & Performance

### Health Checks

```bash
# Check container health
docker-compose ps

# Check app health (PHP-FPM)
docker-compose exec app php-fpm -t

# Check database health
docker-compose exec db mysqladmin ping -u root -p

# Check OPcache status
docker-compose exec app php -i | grep opcache
```

### Performance Metrics

```bash
# PHP-FPM status
docker-compose exec app php-fpm -tt

# MySQL slow queries
docker-compose exec db tail -f /var/log/mysql/slow.log

# Nginx access logs
docker-compose logs nginx | tail -100
```

## 📝 Command Berguna

### Menggunakan Makefile

```bash
make help          # Lihat semua command
make logs          # Lihat logs
make shell         # Masuk ke container
make restart       # Restart containers
make migrate       # Run migrations
make fresh         # Fresh migrate + seed
make clean         # Hapus semua (hati-hati!)
```

### Command Docker Compose

```bash
# Lihat logs real-time
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db

# Masuk ke container
docker-compose exec app bash
docker-compose exec db mysql -u root -p

# Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan queue:work
docker-compose exec app php artisan cache:clear

# Restart services
docker-compose restart app
docker-compose restart nginx

# Stop/Start
docker-compose down
docker-compose up -d

# Rebuild
docker-compose up --build -d
```

## 🔧 Advanced Configuration

### Enable Queue Worker

Edit `docker/entrypoint.sh`, uncomment:
```bash
# Start supervisor for queue workers
supervisord -c /etc/supervisor/supervisord.conf
```

Atau jalankan manual:
```bash
docker-compose exec app php artisan queue:work --daemon
```

### Enable Redis Cache

1. Tambah service Redis di `docker-compose.yml`:
```yaml
redis:
  image: redis:7-alpine
  container_name: acadeasy-redis
  restart: unless-stopped
  networks:
    - acadeasy_net
  volumes:
    - redis_data:/data
```

2. Update `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

3. Rebuild:
```bash
docker-compose up --build -d
```

### Enable Cron/Scheduler

Add ke `docker/entrypoint.sh`:
```bash
# Setup cron for Laravel scheduler
echo "* * * * * www-data cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab
cron
```

### SSL/HTTPS Setup

1. Update Nginx config untuk SSL
2. Mount SSL certificates:
```yaml
volumes:
  - ./ssl:/etc/nginx/ssl:ro
```

3. Update ports:
```yaml
ports:
  - "443:443"
```

## 🐛 Troubleshooting

### Container tidak start

```bash
# Cek status
docker-compose ps

# Cek logs
docker-compose logs app
docker-compose logs db
```

### Database connection error

```bash
# Pastikan MySQL sudah ready
docker-compose exec db mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "SHOW DATABASES;"

# Restart app container
docker-compose restart app
```

### Permission error

```bash
# Fix permissions dari dalam container
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Rebuild from scratch

```bash
# Hapus semua dan mulai fresh
docker-compose down -v
docker system prune -af
docker-compose up --build -d
```

## 📁 Struktur File

```
project/
├── docker/
│   ├── nginx/
│   │   └── default.conf        # Nginx config
│   ├── entrypoint.sh           # Startup script (auto migrate, etc)
│   └── wait-for-db.sh          # Database readiness check
├── Dockerfile                  # Multi-stage Docker build
├── docker-compose.yml          # Docker Compose config
├── .dockerignore              # Files to exclude from build
├── .env.example               # Environment template
├── setup.sh                   # Setup script (optional)
└── Makefile                   # Helper commands (optional)
```

## 🔧 Kustomisasi

### Ubah Port

Edit `.env`:
```
APP_PORT=8080
```

Lalu restart:
```bash
docker-compose down
docker-compose up -d
```

### Tambah Redis

Edit `docker-compose.yml`, tambahkan service redis, lalu update `.env`:
```
CACHE_DRIVER=redis
REDIS_HOST=redis
```

### Database Seeding

Uncomment di `docker/entrypoint.sh`:
```bash
# Seed database
php artisan db:seed --force --no-interaction
```

## 🎯 Production Checklist

- [ ] Update `APP_KEY` dengan value unik
- [ ] Set `APP_DEBUG=false`
- [ ] Gunakan password database yang kuat
- [ ] Update `APP_URL` dengan domain production
- [ ] Setup SSL/HTTPS di Nginx
- [ ] Enable Redis untuk cache & session
- [ ] Setup backup database
- [ ] Monitor logs & resources

## 💡 Tips

1. **First time setup**: Tunggu ~30-60 detik untuk semua service ready
2. **Logs**: Selalu cek logs jika ada masalah dengan `make logs`
3. **Clean build**: Gunakan `--no-cache` jika ada masalah: `docker-compose build --no-cache`
4. **Storage**: Folder `storage/` di-mount ke host untuk persistence data

## 📞 Support

Jika ada masalah:
1. Cek logs: `docker-compose logs -f app`
2. Cek container status: `docker-compose ps`
3. Rebuild: `docker-compose up --build -d`

---

**Happy Coding! 🚀**