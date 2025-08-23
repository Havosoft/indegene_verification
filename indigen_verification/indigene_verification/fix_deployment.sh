#!/bin/bash

# Quick Fix Script for Hostinger Deployment Issue
# Run this script on your Hostinger server to fix the vendor/autoload.php error

echo "🔧 Laravel Hostinger Deployment Fix Script"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found. Make sure you're in the Laravel project root."
    echo "   Current directory: $(pwd)"
    echo "   Expected files: composer.json, artisan, app/, vendor/"
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "❌ vendor/ directory not found. This is the main issue!"
    echo "🔄 Installing Composer dependencies..."
    
    # Check if composer is available
    if command -v composer &> /dev/null; then
        composer install --optimize-autoloader --no-dev
        echo "✅ Composer dependencies installed"
    else
        echo "❌ Composer not found. Please install composer or upload vendor/ directory manually."
        echo "   Alternative: Upload the vendor/ directory from your local project."
        exit 1
    fi
else
    echo "✅ vendor/ directory exists"
fi

# Check if autoload.php exists
if [ -f "vendor/autoload.php" ]; then
    echo "✅ vendor/autoload.php found"
else
    echo "❌ vendor/autoload.php still missing after composer install"
    echo "   Try running: composer dump-autoload"
    composer dump-autoload --optimize
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found"
    if [ -f ".env.production" ]; then
        echo "📋 Copying .env.production to .env"
        cp .env.production .env
        echo "✅ .env file created from template"
        echo "⚠️  IMPORTANT: Update .env with your production database credentials!"
    else
        echo "❌ No .env template found. Create .env file manually."
    fi
else
    echo "✅ .env file exists"
fi

# Set proper permissions
echo "📁 Setting file permissions..."
chmod -R 755 storage/ 2>/dev/null || echo "⚠️  Could not set storage permissions"
chmod -R 755 bootstrap/cache/ 2>/dev/null || echo "⚠️  Could not set bootstrap/cache permissions"
chmod 644 .env 2>/dev/null || echo "⚠️  Could not set .env permissions"

# Create necessary directories
echo "📂 Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Generate application key if needed
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear 2>/dev/null || echo "⚠️  Config clear failed"
php artisan cache:clear 2>/dev/null || echo "⚠️  Cache clear failed"
php artisan view:clear 2>/dev/null || echo "⚠️  View clear failed"
php artisan route:clear 2>/dev/null || echo "⚠️  Route clear failed"

# Test database connection
echo "🗄️ Testing database connection..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" 2>/dev/null; then
    echo "✅ Database connection successful"
    
    # Run migrations
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
else
    echo "❌ Database connection failed. Please check your .env database settings."
    echo "   Required .env variables:"
    echo "   - DB_HOST=localhost"
    echo "   - DB_DATABASE=your_database_name"
    echo "   - DB_USERNAME=your_database_user"
    echo "   - DB_PASSWORD=your_database_password"
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "⚠️  Storage link already exists or failed"

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Final checks
echo ""
echo "🔍 Final Verification:"
echo "====================="

# Check critical files
if [ -f "vendor/autoload.php" ]; then
    echo "✅ vendor/autoload.php exists"
else
    echo "❌ vendor/autoload.php STILL MISSING!"
fi

if [ -f ".env" ]; then
    echo "✅ .env file exists"
else
    echo "❌ .env file missing"
fi

if [ -f "artisan" ]; then
    echo "✅ artisan file exists"
else
    echo "❌ artisan file missing"
fi

# Check directory structure
echo ""
echo "📁 Directory Structure:"
ls -la | grep -E "(vendor|app|bootstrap|config|database|resources|routes|storage|public)"

echo ""
echo "🎉 Fix script completed!"
echo ""
echo "📝 Next Steps:"
echo "1. Test your website: visit your domain"
echo "2. If still getting errors, check: tail -f storage/logs/laravel.log"
echo "3. Verify .env database settings are correct"
echo "4. Contact Hostinger support if server-level issues persist"
echo ""
echo "🔧 Common Issues:"
echo "- If vendor/ is still missing: Upload it manually from your local project"
echo "- If database errors: Update .env with correct Hostinger database credentials"
echo "- If permission errors: Contact Hostinger support"