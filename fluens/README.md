# Pomodoro To-Do (PHP + MySQL)

A to-do list with a built-in Pomodoro timer. Built for the Mini Project brief (J620-002-4:2020-C01).

## Run it
Requirements: PHP 8.0+ with PDO MySQL, and MySQL 5.7+ or MariaDB 10.3+.

1. Import the database: `mysql -u root -p < database.sql`
2. Edit `config.php` if your MySQL user or password differs.
3. Start a local server from this folder: `php -S localhost:8000`
4. Open http://localhost:8000

Demo logins (password for all: `Password123`):

| Username | Role    | Can do |
|----------|---------|--------|
| alice    | user    | Own tasks, categories, timer, dashboard |
| manager  | manager | Everything a user can, plus the Team report |
| admin    | admin   | Everything a manager can, plus Users (change roles, delete) |

New sign-ups always become `user`. Only an admin can promote them.
To test the timer quickly, set `FOCUS_MINUTES` to 1 in `config.php`.

## How it maps to the brief
| Criterion | Where |
|-----------|-------|
| HTML, CSS, PHP, MySQL | `pages/`, `assets/style.css`, `database.sql` |
| 4+ tables, 3+ links | 5 tables, 6 foreign keys (see ERD in `docs/`) |
| Authentication, 3 roles | `includes/auth.php`, `pages/login.php`, `pages/register.php`; roles: user, manager, admin |
| Role-based access | `includes/routes.php` (access rules) enforced in `index.php`; menu is built from the same table |
| Routing | `index.php?page=name` front controller |
| Data management (CRUD) | tasks, categories (create, read, update, delete), users (admin), session log |
| Use case and flowchart | `docs/use-case-and-flowchart.md` |
| Code quality and DRY | one `query()` helper, one route table, one layout, shared form helpers |

## Security notes
Prepared statements everywhere, `password_hash`/`password_verify`, CSRF tokens on every POST, output escaped with `e()`, ownership checked in every task/category query (`AND user_id = ?`).

## Folder map
```
index.php            front controller + access checks
config.php           DB settings and timer lengths
database.sql         schema and demo data
includes/            db, helpers, auth, routes, layout
pages/               one file per route
assets/              style.css, timer.js
docs/                diagrams
```
