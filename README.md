# PATS — Prime Assessment & Testing Services

PATS is a comprehensive Laravel 11 application designed to manage the entire lifecycle of candidate testing and recruitment. It handles everything from candidate registration, profile building, and exact eligibility checking, to fee payments, complex test batch allocations, roll number generations, attendance marking, and final result percentiles.

## Features Overview

- **Robust Role-Based Access Control (RBAC)**: Powered by Spatie `laravel-permission` (Super Admin, Admin, Data Entry, Candidate).
- **Candidate Profiles**: Enforces 100% profile completion including photo, CNIC, domicile, education history, and work experience.
- **Dynamic Eligibility Engine**: Automatically checks age limits, required degree levels, specific subjects, minimum experience, and domicile constraints before allowing application submission.
- **Smart Batch Allocation**: Ensures test centers never exceed capacity and seamlessly groups candidates by job titles.
- **Collision-free Roll Numbers**: Sequentially generates 9-digit alphanumeric roll numbers within specific test centers (e.g., `LHE010001`).
- **PDF Generation**: Generates automated Fee Challans and standardized NTS-style Roll Number Slips.
- **End-to-End Post-Test Processing**: Allows admin to upload attendance scans, upload CSV exam results, and automatically calculates candidate percentiles and percentages.

---

## 🚀 Installation & Local Setup

1. **Clone the repository and install dependencies**:
    ```bash
    git clone https://github.com/asifraza2081/pats- .
    git checkout laravel
    composer install
    npm install && npm run build
    ```

2. **Configure Environment variables**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Ensure you set `DB_DATABASE`, `DB_USERNAME`, `APP_URL`, and any SMTP/SMS settings in the `.env` file.*

3. **Storage Link**:
    To ensure uploaded profile pictures and attendance scans are publicly accessible, run:
    ```bash
    php artisan storage:link
    ```

4. **Migrate and Seed the Database**:
    This will create all the required tables, seed the Spatie roles, and generate dummy test data (a sample project, test centers, open jobs, and a few sample candidates).
    ```bash
    php artisan migrate:fresh --seed
    ```

---

## 🔑 Test Credentials 

After running the seeders, the following accounts are available for testing:

| Role | Email Address | Password | Notes |
|---|---|---|---|
| **Super Admin** | `super@pats.test` | `password` | Can manage other admin users, assign roles, and access all features. |
| **Admin** | `admin@pats.test` | `password` | Can manage projects, jobs, centers, batches, and results. |
| **Candidate** | *Register a new account* | *Any* | Register a new account via the frontend. The seeder also generates 5 random candidate accounts (check the `users` table where `role=candidate`). |

---

## ⏱️ Cron Jobs (Task Scheduling)

**Yes, you need to set up a Cronjob** on your production server. 
Laravel's task scheduler handles asynchronous queue processing, clearing expired OTP tokens, and potentially closing out expired job applications.

Add the following Cron entry to your server to run every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

*(Replace `/path-to-your-project` with the actual absolute path to the Laravel installation directory on your server.)*

If you plan to heavily use SMS/Email notifications, you should also ensure your queue worker is running using Supervisor. For local testing, you can use:
```bash
php artisan queue:work
```

---

## 🧪 Automated End-to-End (E2E) Testing

To verify the core logic works perfectly without navigating the UI, the repository includes two backend simulation scripts. You can run these using Laravel Tinker:

**1. Test the Candidate Application Flow:**
Simulates a candidate finishing their profile, applying, getting batch capacity allocated, paying the fee, and generating a roll number.
```bash
php artisan tinker e2e_test.php
```

**2. Test the Admin Post-Test Flow:**
Simulates an admin marking candidate attendance, uploading a simulated CSV test result sheet, and publishing percentiles.
```bash
php artisan tinker e2e_admin_test.php
```

---

## 📄 File Templates

### Result Upload Format (CSV or Excel)
When an admin uploads the results for a project, the system expects a file (`.csv`, `.xlsx`, or `.xls`) with the following precise header names (case-insensitive):

| roll_number | score | total_marks | status |
|---|---|---|---|
| LHE010001 | 85.5 | 100 | pass |
| LHE010002 | 40.0 | 100 | fail |
| LHE010003 | 0 | 100 | absent |

**Allowed Status Values:** `pass`, `fail`, `absent`, `withheld`.
