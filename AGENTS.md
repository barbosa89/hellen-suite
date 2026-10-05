# Hellen Suite Agent Guide

## Project Shape

- This is an offline-capable hotel PMS delivered as both a Laravel web app and a NativePHP/Electron desktop app. The UI is Inertia 3 + Vue 3 + Tailwind 4; the application is still pre-production.
- Check `composer.lock` and `package-lock.json` before using package APIs. Use Laravel Boost `search-docs` for version-specific Laravel, Inertia, NativePHP, PHPUnit, or Tailwind behavior.
- Activate the matching project skill before Laravel backend, Inertia/Vue, Tailwind, testing, or frontend-design work.

## Setup And Commands

- Initial setup: `composer run setup`. It installs Composer packages, creates `.env`, generates `APP_KEY`, migrates the web database, runs `npm install --ignore-scripts`, and builds assets.
- Desktop setup is a separate required step: `php artisan app:native:install --no-interaction`. The wrapper also runs Electron's postinstall, which the npm setup intentionally skips.
- Web development: `composer run dev`.
- Desktop development: `composer run native:dev`.
- Focused test: `php artisan test --compact tests/Feature/Reservations/ReservationStoreTest.php` or add `--filter=testName`. `composer test` clears cached configuration before running the full suite.
- After PHP changes run `vendor/bin/pint --dirty --format agent` and affected tests.
- After JS/Vue changes run `npm run lint`; this command applies ESLint fixes. Run `npm run build` for a production asset check. The build downloads the configured Bunny font and therefore needs network access.
- There is no tracked CI or pre-commit hook enforcing these checks; run the relevant commands locally.

## Architecture

- `routes/web.php` uses nested, scoped hotel routes. Keep tenant records scoped to the route's `Hotel`; tests explicitly reject cross-hotel records.
- For business writes, follow the existing flow: Form Request validation -> readonly object in `app/Data` -> injected class in `app/Actions`. Keep controllers focused on HTTP/Inertia concerns.
- Reservation, stay, payment, folio, and cash actions contain the transaction, row-lock, inventory-lock, event-history, and idempotency boundaries. Do not move those writes into controllers or Vue components, and preserve existing locks when extending workflows.
- Inertia pages live in `resources/js/Pages`; shared layouts and controls live in `resources/js/Layouts` and `resources/js/Components`. The frontend entrypoint is `resources/js/app.js`; the document shell is `resources/views/app.blade.php`.
- `EnsureAppConfigured` is global web middleware. Only locale and settings routes bypass it; unexpected test or browser redirects to settings usually mean required settings such as currency are missing.

## Data And Generated Files

- Do not confuse the three SQLite contexts: web uses `database/database.sqlite`, NativePHP uses its own `database/nativephp.sqlite` during native execution and needs `php artisan native:migrate`, and PHPUnit uses in-memory SQLite.
- `resources/js/lang/locales.js` is generated from `lang/**/*.php` by `php artisan vue:translations`; never edit it directly. `npm run dev` watches language files, while `npm run build` regenerates it.
- Keep English and Spanish translation files in sync when adding user-facing copy.
- The base `tests/TestCase.php` fakes `GeneralSettings` with currency `COP`. Feature tests generally use `RefreshDatabase` and create required reference records with factories.

## NativePHP Constraints

- Treat every desktop bundle and host machine as untrusted. Never bundle secrets or weaken NativePHP's authenticated internal HTTP communication; review `cleanup_env_keys` in `config/nativephp.php` when adding credentials.
- Before a desktop release, run tests and `npm run build`, bump `NATIVEPHP_APP_VERSION`, and review migrations. Installed native apps apply migrations when the application version changes.
