# Strata Room Scheduler

A browser-based reservation system built with HTML, CSS, JavaScript, PHP, and SQLite for cPanel shared hosting.

## Project folders

- `frontend/` — page markup and browser assets (`assets/app.css`, `assets/app.js`)
- `backend/` — API endpoints and shared server helpers
- `authentication/` — sessions, sign-in checks, and CSRF protection
- `database/` — SQLite connection, schema, and the generated database file
- `index.php` — small root entry point that connects the folders

## Features

- Public monthly calendar and room request form. Public availability shows the department, room, and occupied time; meeting titles and request details remain staff-only.
- Requests await staff approval. Urgent in-person requests are limited to 30 minutes, checked on the server.
- Overlap checks include pending and approved reservations. Saves report conflicts instead of overwriting a newer booking.
- Admins have scheduling, room, and account-management access. They can view database records; only the Super Admin can edit them.
- Users can change their own passwords. Passwords use PHP's `password_hash()` and password hashes are never displayed.
- Public calendar and staff dashboard refresh every three seconds.

## cPanel setup

1. Confirm the hosting account has PHP 8.1+ and `pdo_sqlite` enabled.
2. Upload the full repository, keeping `index.php` in the document root and the four folders beside it. Set that folder as the domain/subdomain document root.
3. Make `database/` writable by the PHP account (usually permission `750` or `755`, depending on the host). The database and schema are initialized automatically.
4. Open the site. On first use, create the Super Admin account. The email field is prefilled with `superadmin@stratastaff.com`; enter your private password (12 characters minimum). Passwords are not committed to this repository.
5. Sign in, add rooms, and create Admin accounts. Use HTTPS before real users sign in.
6. Back up `database/reservations.sqlite` regularly from cPanel. Do not make it publicly downloadable.

The `.htaccess` files block direct web access to database and server-side folders. If your cPanel uses Nginx without Apache compatibility, configure equivalent deny rules in the host settings.

## Local development

```sh
php -S 127.0.0.1:8000
```

Open `http://127.0.0.1:8000`. PHP must have PDO SQLite enabled.

## Hosting note

SQLite needs persistent disk storage, so this deployment is intended for cPanel. Three-second polling makes schedule changes visible quickly; it is not a WebSocket push connection. Set up regular backups before production use.
