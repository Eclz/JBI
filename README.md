# JBI University Management System

JBI is a web-based university management and learning platform built for Johnson Bible Institute. It brings admissions, academic administration, teaching, student services, and finance into one role-based system.

## What the system does

### Administration

- Manage users, roles, students, faculty, departments, programs, courses, academic years, and semesters
- Review applications and payment evidence
- Manage course enrollment, fees, and student records
- View academic, attendance, enrollment, faculty, course, and financial reports
- Publish announcements and configure institutional settings

### Faculty

- Manage assigned courses and learning materials
- Create assignments, quizzes, and examinations
- Record attendance
- Review submissions and maintain grades

### Students

- Apply online and upload application payment evidence
- Access enrolled courses and learning materials
- Submit assignments and take quizzes or examinations
- View grades, attendance, fees, announcements, and notifications
- Request a program change and participate in course forums

The system also provides profile management, password recovery, support requests, discussion forums, email notifications, and receipt verification.

## Technology

- PHP 8.2 or newer
- Laravel 12
- Laravel Sanctum
- SQLite by default; MySQL and other Laravel-supported databases can also be configured
- Tailwind CSS 4
- Vite 6
- Node.js and npm

## Requirements

Install the following before setting up the project:

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- Node.js 18+ and npm
- A database driver for your chosen database, such as PDO SQLite or PDO MySQL

## Local setup

1. Clone the repository and enter its directory:

   ```bash
   git clone https://github.com/Eclz/JBI.git
   cd JBI
   ```

2. Install the PHP and JavaScript dependencies:

   ```bash
   composer install
   npm install
   ```

3. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set the application details in `.env`:

   ```env
   APP_NAME="JBI University"
   APP_URL=http://localhost:8000
   ```

5. Configure the database.

   For SQLite:

   ```bash
   touch database/database.sqlite
   ```

   Keep these values in `.env`:

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/JBI/database/database.sqlite
   ```

   Alternatively, configure MySQL:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jbi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Create the database tables and public file-storage link:

   ```bash
   php artisan migrate
   php artisan storage:link
   ```

7. Build the frontend assets:

   ```bash
   npm run build
   ```

8. Start the application:

   ```bash
   php artisan serve
   ```

   Open [http://localhost:8000](http://localhost:8000).

## Development mode

To run the web server, queue worker, application log viewer, and Vite development server together:

```bash
composer run dev
```

Because the default configuration uses database-backed sessions, cache, and queues, migrations must be completed before starting the application. Keep a queue worker running when testing queued email notifications.

## Demo data and accounts

To populate a development database with a realistic set of academic records:

```bash
php artisan migrate:fresh --seed
```

> This command deletes all existing database data. Use it only for a disposable development database.

For a smaller set of role-based test accounts, run:

```bash
php artisan db:seed --class=CreateTestUsersSeeder
```

All test accounts use the password `password123`:

| Role | Email |
| --- | --- |
| Administrator | `admin@jbiuniversity.com` |
| Faculty | `faculty@jbiuniversity.com` |
| Student | `student@jbiuniversity.com` |
| Parent | `parent@jbiuniversity.com` |

These credentials are for local testing only. Never deploy them to production.

## Demo environment reset

The demo reset command deletes all application records, rebuilds the schema, and loads a small representative dataset with the four operator accounts above:

```bash
DEMO_RESET_ALLOWED=true php artisan demo:reset
```

For automated deployments, use `--force` only after creating and validating a database backup:

```bash
DEMO_RESET_ALLOWED=true php artisan demo:reset --force
```

The command refuses to run unless `DEMO_RESET_ALLOWED=true`. Never enable it on a production system that contains real user data.

For an isolated demo deployment, use a separate database or a dedicated table prefix and application URL:

```env
APP_DATA_MODE=demo
APP_URL=https://portal.jbiuniversity.com/demo
DB_PREFIX=demo_
DEMO_RESET_ALLOWED=true
```

The production deployment must use `APP_DATA_MODE=production`, an empty `DB_PREFIX`, and `DEMO_RESET_ALLOWED=false`.

When `DB_PREFIX` is set, `demo:reset` deletes only tables beginning with that prefix. It never runs the database-wide fresh migration used by an isolated demo database.

To initialize an empty production database with only essential academic structure and the administrator account, run:

```bash
php artisan migrate:fresh --force --seeder=Database\\Seeders\\ProductionDatabaseSeeder
```

## Email configuration

Email is written to the application log by default. To send real password-reset, application, and admission messages, configure the `MAIL_*` values in `.env`. For example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admissions@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

After changing environment values, clear cached configuration:

```bash
php artisan optimize:clear
```

## Testing

Run the automated test suite with:

```bash
composer test
```

## Production notes

Before deployment:

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use a strong, unique `APP_KEY`
- Configure the production database, mail server, and filesystem
- Point the web server document root to the `public` directory
- Run migrations with `php artisan migrate --force`
- Build assets with `npm run build`
- Run a persistent queue worker
- Configure the scheduler to execute `php artisan schedule:run` every minute
- Ensure `storage` and `bootstrap/cache` are writable by the web server
- Cache configuration and routes with `php artisan optimize`

## Author

[levengalvin@gmail.com](mailto:levengalvin@gmail.com)
