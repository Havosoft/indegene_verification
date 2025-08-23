# Setting Up Cron Jobs on Hostinger for Laravel

## 🚀 Essential Cron Jobs for Your Laravel Application

Your Laravel application **requires cron jobs** to function properly in production, especially for:
- Processing queued emails (staff welcome emails, notifications)
- Running scheduled maintenance tasks
- Processing background jobs

## 📋 Step-by-Step Hostinger Cron Setup

### 1. Access Hostinger Control Panel
1. Log into your **Hostinger account**
2. Go to **Hosting** → Select your domain
3. Navigate to **Advanced** → **Cron Jobs**

### 2. Required Cron Jobs

#### **Essential: Queue Worker (For Email Processing)**
```bash
# Command:
/usr/bin/php /home/yourusername/public_html/artisan queue:work --stop-when-empty

# Schedule: Every minute
* * * * *
```

#### **Recommended: Laravel Scheduler**
```bash
# Command:
/usr/bin/php /home/yourusername/public_html/artisan schedule:run

# Schedule: Every minute
* * * * *
```

#### **Optional: User Reassignment (Weekly)**
```bash
# Command:
/usr/bin/php /home/yourusername/public_html/artisan users:reassign-staff --force

# Schedule: Every Sunday at 2 AM
0 2 * * 0
```

### 3. Hostinger-Specific Setup Instructions

#### **Step 1: Find Your PHP Path**
Hostinger typically uses:
- `/usr/bin/php` (most common)
- `/usr/bin/php8.2` (specific PHP version)
- `/opt/alt/php82/usr/bin/php` (alternative path)

#### **Step 2: Find Your Project Path**
Replace `/home/yourusername/public_html/` with your actual path:
- **Subdomain**: `/home/yourusername/domains/subdomain.yourdomain.com/public_html/`
- **Main domain**: `/home/yourusername/public_html/`
- **Subfolder**: `/home/yourusername/public_html/yourfolder/`

#### **Step 3: Create Cron Jobs in Hostinger**

1. **Add Queue Worker Cron Job:**
   - **Command**: `/usr/bin/php /home/yourusername/public_html/artisan queue:work --stop-when-empty`
   - **Schedule**: `* * * * *` (every minute)
   - **Description**: "Laravel Queue Worker"

2. **Add Laravel Scheduler (if needed later):**
   - **Command**: `/usr/bin/php /home/yourusername/public_html/artisan schedule:run`
   - **Schedule**: `* * * * *` (every minute)
   - **Description**: "Laravel Scheduler"

### 4. Testing Your Cron Jobs

#### **Test Queue Worker:**
```bash
# SSH into your server and run:
cd /home/yourusername/public_html
php artisan queue:work --stop-when-empty

# Check if jobs are processed:
php artisan queue:failed
```

#### **Test Email Functionality:**
1. Create a new staff member from admin panel
2. Check if welcome email is sent
3. Monitor logs: `tail -f storage/logs/laravel.log`

### 5. Monitoring and Troubleshooting

#### **Check Cron Job Logs:**
Hostinger provides cron job execution logs in the control panel.

#### **Laravel Queue Monitoring:**
```bash
# Check pending jobs
php artisan queue:work --stop-when-empty

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

#### **Common Issues:**

**1. PHP Path Issues:**
```bash
# Test different PHP paths:
which php
php -v
/usr/bin/php -v
```

**2. Permission Issues:**
```bash
# Set proper permissions:
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

**3. Environment Issues:**
```bash
# Ensure .env file exists and is readable
ls -la .env
cat .env | grep QUEUE_CONNECTION
```

### 6. Alternative: Supervisor (Advanced)

If Hostinger supports Supervisor, you can use it instead of cron:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/yourusername/public_html/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=yourusername
numprocs=1
redirect_stderr=true
stdout_logfile=/home/yourusername/public_html/storage/logs/worker.log
```

### 7. Verification Checklist

- [ ] Cron job created in Hostinger control panel
- [ ] Correct PHP path used
- [ ] Correct project path used
- [ ] Queue worker running every minute
- [ ] Test email sending works
- [ ] Monitor logs for errors
- [ ] Check failed jobs queue

### 8. Important Notes

⚠️ **Critical**: Without the queue worker cron job, your staff emails **will not be sent**!

✅ **Best Practice**: Start with just the queue worker cron job, add others as needed.

🔍 **Monitoring**: Regularly check `storage/logs/laravel.log` for any queue processing errors.

📧 **Email Testing**: Always test email functionality after setting up cron jobs.

### 9. Support Resources

- **Hostinger Cron Jobs Guide**: https://support.hostinger.com/en/articles/1583187-how-to-set-up-cron-jobs
- **Laravel Queue Documentation**: https://laravel.com/docs/queues
- **Laravel Task Scheduling**: https://laravel.com/docs/scheduling

---

**Next Steps After Setup:**
1. Create a test staff member to verify email sending
2. Monitor cron job execution in Hostinger control panel
3. Check Laravel logs for any queue processing errors
4. Set up additional scheduled tasks as needed