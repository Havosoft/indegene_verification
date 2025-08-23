# Laravel Project Deployment Guide for Hostinger

## Prerequisites
- Hostinger hosting account with PHP 8.1+ support
- Domain name (optional, can use subdomain)
- FTP/SFTP access or File Manager access

## Step 1: Prepare Your Project (Already Done)

✅ Application key generated  
✅ Dependencies optimized for production  
✅ Assets built for production  

## Step 2: Upload Files to Hostinger

### Option A: Using File Manager (Recommended for beginners)

1. **Login to Hostinger Control Panel**
   - Go to your Hostinger dashboard
   - Navigate to "File Manager"

2. **Navigate to Public HTML Directory**
   - Go to `public_html` folder
   - This is your website's root directory

3. **Upload Laravel Files**
   - Upload ALL files from your Laravel project EXCEPT:
     - `node_modules/` folder
     - `.git/` folder
     - `.env` file (we'll create this on the server)
     - `storage/logs/` folder contents
     - `storage/framework/cache/` folder contents

4. **Set Proper Permissions**
   - Set `storage/` folder to 755
   - Set `bootstrap/cache/` folder to 755
   - Set `public/` folder to 755

### Option B: Using FTP/SFTP

1. **Connect via FTP Client**
   - Use FileZilla, WinSCP, or any FTP client
   - Connect to your Hostinger FTP server
   - Navigate to `public_html`

2. **Upload Files**
   - Upload all Laravel files to the root of `public_html`
   - Exclude the same folders as mentioned above

## Step 3: Configure Laravel for Hostinger

### Create .env File

Create a `.env` file in your project root with these settings:

```env
APP_NAME="Indegene Verification Platform"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Database Setup

1. **Create Database in Hostinger**
   - Go to "Databases" in Hostinger control panel
   - Create a new MySQL database
   - Note down database name, username, and password

2. **Update .env File**
   - Replace database credentials in `.env` file

3. **Run Migrations**
   - Access your site via SSH or use Hostinger's Terminal
   - Run: `php artisan migrate`

## Step 4: Configure Web Server

### Option A: Using .htaccess (Already Done)
The `.htaccess` file has been optimized for Hostinger.

### Option B: Using Hostinger's PHP Configuration
1. Go to "PHP Configuration" in Hostinger
2. Set PHP version to 8.1 or higher
3. Enable required extensions:
   - `fileinfo`
   - `openssl`
   - `pdo`
   - `mbstring`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`

## Step 5: Set File Permissions

Run these commands via SSH or File Manager:

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chmod 644 .env
```

## Step 6: Final Configuration

1. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

2. **Optimize for Production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Step 7: Test Your Application

1. Visit your domain: `https://yourdomain.com`
2. Test all major functionality
3. Check error logs if issues occur

## Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check file permissions
   - Verify `.env` file exists and is readable
   - Check error logs in `storage/logs/`

2. **Database Connection Error**
   - Verify database credentials in `.env`
   - Ensure database exists and is accessible

3. **Asset Loading Issues**
   - Verify `public/build/` folder exists
   - Check if assets were properly built

4. **Permission Denied**
   - Set proper permissions on `storage/` and `bootstrap/cache/`

### Error Logs Location:
- Laravel logs: `storage/logs/laravel.log`
- Server logs: Check Hostinger's error log section

## Security Recommendations

1. **Keep .env file secure**
   - Never commit `.env` to version control
   - Set proper file permissions (644)

2. **Regular Updates**
   - Keep Laravel and dependencies updated
   - Monitor security advisories

3. **Backup Strategy**
   - Regular database backups
   - File system backups
   - Use Hostinger's backup features

## Performance Optimization

1. **Enable Caching**
   - Use Redis if available
   - Configure file caching

2. **CDN Integration**
   - Use Hostinger's CDN for static assets
   - Configure asset optimization

3. **Database Optimization**
   - Use database indexing
   - Optimize queries

## Support

If you encounter issues:
1. Check Laravel documentation
2. Review Hostinger's knowledge base
3. Contact Hostinger support
4. Check Laravel community forums

---

**Note:** This guide assumes you're using Hostinger's shared hosting. For VPS or dedicated hosting, some steps may vary. 