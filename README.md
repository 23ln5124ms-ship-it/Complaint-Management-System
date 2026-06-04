# ComplainHub — Complaint Management System

ComplainHub is a web-based system where users can file complaints and admins can manage and respond to them. It was built using the Laravel PHP framework.

## Developers

- Mariel Viray
- Marie Anthonette Rodrigueza
- Paul Vincent Pavo

## How the System Works

There are two types of users in the system:

**Regular Users** can sign up and log in, submit a complaint with a title, description, category, and priority, attach a file like a screenshot or document, check the status of their complaints, and reply to their complaint thread. They can also edit or delete their own complaints, but only while the status is still pending.

**Admins** can see all complaints from all users, change the status, priority, and category of any complaint, post replies, manage categories and tags, view reports and analytics, and export complaint data as PDF, CSV, XLSX, or JSON.

### Complaint Flow

1. A user submits a complaint through the form.
2. The system assigns a unique ticket number like `CMP-2026-00001`.
3. The complaint starts as `pending`.
4. An admin reviews it, changes the status to `in_progress`, and posts a response.
5. Once done, the admin marks it as `resolved` or `closed`.
6. The user can check for updates and reply at any time.

## Installation & Setup

### Requirements

- PHP 8.4
- Composer
- Laravel 13
- MySQL or SQLite

### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/23ln5124ms-ship-it/Complaint-Management-System.git
   cd Complaint-Management-System
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file and generate the app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set up your database in the `.env` file, then run the migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Create the storage link for file uploads:
   ```bash
   php artisan storage:link
   ```

6. Start the local server:
   ```bash
   php artisan serve
   ```

### Default Admin Account

```
Email:    admin@complaints.test
Password: password
```

## Live Deployment

[https://complaint-management-system-production-ea60.up.railway.app/login](https://complaint-management-system-production-ea60.up.railway.app/login)

## Tech Stack

- **Framework:** Laravel 13 (PHP 8.4)
- **Frontend:** Blade Templates, CSS
- **Database:** MySQL
- **Hosting:** Railway
