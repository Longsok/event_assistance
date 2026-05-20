# Event Assistance — Smart Event Management System

A full-stack web application for planning and managing events. Built with Laravel 13, Livewire, and Tailwind CSS.

---

## Features

- Role-based access — Admin and Organizer roles
- Event creation with auto-generated tasks, budget, and schedule from templates
- Guest management with QR code check-in and invite links
- Real-time task checklist, budget tracker, and guest search via Livewire
- Attendance tracking with live counter
- Event completion with summary report
- Contributions tracking
- Admin panel for managing categories, templates, and users

---

## Requirements

Make sure your machine has the following installed before starting:

| Tool | Version | Download |
|------|---------|----------|
| PHP | 8.3 or higher | https://windows.php.net/download |
| Composer | 2.x | https://getcomposer.org/download |
| Node.js | 18 or higher | https://nodejs.org |
| MySQL | 5.7 or higher | via XAMPP https://www.apachefriends.org |
| Git | any | https://git-scm.com |

---

## Installation

### Step 1 — Clone the repository

```bash
git clone https://github.com/Longsok/event_assistance.git
cd event_assistance
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

### Step 3 — Install frontend dependencies

```bash
npm install
```

### Step 4 — Copy environment file

```bash
cp .env.example .env
```

On Windows:
```cmd
copy .env.example .env
```

### Step 5 — Generate application key

```bash
php artisan key:generate
```

### Step 6 — Configure the database

Open `.env` and update the database section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_assistance
DB_USERNAME=root
DB_PASSWORD=
```

> If you use XAMPP, the default MySQL username is `root` with no password.

### Step 7 — Create the database

Open phpMyAdmin at `http://localhost/phpmyadmin` and create a new database named `event_assistance`.

Or run via MySQL command line:

```sql
CREATE DATABASE event_assistance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 8 — Run migrations and seed

```bash
php artisan migrate
php artisan db:seed
```

### Step 9 — Install QR code package

```bash
composer require simplesoftwareio/simple-qrcode
```

### Step 10 — Create storage link

```bash
php artisan storage:link
```

---

## Running the Application

You need **three terminal windows** running at the same time:

**Terminal 1 — Start the web server**
```bash
php artisan serve
```

**Terminal 2 — Start the frontend compiler**
```bash
npm run dev
```

**Terminal 3 — Free for artisan commands**

Then open your browser and go to:
```
http://localhost:8000
```

---

## Default Login Credentials

After seeding, two accounts are created:

| Role | Email | Password |
|------|-------|----------|
| Admin | soklongyoung03@gmail.com | long123456 |
| Organizer | soklong260@gmail.com | long123456 |

> Admin panel is at `http://localhost:8000/admin/login`

---

## Admin Setup (Required Before Creating Events)

Log in as Admin and set up the following in order:

1. **Task Groups** — `/admin/task-groups`
   - Add groups like: Venue, Catering, Marketing, Logistics, Entertainment, Administration

2. **Event Categories** — `/admin/categories`
   - Add categories like: Wedding, Conference, Birthday Party

3. **Category Templates** — `/admin/category-templates`
   - Click a category, then add task templates with days before, anchor, and priority

4. **Budget Templates** — `/admin/budget-templates`
   - Click a category, then add budget line items with percentage allocation

5. **Schedule Templates** — `/admin/schedule-templates`
   - Click a category, then add sessions with duration and offset

Once templates are set up, creating an event as an Organizer will auto-generate tasks, budget items, and schedule sessions.

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/          — Admin panel controllers
│   ├── Organizer/      — Organizer panel controllers
│   └── Public/         — Public guest registration and check-in
├── Livewire/           — Real-time components
├── Models/             — Eloquent models
└── Services/           — TimelineEngine, BudgetEngine, ScheduleEngine

resources/
├── views/
│   ├── admin/          — Admin blade views
│   ├── dashboard/      — Organizer dashboard
│   ├── events/         — Event management pages
│   ├── guests/         — Guest management pages
│   ├── tasks/          — Task checklist
│   ├── budget/         — Budget tracker
│   ├── schedules/      — Schedule builder
│   ├── attendance/     — QR check-in
│   ├── contributions/  — Contributions
│   ├── invite/         — Invite card
│   ├── public/         — Public guest pages
│   ├── livewire/       — Livewire component views
│   └── layouts/        — App, guest, admin layouts
```

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 |
| Frontend | Blade, Tailwind CSS, Alpine.js |
| Real-time | Livewire 4 |
| Auth | Laravel Breeze + Spatie Permission |
| Database | MySQL / MariaDB |
| Build tool | Vite |
| QR Code | simplesoftwareio/simple-qrcode |

---

## Troubleshooting

**Vite manifest not found**
```bash
npm run dev
```
Keep this running while using the app.

**Class not found errors**
```bash
composer dump-autoload
php artisan optimize:clear
```

**Database errors**
```bash
php artisan migrate:fresh --seed
```
Warning: this will delete all existing data.

**Permission errors on storage**
```bash
php artisan storage:link
```

---

## License

This project is for educational purposes.
