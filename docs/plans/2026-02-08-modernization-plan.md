# Modernization & PSR Compliance Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Modernize the app to PSR-12/PSR-4 with strict types, DI, composer autoload, CSRF/session utilities, and safer uploads/shortener handling.

**Architecture:** Shift to `src/` namespaced classes loaded via Composer. Introduce a PDO factory/container, shared session/CSRF helpers, and controllers/services for auth, uploads, and URL shortening. Keep public entry files thin and move logic into services; store uploads outside webroot with execution blocked.

**Tech Stack:** PHP 8.1+, Composer (autoload, phpcs, phpunit), PDO, GD (for images), vlucas/phpdotenv, Monolog.

---

### Task 1: Tooling & Autoload

**Files:**
- Create: `composer.json`, `.gitignore`, `.env.example`
- Modify: `README.md`

**Step 1: Write composer.json and .gitignore**
```bash
cat > composer.json <<'EOF'
{ ... psr-4 App\\, deps: dotenv, monolog, phpunit, phpcs ... }
EOF
printf "vendor/\n.env\nstorage/logs/\nstorage/uploads/\nstorage/thumbnails/\n" >> .gitignore
```

**Step 2: Install deps**
Run: `composer install`
Expected: vendor directory created, autoload files present.

**Step 3: Scaffold .env.example**
```bash
cat > .env.example <<'EOF'
DB_HOST=localhost
DB_NAME=project
DB_USER=root
DB_PASS=
APP_ENV=development
APP_URL=http://localhost:8000
MAX_FILE_SIZE=10485760
ALLOWED_EXTENSIONS=jpg,png
EOF
```

**Step 4: Update README**
Add composer setup, env copy instructions, and new structure summary.

### Task 2: Core Infrastructure (Session, CSRF, Config)

**Files:**
- Create: `src/Utils/Session.php`, `src/Utils/Csrf.php`, `src/Config/Config.php`
- Modify: entry scripts (`index.php`, `upload.php`, `shorten.php`, `main.php`, handlers in `includes/`)

**Step 1: Add session helper**
```php
// src/Utils/Session.php
declare(strict_types=1);
namespace App\Utils;
final class Session { public static function start(): void { /* secure params + session_start */ } }
```

**Step 2: Add CSRF helper**
```php
// src/Utils/Csrf.php
final class Csrf { public static function token(): string { ... } public static function verify(string $t): bool { ... } }
```

**Step 3: Add config loader**
Use dotenv to read `.env`, expose getters for DB creds, upload paths, limits.

**Step 4: Wire into entry points**
Replace raw `session_start` with `Session::start()`; inject hidden CSRF inputs and verify in handlers.

### Task 3: Database & DI Container

**Files:**
- Create: `src/Database/Connection.php`, `src/Container.php`
- Modify: `Classes/Databasehandler.php`, `Classes/Signup.php`, `Classes/Login.php`, `Classes/ImageHandler.php`, `Classes/Urlshortener.php`

**Step 1: Build PDO factory**
`Connection::make(Config $config): PDO` with errmode exception, UTF-8, no emulation.

**Step 2: Add Container**
Static container to share PDO + config instances.

**Step 3: Refactor classes to DI**
Add namespaces `App\...`, `declare(strict_types=1);`, constructor-inject PDO/config; remove singleton use.

**Step 4: Update includes to use container**
Replace `Databasehandler::getInstance()` calls with `$pdo = Container::db();`.

### Task 4: Services & Controllers

**Files:**
- Create: `src/Services/AuthService.php`, `src/Services/ImageService.php`, `src/Services/UrlService.php`
- Create: `src/Controllers/AuthController.php`, `src/Controllers/ImageController.php`, `src/Controllers/UrlController.php`
- Modify: `includes/*.inc.php` and public pages to call controllers/services.

**Step 1: AuthService**
Methods: `login`, `signup`, `logout`; use Validator, Session, Csrf; regenerate IDs; password hash/verify.

**Step 2: ImageService**
Methods: `upload`, `delete`, `list`; enforce size/mime via finfo; random filenames; move uploads; optional re-encode; ownership check.

**Step 3: UrlService**
Methods: `shorten`, `resolve`; validate scheme; collision retry; optional APCu cache.

**Step 4: Controllers**
Thin classes mapping request data to services; return redirects/responses via helper.

### Task 5: Routing & Public Entry

**Files:**
- Create: `public/index.php` (front controller), move assets into `public/`
- Modify: existing `index.php`, `upload.php`, `main.php`, `shorten.php` to forward or relocate to `views/`

**Step 1: Set up front controller**
Load autoload, dotenv, start session, simple router mapping to controllers.

**Step 2: Move legacy pages to views**
Convert to templates requiring CSRF tokens; ensure only `public/` is web-accessible.

### Task 6: Standards & Tests

**Files:**
- Modify: all PHP files to add `declare(strict_types=1);`, type hints, namespaces, docblocks
- Create: `tests/Unit/ValidatorTest.php`, `tests/Unit/AuthServiceTest.php` (mocks), `phpunit.xml`

**Step 1: Add declarations and types**
Pass through all classes and handlers; align with PSR-12 formatting.

**Step 2: Add PHPUnit config and base tests**
Cover validators, URL service collision handling, image validator (using temp files).

**Step 3: Lint and test**
Run: `composer lint` then `composer test`; fix findings.

### Task 7: Migrations & Security Hardening

**Files:**
- Create: `migrations/001_add_indexes.sql`
- Modify: uploads directory permissions/.htaccess if needed (already present but relocate under storage)

**Step 1: Write migration for indexes**
Indexes on `photos(user_id, reg_date)`, `urls(short_code unique)`, `users(email_address unique)`.

**Step 2: Ensure uploads non-executable**
If uploads move outside webroot, keep `.htaccess` for defense-in-depth; adjust paths in ImageService.

**Step 3: Document migration/apply**
Add README snippet for running migration manually.
