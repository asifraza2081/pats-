# PATS — Prime Assessment & Testing Services

PATS is a comprehensive Laravel 11 application designed to manage the entire lifecycle of candidate testing and recruitment. It handles everything from candidate registration, profile building, and exact eligibility checking, to fee payments, complex test batch allocations, roll number generations, attendance marking, and final result percentiles.

## Features Overview
- **Robust Role-Based Access Control (RBAC)**: Powered by Spatie `laravel-permission` (Super Admin, Admin, Data Entry, Candidate).
- **Candidate Profiles**: Enforces 100% profile completion including photo, CNIC, domicile, education history, and work experience.
- **Dynamic Eligibility Engine**: Automatically checks age limits, required degree levels, specific subjects, minimum experience, and domicile constraints before allowing application submission.
- **Smart Batch Allocation**: Ensures test centers never exceed capacity and seamlessly groups candidates by job titles based on city preferences and verified payment status.
- **Collision-free Roll Numbers**: Sequentially generates collision-free, strictly numeric roll numbers within specific test centers.
- **Document Master Suite**: Generates automated Fee Challans, Roll Number Slips, and Hall Attendance Lists with candidate photos in institutional-standard high-fidelity layouts.
- **Administrative Print Portal**: A high-efficiency, AJAX-powered portal for batch-printing thousands of documents by Project, City, and Test Center without page reloads.
- **End-to-End Post-Test Processing**: Allows admin to upload attendance scans, upload CSV exam results, and automatically calculates candidate percentiles and percentages.
- **Hierarchical Document Repository**: Automatically organizes every recruitment lifecycle document (Slips, Attendance, Results) into a structured {Project}/{Job}/{Candidate} directory tree for persistent access and traceability.
- **Production-Grade Scorecards**: Generates individual, high-fidelity Result Cards for candidates with score breakdown, percentile, and digital verification.
- **Dynamic News Ticker**: A persistent dashboard announcement system for real-time recruitment updates.
- **Financial & Audit Module (FBR Ready)**: Includes an immutable financial ledger to track application revenue instantly. Features an operational expense tracker, automatic Withholding Tax (WHT) estimators, FBR IRIS compatible exports (Annex-A), and printable PDF vouchers.
- **Advanced Codebase Security**: Hardened globally with strict rate limiters (max 5 login attempts per 15 min), a robust HTMLPurifier middle layer that proactively scrubs XSS injections, decoupled environment secrets, and strict payload bound limitations.

---

## 🏗️ Core System Logic (The "Handshake")

The PATS system is built on a "Handshake" logic that coordinates candidate preferences with administrative infrastructure:

1.  **Candidate Choice**: When applying for a job, a candidate selects their **Desired Test City** (e.g., *Islamabad*). This preference is bound to their application.
2.  **Admin Infrastructure**: Administrators manage **Cities** and **Test Centers**. Every Center is physically located in a City (e.g., *Friendship Center* is in *Islamabad*).
3.  **The Allocation Process**: During the **Scheduling Phase**, the Admin creates **Batches** for specific **Test Centers**.
4.  **Automatic Matching**: When an Admin initiates allocation for a batch, the system:
    *   Filters for candidates who have a **Verified Payment**.
    *   Matches candidates whose **Desired Test City** matches the city of the **Test Center** being filled.
    *   Fills the Batch sequentially until the Center's **Seating Capacity** is reached.
    *   Generates a unique, barcode-ready **Roll Number** for the specific seat.

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
    
    *Optional Security Override:* If you are generating seeders in a non-local environment, you MUST define `ADMIN_DEFAULT_PASSWORD` and `USER_DEFAULT_PASSWORD` in your `.env` so passwords aren't locked to codebase defaults.

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

5. **Stress Test Data (Optional)**:
    To simulate a massive recruitment project with 5,000+ candidates across multiple cities and sessions:
    ```bash
    php SimulateMassiveResults.php  # Processes 5,000 candidate scores
    php SimulateAttendance.php      # Digitally signs 34 center batches
    php SyncMassiveRepository.php   # Generates 10,000+ persistent PDFs
    ```

---

## 🔑 Test Credentials 

After running the seeders, the following accounts are available for testing:

| Role | Email Address | Password | Notes |
|---|---|---|---|
| **Super Admin** | `admin@pats.test` | `Admin@1234` | Can manage other admin users, assign roles, and access all features. |
| **Examiner** | `examiner.lahore@pats.test` | `password` | Sample examiner (Lahore City). Access for field staff. |
| **Candidate** | `candidate@pats.test` | `password` | Sample candidate with a partially completed profile. |

*Note: The test credentials are bound to standard fallback values. To dynamically change these during deployment seeding, declare `ADMIN_DEFAULT_PASSWORD` and `USER_DEFAULT_PASSWORD` inside your base `.env` file.*

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

## 📖 User Role Instruction Manual

Below are the step-by-step workflows for each user role in the PATS system.

---

### 👤 1. For Candidates (The Applicant)
*The platform for individuals applying for jobs.*

1.  **Registration & Login**: Create an account and verify your identity.
2.  **Profile Completion (Essential)**: 
    - Navigate to **"My Profile"**.
    - You MUST reach **100% profile status** to apply for any job.
    - This includes: Personal Info, Education history (at least one degree), and uploading a Photo/CNIC scan.
3.  **Job Application**: 
    - Browse **"Open Projects"** and select a job.
    - Choose your **Desired Test City**.
    - The system will immediately check if you are eligible based on age, education, and experience.
4.  **Payment**: 
    - After applying, download the **Fee Challan** from "My Applications".
    - Visit the designated bank to pay and upload a scan/photo of the paid receipt for verification.
5.  **Roll Number Slip**: 
    - Once the Admin publishes slips, a download button will appear in your dashboard. Print this for the test day.
6.  **Results**: 
    - After the test, view your result card and **Scanned Answer Sheet** in the "My Applications" section.

---

### 🛡️ 2. For Admins (The Agency)
*The central authority managing the recruitment process.*

1.  **Project Management**: Create a recruitment project and add specific jobs with their unique eligibility criteria.
2.  **Center Verification**: Manage the list of cities and test centers (each with their seating capacity).
3.  **Payment Verification**: In the **"Payments"** module, review uploaded candidate receipts and mark them as "Verified".
4.  **Batch Scheduling**:
    - Go to **"Batches"** -> **"Create New Batch"**.
    - Select a Project, Jobs, and one or more Centers.
    - Set the Test Date and Time.
    - The system will automatically allocate all "Verified & Paid" candidates to these centers until capacity is reached (matching candidate city preference).
5.  **Multi-Center Logistics (Print Portal)**:
    - On the Batches index, use the **"Print Portal"** button.
    - Select the project to load its centers dynamically.
    - Instantly print **Roll Number Slips**, **Attendance Sheets**, and **Answer Sheets** for each specific center.
6.  **Roll Number Issuance**: After scheduling, click **"Mark Ready / Notify"** at the batch level. This generates roll numbers and notifies candidates via SMS/Email.
7.  **Attendance & Results**:
    - Print **Attendance Sheets** and **Answer Sheets** for the examiners.
    - After the test, use **"Upload Results"** to import a CSV file.
    - Once reviewed, click **"Publish Results"** to make them live for candidates.
8.  **Financial Integrity**:
    - Record daily operating expenses securely via **"Financials -> Expenses"**.
    - Toggle **"Enable FBR Mode"** in the Settings to unlock advanced Pakistan Tax Law (ITO 2001) compliance features, generating 1-click I&E summarizations and Excel-ready Annex-A registers.

---

### 📋 3. For Examiners (The Field Staff)
*Staff assigned to monitor a specific test session.*

1.  **Session Dashboard**: Log in to see only the sessions assigned to you for the day.
2.  **Logistics Processing**:
    - Open the **"Session Portal"**.
    - Download and print the **Attendance Sheet** (with candidate photos).
    - Print the pre-filled **Answer Sheets** to distribute in the exam hall.
3.  **Reporting**: After the test, the examiner or data entry operator can upload scanned attendance sheets and mark individual candidate attendance (Appeared/Absent).

---

## 📂 Digital Document Repository

The system implements a structured archiving system for all generated recruitment documents to ensure 100% traceability and persistent access:

**Directory Structure:**
- `storage/app/public/projects/{Project Name - Date}/`
  - `{Job Title}/Attendance Sheets/` (Bulk attendance PDFs)
  - `{Job Title}/Roll Numbers & Results/RollNo_{Num}/`
    - `RollNoSlip.pdf` (The persistent slip)
    - `ResultCard.pdf` (The primary candidate scorecard)
  - `{Job Title}/Summary/` (Allocation and merit summaries)

---

## 📄 File Templates

### Result Upload Format (CSV or Excel)
When an admin uploads the results for a project, the system expects a file (`.csv`, `.xlsx`, or `.xls`) with the following precise header names (case-insensitive):

| roll_no | score | total_marks | result_status |
|---|---|---|---|
| 1010130010001 | 85.5 | 100 | pass |
| 1010130010002 | 40.0 | 100 | fail |
| 1010130010003 | 0 | 100 | absent |

**Allowed Status Values:** `pass`, `fail`, `absent`, `withheld`.

<p align="right"><sub>Platform maintained by Adeel Ali Raja</sub></p>
