# Strata Room Scheduler

A browser-based room reservation system using HTML, CSS, JavaScript, PHP, and SQLite. It is designed for cPanel shared hosting with PHP and PDO SQLite enabled. The public calendar refreshes every three seconds and shows only the department, room, and occupied time. Request descriptions and requestor details stay staff-only.

## Features

- Public monthly calendar and room request form; requests remain pending until staff review.
- Urgent in-person requests are limited to 30 minutes, checked on the server.
- Overlap checks include pending and approved reservations. Saves are protected by SQLite transactions and report conflicts instead of overwriting a newer schedule.
- Super Admin creates Admin accounts. Admins have scheduling, room, and account viewing access, and can view database records. Only the Super Admin can edit database records.
- Users can change their own passwords. Passwords use PHP's `password_hash()` and are never displayed in the database screen.
- Polling every three seconds keeps the public calendar and staff dashboard current.

## cPanel setup

1. Confirm the hosting account has PHP 8.1+ and `pdo_sqlite` enabled.
2. Upload the project files to the target folder. Set that folder as the domain/subdomain document root.
3. Make `data/` writable by the PHP account (usually directory permission `750` or `755`, as required by your host). The database is created automatically at first visit.
4. Open the site. The first-run screen creates the Super Admin. It starts with `superadmin@stratastaff.com`; enter the private password you want to use (at least 12 characters). The password is not stored in this repository.
5. Sign in, add rooms, then create Admin accounts. Use HTTPS before real users sign in.
6. Back up `data/reservations.sqlite` regularly from cPanel. Do not make it publicly downloadable.

The included `.htaccess` blocks direct access to the SQLite database and disables directory listing. If your cPanel uses Nginx without Apache compatibility, set equivalent deny rules in the host configuration.

## Local development

```sh
php -S 127.0.0.1:8000
```

Open `http://127.0.0.1:8000`. PHP must have PDO SQLite enabled.

## Notes

SQLite is suitable for a small internal scheduling deployment. It is not a shared Vercel database: the database must live on persistent cPanel storage. Polling makes updates visible within about three seconds; it is not a push/WebSocket connection. Configure automated backups on the hosting account before production use.
