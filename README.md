# PATS — Pakistan Aptitude Testing Service

A robust, modern Recruitment and Testing Management System built with a custom PHP MVC engine. PATS is designed for high-performance candidate management, project tracking, automated test scheduling, and result processing.

## Key Features

- **Candidate Portal**: Profile management, educational history, and 1-click job applications.
- **Admin Dashboard**: Real-time stats, project/job CRUD, and test center management.
- **Automated Scheduling**: Seat capacity checks and dynamic slot allocation.
- **Financial Module**: Automated PDF Bank Challan generation and payment verification.
- **Results Engine**: CSV bulk upload, automated result mapping, and CNIC-based result lookup.
- **Security**: Role-based access control (RBAC), CSRF protection, and OTP-based verification.
- **Premium UI**: NTS-inspired landing page with a modern, responsive design.

---

## Prerequisites

- **PHP**: 8.1 or higher
- **Web Server**: Apache (with `mod_rewrite` enabled) or Nginx
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Dependency Manager**: [Composer](https://getcomposer.org/)

---

## Installation & Setup

### 1. Windows (Using XAMPP / Laragon)

1. **Clone the Project**:
   Clone the repository into your web root directory (e.g., `C:\xampp\htdocs\pats` or `C:\laragon\www\pats`).
   ```bash
   git clone -b oop-rewrite https://github.com/asifraza2081/pats- .
   ```

2. **Install Dependencies**:
   Open a terminal in the project root and run:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   Copy the `.env.example` (or use the existing `.env`) and update your credentials:
   ```bash
   cp .env.example .env
   ```
   Main settings to update:
   - `APP_URL`: e.g., `http://localhost/pats/public` or `http://pats.test`
   - `DB_NAME`, `DB_USER`, `DB_PASS`

4. **Database Setup**:
   - Create a new database named `pats` in phpMyAdmin or your SQL client.
   - Import the schema: `database/schema.sql`.
   - Run the seeder to create initial data (admin user, projects, jobs):
     ```bash
     php database/seed.php
     ```

5. **Apache Configuration**:
   Ensure `AllowOverride All` is enabled in your Apache config so the `.htaccess` files can handle routing. If using Laragon, it will automatically handle the virtual host.

### 2. Linux / Production (Nginx)

If using Nginx, you must forward all requests to `public/index.php`. Example config snippet:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Ensure the `storage` and `public/uploads` directories have write permissions:
```bash
chmod -R 775 storage public/uploads
chown -R www-data:www-data storage public/uploads
```

---

## Default Credentials

After running the seeder (`database/seed.php`), you can use the following accounts:

- **Super Admin**: 
  - Email: `admin@pats.test`
  - Password: `password123`
- **Candidate Account**:
  - CNIC: `3520212345671`
  - Password: `password123`

---

## Project Structure

- `app/`: Core MVC framework and Application logic.
  - `Controllers/`: HTTP Request handlers.
  - `Models/`: Database interactions.
  - `Core/`: Basic engine classes (Router, Database, Session, etc.).
  - `Services/`: External integrations (SMS, FileUpload).
- `config/`: Application, Database, and Route configurations.
- `database/`: SQL schema and seeders.
- `public/`: Web root (Assets, Uploads, Entry point).
- `views/`: UI templates and layouts.
- `storage/`: Logs and temporary files.

---

## Support & Contributing

This project is on the `oop-rewrite` branch. For contributions, please create a new feature branch and submit a pull request.
