# PATS — Prime Assessment & Testing Services

PATS is a comprehensive Laravel 11 application designed to manage the entire lifecycle of candidate testing and recruitment. It handles everything from candidate registration, profile building, and exact eligibility checking, to fee payments, complex test batch allocations, roll number generations, attendance marking, and final result percentiles.

## 🛡️ Institutional-Grade Hardening (Ironman V5)

The system has undergone a rigorous "Ironman" hardening phase to ensure stability, high-fidelity rendering, and administrative efficiency for large-scale governmental and private recruitment projects.

### Key Ironman Features:
- **High-Performance Print Portal**: A centralized, AJAX-powered modal for administrators to scope and batch-print thousands of documents by Project, City, and Test Center without page reloads.
- **Visual Artifact Resolution**: Definitive resolution of navbar and UI regressions for a clean, professional administrative interface.
- **High-Fidelity PDF Engine**: Optimized document generation using standard institutional fonts (Helvetica) and clear, NTS-style layouts for Slips, Attendance, and OMR sheets.
- **Dumbproof Workflows**: Enhanced helping text and instructional alerts across critical scheduling and allocation modules.

## Features Overview
- **Robust Role-Based Access Control (RBAC)**: Powered by Spatie `laravel-permission` (Super Admin, Admin, Data Entry, Candidate).
- **Candidate Profiles**: Enforces 100% profile completion including photo, CNIC, domicile, education history, and work experience.
- **Dynamic Eligibility Engine**: Automatically checks age limits, required degree levels, specific subjects, minimum experience, and domicile constraints before allowing application submission.
- **Smart Batch Allocation**: Ensures test centers never exceed capacity and seamlessly groups candidates by job titles.
- **Collision-free Roll Numbers**: Sequentially generates collision-free roll numbers within specific test centers.
- **Document Master Suite**: Generates automated Fee Challans, Roll Number Slips, and Hall Attendance Lists with candidate photos.
- **End-to-End Post-Test Processing**: Allows admin to upload attendance scans, upload CSV exam results, and automatically calculates candidate percentiles and percentages.

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
| **Super Admin** | `admin@pats.test` | `Admin@1234` | Can manage other admin users, assign roles, and access all features. |
| **Examiner** | `examiner@pats.test` | `password` | Can view assigned sessions and print logistics. |
| **Candidate** | `testcand@pats.test` | `password` | Sample candidate with a partially completed profile. |

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

1.  **Project Management**: Create a recruitment project (e.g., "MofIT 2026") and add specific jobs with their unique eligibility criteria.
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
    - Instantly print **Roll Number Slips**, **Attendance Sheets**, and **OMR Sheets** for each specific center.
6.  **Roll Number Issuance**: After scheduling, click **"Mark Ready / Notify"** at the batch level. This generates roll numbers and notifies candidates via SMS/Email.
7.  **Attendance & Results**:
    - Print **Attendance Sheets** and **Answer Sheets** for the examiners.
    - After the test, use **"Upload Results"** to import a CSV file.
    - Once reviewed, click **"Publish Results"** to make them live for candidates.

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

## 📄 File Templates

### Result Upload Format (CSV or Excel)
When an admin uploads the results for a project, the system expects a file (`.csv`, `.xlsx`, or `.xls`) with the following precise header names (case-insensitive):

| roll_no | score | total_marks | result_status |
|---|---|---|---|
| 1010130010001 | 85.5 | 100 | pass |
| 1010130010002 | 40.0 | 100 | fail |
| 1010130010003 | 0 | 100 | absent |

**Allowed Status Values:** `pass`, `fail`, `absent`, `withheld`.
