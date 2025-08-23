# Hostinger Deployment Checklist

## ✅ Pre-Deployment (Local)
- [x] Generate application key: `php artisan key:generate`
- [x] Install production dependencies: `composer install --optimize-autoloader --no-dev`
- [x] Build assets: `npm run build`
- [x] Test application locally

## 📤 Upload to Hostinger
- [ ] Login to Hostinger Control Panel
- [ ] Navigate to File Manager
- [ ] Go to `public_html` directory
- [ ] Upload all Laravel files (except excluded folders)
- [ ] Create `.env` file with production settings

## ⚙️ Server Configuration
- [ ] Set PHP version to 8.1+ in Hostinger
- [ ] Enable required PHP extensions
- [ ] Create MySQL database
- [ ] Update database credentials in `.env`

## 🔧 Laravel Setup
- [ ] Run deployment script: `./deploy.sh`
- [ ] Set file permissions manually if needed
- [ ] Test website functionality
- [ ] Check error logs

## 🚀 Post-Deployment
- [ ] Configure domain/subdomain
- [ ] Set up SSL certificate
- [ ] Test all features
- [ ] Set up backups
- [ ] Monitor performance

## 📁 Files to Upload
```
✅ Upload these:
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── .htaccess
├── deploy.sh
└── DEPLOYMENT_GUIDE.md

❌ Don't upload these:
├── node_modules/
├── .git/
├── .env (create on server)
├── storage/logs/*.log
└── storage/framework/cache/*
```

## 🔑 Required .env Settings
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🛠️ Troubleshooting Commands
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan config:clear
php artisan cache:clear

# Check permissions
ls -la storage/
ls -la bootstrap/cache/

# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

## 📞 Support
- Hostinger Support: https://support.hostinger.com
- Laravel Documentation: https://laravel.com/docs
- Laravel Community: https://laracasts.com/discuss 