# Taraba State Indigene Verification & Certification Platform
## System Architecture & Technical Documentation

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-^8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Livewire](https://img.shields.io/badge/Livewire-Volt%20v3.4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)](#)

---

## 1. Executive Summary

The **Taraba State Indigene Verification & Certification Platform** is an enterprise-grade digital identity, indigene verification, and certification portal engineered for the Government of Taraba State, Nigeria. 

The platform digitizes and automates the verification of citizen indigene claims across the **16 Local Government Areas (LGAs)** of Taraba State. It features direct integration with the **National Identity Management Commission (NIMC)** National Identification Number (NIN) verification API, automated geographical and demographic validation, secure online payment processing via **Paystack**, role-based staff review workflows, certificate generation, and an automated LGA load-balancing assignment engine.

```
                      +---------------------------------------+
                      |       Citizen Registration Portal     |
                      +---------------------------------------+
                                          |
                      +-------------------+-------------------+
                      |                                       |
                      v                                       v
          +-----------------------+               +-----------------------+
          |   NIMC Verification   |               |   Paystack Gateway    |
          |  (NIN / LGA / DOB)    |               |  (Card / USSD / Bank) |
          +-----------------------+               +-----------------------+
                      |                                       |
                      +-------------------+-------------------+
                                          |
                                          v
                      +---------------------------------------+
                      |   LGA Staff Verification Workspace   |
                      |    (16 Taraba LGAs Distributed)      |
                      +---------------------------------------+
                                          |
                                          v
                      +---------------------------------------+
                      |   Approved Certificate Issuance &    |
                      |   Secure Multi-channel Delivery       |
                      +---------------------------------------+
```

---

## 2. High-Level Architecture

The platform follows a modern, decoupled **Model-View-Controller (MVC)** architectural pattern within the Laravel 12 framework, enhanced with a dedicated **Service Layer** for external integrations, asynchronous queue workers for notification handling, and fine-grained middleware pipelines.

```mermaid
graph TD
    Client[Web Browser / Mobile Client] -->|HTTPS| WebServer[Web Server: Apache / Nginx / Hostinger]
    WebServer -->|index.php| Bootstrap[Laravel Bootstrap & Pipeline]

    subgraph Security_Layer [Middleware Pipeline]
        Bootstrap --> AuthMdl[auth / guest Middleware]
        AuthMdl --> StaffMdl[StaffMiddleware]
        AuthMdl --> NinMdl[CheckNinVerification Middleware]
    end

    subgraph Presentation_Layer [Presentation & Interaction]
        Blade[Blade Templating Engine]
        Livewire[Livewire 3 / Volt Components]
        Alpine[Alpine.js State Handlers]
        Tailwind[Tailwind CSS UI]
    end

    subgraph Controller_Layer [Application Controllers]
        RegCtrl[RegisteredUserController]
        PayCtrl[PaymentController / UserPaymentController]
        AdminCtrl[AdminController]
        StaffCtrl[StaffController]
        CertCtrl[CertificateController]
        LgaCtrl[StaffLgaController]
        SettingCtrl[PaymentSettingController]
    end

    subgraph Service_Layer [Business & Integration Layer]
        NinService[NinVerificationService]
        PaystackClient[Paystack Verification Client]
        MailSubsystem[Mailable Notification Queue]
    end

    subgraph Data_Layer [Data & Persistence]
        Eloquent[Eloquent ORM Models]
        MySQL[(MySQL / MariaDB)]
        Storage[(Local / Private Disk: Certificates)]
    end

    Security_Layer --> Controller_Layer
    Controller_Layer --> Presentation_Layer
    Controller_Layer --> Service_Layer
    Controller_Layer --> Eloquent
    Service_Layer --> Eloquent
    Eloquent --> MySQL
    CertCtrl --> Storage
```

---

## 3. Technology Stack & Key Dependencies

| Layer | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Backend Core** | PHP | `^8.2` | Runtime environment |
| **Framework** | Laravel | `^12.0` | MVC architecture, routing, ORM, queues |
| **Reactive UI** | Livewire & Volt | `^3.4` / `^1.7` | Dynamic reactive components without SPA overhead |
| **Client State** | Alpine.js | `^3.4` | Modal dialogs, dropdowns, client checkout bindings |
| **Styling** | Tailwind CSS | `^3.1` / `@tailwindcss/vite` | Modern responsive utility-first UI styling |
| **Asset Bundler** | Vite | `^6.2` | High-performance client-side asset compilation |
| **Payment Gateway**| Paystack API | Standard REST + Inline JS | Secure transaction settlement (NGN) |
| **Identity API** | Collaboratoor NIMC API | JSON REST Endpoint | Real-time biometric/demographic NIN verification |
| **Database** | MySQL / SQLite | 8.0+ / 3.x | Relational entity and session/queue persistence |
| **Mailing** | Laravel Mailable / SMTP | TLS Port 587 | Transactional citizen and staff notifications |

---

## 4. User Personas & Access Control Matrix

The platform implements a **Three-Tier Role-Based Access Control (RBAC)** architecture:

```mermaid
graph LR
    User[Citizen User] -->|Access| UserScope[Profile, Pay Services, Download Certificates, Reverify NIN]
    Staff[LGA Staff Officer] -->|Access| StaffScope[Scoped LGA User Queue, NIN Inspection, Certificate Upload & Approval]
    Admin[Super Administrator] -->|Access| AdminScope[LGA Distribution, Global Stats, Revenue Charts, Staff Registration, Global User Management]
```

### Detailed Permissions Matrix

| Feature / Action | Citizen (`user`) | LGA Officer (`staff`) | Administrator (`admin`) |
| :--- | :---: | :---: | :---: |
| Self-Registration & Profile Management | ✅ | ❌ | ❌ |
| Pay for Certificates & LGA ID Card | ✅ | ❌ | ❌ |
| Run NIN Verification / Re-verification | ✅ (Self) | ✅ (Assigned) | ✅ (All) |
| Download Approved Certificate | ✅ (Self) | ❌ | ✅ (All) |
| View Assigned LGA Applicant Queue | ❌ | ✅ (Assigned LGA) | ✅ (Global) |
| Approve / Reject Certificate Application | ❌ | ✅ (Assigned LGA) | ✅ (Global) |
| Upload Signed Certificate File | ❌ | ✅ (Assigned LGA) | ✅ (Global) |
| View Staff Activity Audit Logs | ❌ | ❌ | ✅ |
| Assign / Reassign Staff to LGAs | ❌ | ❌ | ✅ |
| Execute LGA Load-Balancing Reassignment | ❌ | ❌ | ✅ |
| Configure Payment Settings (Pay-First vs Pay-Later)| ❌ | ❌ | ✅ |
| Manage Services & Pricing Catalog | ❌ | ❌ | ✅ |
| View Revenue Analytics & Registration Charts | ❌ | ❌ | ✅ |

---

## 5. Domain Models & Relational Schema

```mermaid
erDiagram
    USERS ||--o{ PAYMENTS : "makes"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ STAFF_ACTIVITY_LOGS : "acts_on / performed_by"
    USERS ||--o{ USERS : "assigned_staff_id (staff manages users)"
    PAYMENTS ||--o{ STAFF_ACTIVITY_LOGS : "logs_decision_for"
    SERVICES ||..o{ PAYMENTS : "referenced_in"

    USERS {
        bigint id PK
        string name
        string email UK
        string phone
        date date_of_birth
        enum gender "male,female,other"
        string state_of_origin "Default: Taraba"
        string lga "Citizen LGA"
        string assigned_lga "Staff assigned LGA"
        bigint assigned_staff_id FK "Assigned LGA Officer"
        string address
        string place_of_birth
        string nin
        string temporary_id "TMP-XXXXXXXX"
        string permanent_id "TAR-TAR-XXXXXX"
        enum role "user,staff,admin"
        boolean is_verified
        enum nin_verification_status "pending,verified,failed"
        text nin_verification_failure_reason
        json nin_verification_data
        timestamp email_verified_at
        timestamps created_at_updated_at
    }

    PAYMENTS {
        bigint id PK
        bigint user_id FK
        string service_name
        integer price
        string payment_ref
        string payer_name
        string payer_email
        string approved "awaiting_review,approved,not_approved"
        string file "Storage path to certificate"
        timestamps created_at_updated_at
    }

    SERVICES {
        bigint id PK
        string name UK
        integer price
        boolean is_active
        boolean is_required
        timestamps created_at_updated_at
    }

    PAYMENT_SETTINGS {
        bigint id PK
        string key UK "e.g. registration_payment"
        boolean pay_before_data_save
        timestamps created_at_updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type
        text message
        timestamp read_at
        timestamps created_at_updated_at
    }

    STAFF_ACTIVITY_LOGS {
        bigint id PK
        bigint staff_id FK "Users table"
        bigint user_id FK "Users table"
        bigint payment_id FK "Payments table"
        string action "verified,approved,not_approved"
        string details
        text description "Reason for disapproval"
        timestamps created_at_updated_at
    }
```

---

## 6. Core Application Workflows

### 6.1. Registration & Payment Pipeline

The platform supports a dual registration workflow dictated dynamically by the `payment_settings.pay_before_data_save` database flag.

```mermaid
sequenceDiagram
    autonumber
    actor Citizen
    participant Browser
    participant RegController as RegisteredUserController
    participant Paystack as Paystack API
    participant PayController as PaymentController
    participant NinService as NinVerificationService
    participant DB as MySQL Database

    Citizen->>Browser: Fill Demographic & NIN Form
    Browser->>RegController: Submit Registration Data

    alt Setting: pay_before_data_save == true
        RegController->>Browser: Store Form in Session -> Redirect to Service Selection
        Citizen->>Browser: Select Certificates (e.g., Indigene Certificate, LGA ID)
        Browser->>Paystack: Launch PaystackPop Inline Modal
        Citizen->>Paystack: Complete Payment
        Paystack-->>Browser: Callback with Reference
        Browser->>PayController: GET /payment/paystack/callback?reference={ref}
        PayController->>Paystack: Verify Transaction via Secret Key
        PayController->>NinService: verifyNin(nin, user)
        NinService-->>PayController: Return Validation Status
        PayController->>DB: Create User + Payments + Assign LGA Staff
        PayController->>Browser: Redirect to Dashboard with Verified Status
    else Setting: pay_before_data_save == false
        RegController->>DB: Create User Immediately (Temporary ID)
        RegController->>DB: Assign Random Staff for User's LGA
        RegController->>Browser: Authenticate & Redirect to Dashboard
        Browser->>Citizen: Display Pending Services Payment Modal
        Citizen->>Paystack: Settle Payment from Dashboard
        Paystack-->>PayController: UserPaymentController Callback -> Trigger NIN Verification
    end
```

### 6.2. NIN Identity & Indigene Verification Engine

Automated verification occurs through `App\Services\NinVerificationService`. The service enforces three strict validation rules against the NIMC biometric record:

1. **State of Origin Match**: `nin_data.state_of_origin` must strictly match `"taraba"`.
2. **LGA of Origin Match**: The LGA selected during registration must strictly match `nin_data.lga_of_origin`.
3. **Date of Birth Match**: The citizen's submitted Date of Birth (`YYYY-MM-DD`) must match the date of birth on NIMC records.

```mermaid
flowchart TD
    Start[User Submits NIN] --> APIReq[Query Collaboratoor NIMC API]
    APIReq --> RespCheck{HTTP 200 & Status == 'success'?}
    
    RespCheck -- No / Timeout --> FailAPI[Flag Verification Failed: Network or Service Disruption]
    RespCheck -- Yes --> ValState{State == 'Taraba'?}

    ValState -- No --> FailState[Flag Failed: User is from another State]
    ValState -- Yes --> ValLGA{User LGA == NIN LGA?}

    ValLGA -- No --> FailLGA[Flag Failed: LGA Mismatch]
    ValLGA -- Yes --> ValDOB{User DOB == NIN DOB?}

    ValDOB -- No --> FailDOB[Flag Failed: Date of Birth Mismatch]
    ValDOB -- Yes --> Success[Verification Successful]

    Success --> GenPermID[Generate Permanent ID: TAR-TAR-XXXXXX]
    GenPermID --> SetVerified[Set is_verified = true, status = 'verified']

    FailAPI --> SetFailed[Set status = 'failed', Store Failure Reason]
    FailState --> SetFailed
    FailLGA --> SetFailed
    FailDOB --> SetFailed
    SetFailed --> TriggerModal[Dashboard Displays Actionable Reverification Modal]
```

### 6.3. Staff Review & Certificate Issuance Workflow

```mermaid
sequenceDiagram
    autonumber
    actor Staff as LGA Staff Officer
    participant StaffUI as Staff Portal
    participant StaffCtrl as StaffController
    participant Audit as StaffActivityLog
    participant Notify as Notifications & Mail
    actor Citizen

    Staff->>StaffUI: Navigate to /staff/users
    StaffUI->>StaffCtrl: Request Viewable Users (Scoped to Assigned LGA)
    StaffCtrl-->>StaffUI: Display Applicant Queue

    opt Manual Re-Verification
        Staff->>StaffCtrl: POST /staff/users/{id}/verify
        StaffCtrl->>Audit: Log 'verified' action
    end

    Staff->>StaffUI: Open Applicant Payment Detail
    Staff->>StaffUI: Attach Signed Certificate Document (PDF/PNG)
    Staff->>StaffCtrl: POST /staff/certificates/{paymentId}/approve (approved)
    StaffCtrl->>Audit: Log 'approved' with file path
    StaffCtrl->>Notify: Send CertificateApprovedMail & Dashboard Notification
    StaffCtrl-->>StaffUI: Return Success Toast

    Citizen->>StaffUI: Access /certificates/download/{id}
    StaffUI-->>Citizen: Secure File Download Response
```

### 6.4. LGA Load Balancing Subsystem

Taraba State comprises 16 distinct Local Government Areas:

| # | Local Government Area | # | Local Government Area |
|---|-----------------------|---|-----------------------|
| 1 | Ardo Kola             | 9 | Kurmi                 |
| 2 | Bali                  | 10| Lau                   |
| 3 | Donga                 | 11| Sardauna              |
| 4 | Gashaka               | 12| Takum                 |
| 5 | Gassol                | 13| Ussa                  |
| 6 | Ibi                   | 14| Wukari                |
| 7 | Jalingo               | 15| Yorro                 |
| 8 | Karim Lamido          | 16| Zing                  |

When a citizen registers:
- `User::assignUserToRandomStaff($user)` inspects the citizen's LGA.
- It identifies active staff members with `assigned_lga == $user->lga`.
- It assigns a staff member randomly to balance application volume.
- **Admin Command / Scheduled Routine**: `php artisan users:reassign-staff {--force}` can be triggered manually or via cron to re-distribute workloads dynamically when new staff members are onboarded.

---

## 7. Storage & File Management Architecture

Uploaded certificates and generated assets are managed through Laravel's Filesystem abstraction:

```
storage/
├── app/
│   ├── private/
│   │   └── certificates/    # Private signed certificates (Access restricted)
│   └── public/              # Public asset storage
├── framework/
│   ├── cache/               # Application & data cache
│   ├── sessions/            # File-based session fallback
│   └── views/               # Compiled Blade views
└── logs/
    └── laravel.log          # System application logs
```

### Certificate Access Control
Certificates are protected from unauthorized direct URL access. Downloading requires:
1. Active session authentication (`auth` middleware).
2. Explicit ownership verification (`$payment->user_id === Auth::id()` or administrative override).
3. Delivery through `CertificateController::downloadCertificate($id)` using streaming file responses.

---

## 8. Deployment & Infrastructure Architecture

The platform includes comprehensive deployment automation tailored for standard Linux distributions and **Hostinger Web Hosting (Cloud / VPS / Shared)**:

```
                                +-----------------------------+
                                |      Internet / HTTPS       |
                                +-----------------------------+
                                               |
                                               v
                                +-----------------------------+
                                |      Hostinger / Apache     |
                                |     (.htaccess Protection)  |
                                +-----------------------------+
                                               |
                     +-------------------------+-------------------------+
                     |                                                   |
                     v                                                   v
      +-----------------------------+                     +-----------------------------+
      |       public/index.php      |                     |      Cron Job Automation    |
      |   - Gzip Compression        |                     |   - Queue Worker (Email)    |
      |   - Security Headers        |                     |   - Laravel Task Scheduler  |
      |   - Sensitive Folder Shield |                     |   - Load Balance Reassign   |
      +-----------------------------+                     +-----------------------------+
                     |                                                   |
                     +-------------------------+-------------------------+
                                               |
                                               v
                                +-----------------------------+
                                |      Laravel Application    |
                                |    (PHP 8.2+ / Artisan)     |
                                +-----------------------------+
                                               |
                                               v
                                +-----------------------------+
                                |      MySQL Database &       |
                                |     Private Storage Disk    |
                                +-----------------------------+
```

### 8.1. Web Server Hardening (`.htaccess`)
The root `.htaccess` enforces enterprise-grade web server policies:
- **Directory Access Shield**: Direct HTTP requests to `storage/`, `bootstrap/cache/`, `vendor/`, `database/`, `tests/`, `config/`, `app/`, `routes/`, and `resources/` are blocked with `403 Forbidden`.
- **Sensitive File Shield**: `.env`, `composer.json`, `package.json`, and `*.log` files are completely inaccessible.
- **Security Headers**:
  - `X-Frame-Options: SAMEORIGIN` (Clickjacking mitigation)
  - `X-XSS-Protection: 1; mode=block`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - Granular `Content-Security-Policy (CSP)`
- **Performance Optimization**: `mod_deflate` Gzip compression and `mod_expires` caching for static assets.

### 8.2. Cron Jobs & Daemon Infrastructure

For complete operational functionality, the following cron commands should be scheduled in the server control panel:

```bash
# 1. Essential Queue Worker (Dispatches background emails & notifications)
* * * * * /usr/bin/php /path/to/project/artisan queue:work --stop-when-empty

# 2. Laravel Task Scheduler (Executes maintenance tasks)
* * * * * /usr/bin/php /path/to/project/artisan schedule:run

# 3. Weekly LGA Staff Load-Balancing Reassignment
0 2 * * 0 /usr/bin/php /path/to/project/artisan users:reassign-staff --force
```

---

## 9. Directory Structure & Key Files Reference

```
indigene_verification/
├── app/
│   ├── Console/Commands/
│   │   └── ReassignUsersToStaff.php       # CLI command for load-balancing
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php         # Admin analytics, payments & staff activity
│   │   │   ├── CertificateController.php   # Secure certificate download handler
│   │   │   ├── PaymentController.php       # Registration Paystack verification callback
│   │   │   ├── PaymentSettingController.php# Pay-before vs pay-after toggles
│   │   │   ├── ServiceController.php       # Service catalogue CRUD operations
│   │   │   ├── StaffController.php         # Staff dashboard, queue & review workflows
│   │   │   ├── StaffLgaController.php      # Staff LGA assignment manager
│   │   │   └── UserPaymentController.php   # Authenticated user Paystack checkout & reverify
│   │   └── Middleware/
│   │       ├── CheckNinVerification.php    # Intercepts failed NIN status on protected routes
│   │       └── StaffMiddleware.php         # Restricts staff workspace to authorized roles
│   ├── Mail/                              # Transactional email classes (Mailable)
│   ├── Models/
│   │   ├── Notification.php                # Citizen notifications
│   │   ├── Payment.php                     # Payment transactions
│   │   ├── PaymentSetting.php              # Dynamic checkout policy settings
│   │   ├── Service.php                     # Certificate catalog & fees
│   │   ├── StaffActivityLog.php            # Audit trail for staff actions
│   │   └── User.php                        # User identity, roles & LGA scopes
│   └── Services/
│       └── NinVerificationService.php      # NIMC API client & validation engine
├── config/                                # Application, DB, Mail & Service configuration
├── database/
│   ├── migrations/                        # Structured database schema history
│   └── seeders/                           # Initial payment settings & default seeders
├── public/
│   ├── .htaccess                          # Apache web server rules & security filters
│   ├── index.php                          # Single entry point
│   └── json/nigeria-states-lgas.json      # Nigerian geographical boundary dataset
├── resources/
│   ├── views/
│   │   ├── admin/                         # Admin dashboards, revenue charts, staff logs
│   │   ├── auth/                          # Login, registration, service selection, checkout
│   │   ├── certificates/                  # Citizen certificate download portal
│   │   ├── staff/                         # LGA officer workspace & applicant review
│   │   └── dashboard.blade.php            # Primary citizen and admin dashboard
│   ├── css/app.css                        # Tailwind CSS source
│   └── js/app.js                          # Alpine.js & client scripts
├── routes/
│   ├── web.php                            # Main HTTP route definitions
│   └── auth.php                           # Authentication & password reset routes
└── storage/                               # Logs, private certificates, and session storage
```

---

## 10. Environment & Configuration Reference

Create a `.env` file in the project root with the following configuration keys:

```dotenv
APP_NAME="Taraba State Indigene Verification"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://indigene.tarabastate.gov.ng

# Database Persistence
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taraba_indigene_db
DB_USERNAME=taraba_user
DB_PASSWORD=YOUR_STRONG_PASSWORD

# Drivers
SESSION_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

# Paystack Gateway Credentials
PAYSTACK_PUBLIC_KEY=your_paystack_public_key
PAYSTACK_SECRET_KEY=your_paystack_secret_key
PAYSTACK_PAYMENT_URL=https://api.paystack.co

# Mail Delivery (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=mail.tarabastate.gov.ng
MAIL_PORT=587
MAIL_USERNAME=noreply@tarabastate.gov.ng
MAIL_PASSWORD=YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tarabastate.gov.ng"
MAIL_FROM_NAME="Taraba State Indigene Verification Portal"
```

---

## 11. Security & Operational Hardening Architecture

The platform incorporates multi-tiered security safeguards:

1. **Authentication & Password Hygiene**:
   - Passwords hashed using bcrypt (`BCRYPT_ROUNDS=12`).
   - Rate limiting enforced on authentication and verification routes (`throttle:6,1`).
   - Session fixation protection and database-backed session storage.

2. **Transaction Integrity**:
   - All Paystack transaction callbacks require server-to-server HTTP verification using the secret key before account status or payments are updated.
   - Reference deduplication prevents replay attacks.

3. **Auditability**:
   - Every staff action (verification approval, rejection, certificate upload) is immutably recorded in `staff_activity_logs` with staff ID, target citizen ID, timestamp, and detailed explanatory notes.

4. **Security Audit & Continuous Hardening**:
   - A dedicated internal security assessment report is maintained by the engineering team covering credential management, environment segregation, and periodic dependency scanning.

---

## 12. Local Development & Setup Guide

### Prerequisites
- PHP `>= 8.2` with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo` extensions
- Composer `>= 2.5`
- Node.js `>= 18.x` & npm
- MySQL / MariaDB Server

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Havosoft/indegene_verification.git
   cd indegene_verification/indigen_verification/indigene_verification
   ```

2. **Install backend dependencies:**
   ```bash
   composer install
   ```

3. **Install and compile frontend assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Execute database migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the application:**
   ```bash
   php artisan serve
   ```

---

*Document maintained by the Engineering Team. For questions or internal inquiries, contact the administrative office.*
