# Job Tracker

A Laravel app for tracking job applications, interviews and follow-ups, with a stats dashboard.

Built with **Laravel 12**, **Laravel Breeze** (Blade + Tailwind CSS), **MySQL** and **Chart.js**.

## Features

- Register / log in / reset password (Laravel Breeze)
- Create, edit, view and delete job applications
- Status filter tabs with live counts, search by company or position, sortable columns, pagination
- Log interviews (date, type, notes) against each application
- Follow-up dates, with a "Follow up due" flag on open applications
- Kanban board: drag cards between status columns (or use the "Move to" menu on touch screens)
- Dashboard: totals, interview rate, status doughnut chart, applications-per-week chart, upcoming interviews
- Each user only sees and can change their own data (enforced with a policy)
- Server-side validation via Form Requests
- Feature tests covering CRUD, search, sorting, interviews, the dashboard and authorisation

## Running locally (Windows + XAMPP)

Requirements: PHP 8.2+, Composer, Node.js 20+, MySQL (XAMPP).

```bash
git clone <your-repo-url> job-tracker
cd job-tracker
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Create a MySQL database called `job_tracker` (phpMyAdmin or `mysql -u root`), then set these in `.env`:

```
DB_CONNECTION=mysql
DB_DATABASE=job_tracker
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Open http://127.0.0.1:8000 and log in with the demo account **test@example.com / password**.

## Deploying to Railway

1. Create a Railway project from this GitHub repo and add a **MySQL** database service.
2. On the app service set these variables:

   | Variable | Value |
   | --- | --- |
   | `APP_KEY` | output of `php artisan key:generate --show` |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | your Railway domain, e.g. `https://job-tracker.up.railway.app` |
   | `DB_CONNECTION` | `mysql` |
   | `DB_URL` | `${{MySQL.MYSQL_URL}}` |
   | `LOG_CHANNEL` | `stderr` |

3. Settings: build command `npm run build`, pre-deploy command `php artisan migrate --force`.
4. Under **Settings > Networking**, click **Generate Domain**.

## Running the tests

```bash
php artisan test
```

## Project structure (the interesting bits)

| File | What it does |
| --- | --- |
| `app/Enums/ApplicationStatus.php` | The list of statuses, their labels and badge colours |
| `app/Models/JobApplication.php` | Eloquent model (`belongsTo` User, `hasMany` Interview) |
| `app/Http/Controllers/JobApplicationController.php` | List/filter/search/sort, create, show, update, delete |
| `app/Http/Controllers/InterviewController.php` | Add and delete interviews |
| `app/Http/Controllers/DashboardController.php` | Stats queries and chart data |
| `resources/js/dashboard.js` | Draws the charts with Chart.js |
| `app/Http/Requests/JobApplicationRequest.php` | Validation rules |
| `app/Policies/JobApplicationPolicy.php` | Only the owner may edit/delete |
| `resources/views/job-applications/` | Blade views |
| `tests/Feature/JobApplicationTest.php` | Feature tests |
