# Taraba State Indigene Verification & Certification Platform
## Core Application Subsystem (`indigene_verification`)

This folder contains the active Laravel 12 core application for the **Taraba State Indigene Verification & Certification Platform**.

> **Note:** For the comprehensive enterprise architecture document, diagrams, relational database schemas, and end-to-end integration workflows, refer to the master architecture document:
> 
> 📄 **[Master Architecture Document (Root README.md)](../../README.md)**

---

## Quick Navigation

- **Core Documentation**:
  - [Master Architecture Document](../../README.md)
  - [Hostinger Deployment Guide](./DEPLOYMENT_GUIDE.md)
  - [Folder Structure Reference](./FOLDER_STRUCTURE.md)
  - [Hostinger Deployment Checklist](./HOSTINGER_CHECKLIST.md)
  - [Hostinger Fix Guide](./HOSTINGER_FIX_GUIDE.md)
  - [Hostinger Cron Setup](./HOSTINGER_CRON_SETUP.md)
  - [Cron Commands Reference](./CRON_COMMANDS.txt)

---

## Getting Started

### 1. Requirements
- PHP `>= 8.2` (Extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`)
- Composer `>= 2.5`
- Node.js `>= 18.x` & npm
- MySQL / MariaDB

### 2. Local Setup
```bash
# 1. Install PHP dependencies
composer install

# 2. Install and compile frontend assets
npm install
npm run build

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Run migrations & seeders
php artisan migrate --seed

# 5. Start development servers
php artisan serve
```

### 3. Background Services & Queue
```bash
# Start background worker for queued emails
php artisan queue:work

# Test user reassignment load balancing
php artisan users:reassign-staff
```

---

## Application Structure Overview

- `app/Http/Controllers/`: Application controllers (`AdminController`, `StaffController`, `PaymentController`, `UserPaymentController`, `StaffLgaController`, `ServiceController`, `CertificateController`).
- `app/Services/`: Integration services (`NinVerificationService` communicating with NIMC endpoint).
- `app/Models/`: Domain entities (`User`, `Payment`, `Service`, `PaymentSetting`, `StaffActivityLog`, `Notification`).
- `resources/views/`: Blade views styled with Tailwind CSS and powered by Alpine.js & Livewire.
- `routes/web.php`: System routing with middleware safeguards (`CheckNinVerification`, `StaffMiddleware`).
- `deploy.sh`: Hostinger deployment script.
