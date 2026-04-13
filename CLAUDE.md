# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Setup (first time)
```bash
composer run setup
```
This installs PHP/JS dependencies, copies `.env`, generates the app key, runs migrations, and builds assets.

### Development server
```bash
composer run dev
```
Starts four concurrent processes: Laravel dev server, queue worker, Pail log viewer, and Vite HMR. Access at `http://localhost:8000` (or `http://e-aduan.test` via Laragon).

### Tests
```bash
composer run test          # Run all tests (clears config cache first)
php artisan test --filter ExampleTest   # Run a single test class
php artisan test tests/Feature/ExampleTest.php  # Run a specific file
```
Tests use an in-memory SQLite database — no `.env` changes needed.

### Code style
```bash
./vendor/bin/pint           # Auto-fix code style (Laravel Pint / PSR-12)
```

### Useful Artisan commands
```bash
php artisan migrate         # Run pending migrations
php artisan migrate:fresh --seed  # Reset DB and seed
php artisan make:model Foo -mrc   # Model + migration + resource controller
php artisan tinker          # REPL
```

## Architecture

This is a fresh **Laravel 13** application using:
- **PHP 8.3+**, **Laravel Framework 13.x**
- **SQLite** as default database (file at `database/database.sqlite`; in-memory for tests)

@.claude/rules/frontend.md

### Request lifecycle
`routes/web.php` → `app/Http/Controllers/` → Blade views in `resources/views/`

`routes/console.php` holds Artisan closure commands.

### Key directories
| Path | Purpose |
|---|---|
| `app/Http/Controllers/` | HTTP controllers |
| `app/Models/` | Eloquent models |
| `app/Providers/` | Service providers |
| `database/migrations/` | Schema migrations |
| `database/factories/` | Model factories for tests/seeding |
| `database/seeders/` | Database seeders |
| `resources/views/` | Blade templates |
| `tests/Unit/` | Unit tests (no DB) |
| `tests/Feature/` | Feature tests (in-memory SQLite) |

### Testing environment
`phpunit.xml` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` for all test runs, along with `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, and `SESSION_DRIVER=array` — no external services required.
