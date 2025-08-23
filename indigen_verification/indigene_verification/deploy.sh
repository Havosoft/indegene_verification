#!/bin/bash

# Laravel Deployment Script for Hostinger
# Run this script on your server after uploading files

echo "🚀 Starting Laravel deployment for Hostinger..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Make sure you're in the Laravel project root."
    exit 1
fi

# Check PHP version
echo "🔍 Checking PHP version..."
php_version=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
echo "PHP version: $php_version"

# Set proper permissions
echo "📁 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chmod 644 .env 2>/dev/null || echo "⚠️  .env file not found - create it manually"

# Create necessary directories if they don't exist
echo "📂 Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set permissions for created directories
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear 2>/dev/null || echo "⚠️  Config clear failed"
php artisan cache:clear 2>/dev/null || echo "⚠️  Cache clear failed"
php artisan view:clear 2>/dev/null || echo "⚠️  View clear failed"
php artisan route:clear 2>/dev/null || echo "⚠️  Route clear failed"

# Check database connection
echo "🗄️ Testing database connection..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" 2>/dev/null; then
    echo "✅ Database connection successful"
    
    # Run database migrations
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
    
    # Seed database if needed
    if [ "$1" = "--seed" ]; then
        echo "🌱 Seeding database..."
        php artisan db:seed --force
    fi
else
    echo "❌ Database connection failed. Please check your .env database settings."
fi

# Generate application key if not set
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "⚠️  Storage link already exists or failed"

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check if storage is writable
echo "🔍 Checking storage permissions..."
if [ -w "storage" ]; then
    echo "✅ Storage directory is writable"
else
    echo "❌ Warning: Storage directory is not writable"
    echo "   Run: chmod -R 755 storage/"
fi

# Check if .env exists
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    
    # Check critical .env variables
    if grep -q "APP_KEY=base64:" .env; then
        echo "✅ Application key is set"
    else
        echo "❌ Warning: Application key not set properly"
    fi
    
    if grep -q "APP_ENV=production" .env; then
        echo "✅ Environment set to production"
    else
        echo "⚠️  Warning: Environment not set to production"
    fi
    
    if grep -q "APP_DEBUG=false" .env; then
        echo "✅ Debug mode disabled"
    else
        echo "⚠️  Warning: Debug mode should be disabled in production"
    fi
else
    echo "❌ Warning: .env file not found"
    echo "   Copy .env.production to .env and update database credentials"
fi

# Check if build assets exist
if [ -d "public/build" ]; then
    echo "✅ Build assets found"
else
    echo "❌ Warning: Build assets not found. Run 'npm run build' locally first."
fi

# Check critical directories
echo "🔍 Checking critical directories..."
for dir in "storage/logs" "storage/framework/cache" "storage/framework/sessions" "storage/framework/views" "bootstrap/cache"; do
    if [ -d "$dir" ] && [ -w "$dir" ]; then
        echo "✅ $dir exists and is writable"
    else
        echo "❌ Warning: $dir is missing or not writable"
    fi
done

echo ""
echo "🎉 Deployment completed!"
echo ""
echo "📝 Next steps:"
echo "   1. Update .env file with your production database credentials"
echo "   2. Update .env file with your production domain URL"
echo "   3. Update .env file with your production email settings"
echo "   4. Update .env file with your live Paystack keys"
echo "   5. Test your website functionality"
echo "   6. Check error logs if issues occur: tail -f storage/logs/laravel.log"
echo "   7. Configure SSL certificate in Hostinger"
echo ""
echo "🔧 Troubleshooting commands:"
echo "   - Check logs: tail -f storage/logs/laravel.log"
echo "   - Clear cache: php artisan cache:clear"
echo "   - Test database: php artisan tinker"
echo "   - Check permissions: ls -la storage/ bootstrap/cache/"