# Laravel Project Folder Structure

## ✅ Standard Laravel Project Structure

Your project now follows the standard Laravel folder structure:

```
indigene_verification/
├── .editorconfig              # Editor configuration
├── .env                       # Environment variables (production)
├── .env.example              # Environment template
├── .gitattributes            # Git attributes
├── .gitignore                # Git ignore rules
├── README.md                 # Project documentation
├── artisan                   # Laravel command-line interface
├── composer.json             # PHP dependencies
├── composer.lock             # Locked PHP dependencies
├── package.json              # Node.js dependencies
├── package-lock.json         # Locked Node.js dependencies
├── phpunit.xml               # PHPUnit testing configuration
├── postcss.config.js         # PostCSS configuration
├── tailwind.config.js        # Tailwind CSS configuration
├── vite.config.js            # Vite build configuration
├── deploy.sh                 # Deployment script
├── fix_deployment.sh         # Deployment fix script
├── DEPLOYMENT_GUIDE.md       # Deployment documentation
├── HOSTINGER_CHECKLIST.md    # Hostinger deployment checklist
├── HOSTINGER_FIX_GUIDE.md    # Hostinger troubleshooting guide
│
├── app/                      # Application logic
│   ├── Console/              # Artisan commands
│   ├── Http/                 # Controllers, middleware, requests
│   │   ├── Controllers/      # Application controllers
│   │   ├── Middleware/       # HTTP middleware
│   │   └── Requests/         # Form request validation
│   ├── Livewire/             # Livewire components
│   │   ├── Actions/          # Livewire actions
│   │   └── Forms/            # Livewire forms
│   ├── Mail/                 # Mail classes
│   ├── Models/               # Eloquent models
│   ├── Providers/            # Service providers
│   ├── Services/             # Business logic services
│   └── View/                 # View composers and components
│       └── Components/       # Blade components
│
├── bootstrap/                # Framework bootstrap files
│   ├── app.php              # Application bootstrap
│   ├── cache/               # Framework cache files
│   └── providers.php        # Service providers bootstrap
│
├── config/                   # Configuration files
│   ├── app.php              # Application configuration
│   ├── auth.php             # Authentication configuration
│   ├── cache.php            # Cache configuration
│   ├── database.php         # Database configuration
│   ├── filesystems.php      # Filesystem configuration
│   ├── logging.php          # Logging configuration
│   ├── mail.php             # Mail configuration
│   ├── queue.php            # Queue configuration
│   ├── services.php         # Third-party services
│   └── session.php          # Session configuration
│
├── database/                 # Database files
│   ├── .gitignore           # Database gitignore
│   ├── database.sqlite      # SQLite database (development)
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
│
├── public/                   # Web server document root
│   ├── .htaccess            # Apache rewrite rules
│   ├── index.php            # Application entry point
│   ├── favicon.ico          # Website favicon
│   ├── robots.txt           # Search engine robots file
│   ├── build/               # Compiled assets (Vite)
│   │   ├── assets/          # CSS/JS assets
│   │   └── manifest.json    # Asset manifest
│   ├── img/                 # Public images
│   │   └── bg/              # Background images
│   └── json/                # Public JSON files
│
├── resources/                # Raw assets and views
│   ├── css/                 # Source CSS files
│   │   └── app.css          # Main CSS file
│   ├── js/                  # Source JavaScript files
│   │   ├── app.js           # Main JavaScript file
│   │   └── bootstrap.js     # Bootstrap JavaScript
│   └── views/               # Blade templates
│       ├── admin/           # Admin panel views
│       ├── auth/            # Authentication views
│       ├── components/      # Blade components
│       ├── emails/          # Email templates
│       ├── layouts/         # Layout templates
│       ├── livewire/        # Livewire component views
│       ├── profile/         # User profile views
│       ├── services/        # Service-related views
│       ├── staff/           # Staff panel views
│       └── vendor/          # Vendor views
│
├── routes/                   # Route definitions
│   ├── auth.php             # Authentication routes
│   ├── console.php          # Console routes
│   └── web.php              # Web routes
│
├── storage/                  # Storage files
│   ├── app/                 # Application storage
│   │   ├── .gitignore       # Storage gitignore
│   │   ├── private/         # Private files
│   │   └── public/          # Public storage files
│   ├── framework/           # Framework storage
│   │   ├── .gitignore       # Framework gitignore
│   │   ├── cache/           # Application cache
│   │   ├── sessions/        # Session files
│   │   ├── testing/         # Testing files
│   │   └── views/           # Compiled views
│   └── logs/                # Application logs
│       ├── .gitignore       # Logs gitignore
│       └── laravel.log      # Laravel log file
│
├── tests/                    # Test files
│   ├── Feature/             # Feature tests
│   │   └── Auth/            # Authentication tests
│   ├── Unit/                # Unit tests
│   ├── Pest.php             # Pest testing configuration
│   └── TestCase.php         # Base test case
│
└── vendor/                   # Composer dependencies
    ├── autoload.php         # Composer autoloader
    ├── bin/                 # Vendor binaries
    └── [various packages]   # Third-party packages
```

## ✅ What Was Fixed

1. **Removed duplicate nested folder**: Eliminated the redundant `indigene_verification/indigene_verification/` structure
2. **Clean root structure**: All Laravel directories are now at the proper root level
3. **Standard conventions**: The project now follows Laravel's standard folder organization

## 🎯 Key Laravel Directories

- **`app/`**: Contains your application's core logic (models, controllers, services)
- **`public/`**: Web server document root (contains index.php and public assets)
- **`resources/`**: Contains raw assets and Blade templates
- **`routes/`**: Contains all route definitions
- **`config/`**: Contains all configuration files
- **`database/`**: Contains migrations, seeders, and factories
- **`storage/`**: Contains logs, cache, and uploaded files
- **`vendor/`**: Contains Composer dependencies (auto-generated)

## 🚀 Ready for Deployment

Your Laravel project now has a clean, standard structure that:
- ✅ Follows Laravel conventions
- ✅ Is ready for version control
- ✅ Is properly organized for deployment
- ✅ Maintains separation of concerns
- ✅ Is easy to navigate and maintain

The folder structure is now optimized for both development and production environments!