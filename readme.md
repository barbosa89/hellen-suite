# Hellen Suite

Hellen Suite is an application for managing hotels and similar businesses. It simplifies operational controls and administrative and management tasks.

The application is available as both a web application and a desktop application powered by [NativePHP Desktop](https://nativephp.com/docs/desktop/2). It is currently under development and is not yet ready for production.

Website: [hellensuite.com](https://hellensuite.com)

## Stack

- PHP 8.3 or later and Laravel 13.
- NativePHP Desktop 2 and Electron.
- Inertia 3 and Vue 3.
- Tailwind CSS 4 and Vite 8.
- SQLite.
- PHPUnit 12.

## Development

### Requirements

- PHP, Composer, and the extensions required by Laravel.
- Node.js and npm.
- SQLite.
- Platform build tools for working with Electron and generating native applications.

### Initial setup

Clone the repository and run the following command from its root:

```bash
composer run setup
```

This command installs the Composer and npm dependencies, creates `.env` from `.env.example`, generates `APP_KEY`, runs the migrations, and builds the frontend.

Prepare the NativePHP and Electron dependencies:

```bash
php artisan native:install --no-interaction
```

### Web development

```bash
composer run dev
```

This command starts the Laravel web server, Vite, and the development logs. The web database is located at `database/database.sqlite`.

Apply new migrations with:

```bash
php artisan migrate
```

### Desktop development

```bash
composer run native:dev
```

This command starts the Electron application and Vite. The main window configuration is located at `app/Providers/NativeAppServiceProvider.php`.

NativePHP uses its own database during native execution. Apply its migrations with:

```bash
php artisan native:migrate
```

### Quality and testing

```bash
# Test suite
php artisan test --compact

# Format modified PHP code
vendor/bin/pint --dirty --format agent

# Frontend ESLint; applies fixes automatically
npm run lint

# Production frontend build
npm run build
```

The Vite build downloads the fonts configured in `vite.config.js`, so it requires a network connection.

## NativePHP Security

A desktop application is installed on devices outside the developer's control. The user's system must therefore be treated as a potentially hostile environment, and no secret included in the application bundle should be assumed to be inaccessible.

The following principles apply to Hellen Suite:

- Do not bundle passwords, tokens, private keys, or infrastructure credentials in `.env` or the source code.
- Generate unique keys for each installation on first run whenever possible instead of sharing one key among all users.
- Use a robust protocol such as OAuth2 for private APIs, with unique, high-entropy, short-lived tokens. NativePHP recommends an expiration time of less than 48 hours.
- Always use HTTPS when sending data between the application and external services.
- Encrypt user-provided API keys if they must be stored in files or the database, and decrypt them only when needed.
- Restrict file access to expected locations, primarily the `appdata` directory, the user's home directory, and its subdirectories, using the `Storage` disks provided by NativePHP.
- Keep Laravel's CSRF and CORS protections enabled, and do not expose unnecessary routes or native actions.

NativePHP runs local servers to connect Laravel and Electron. Their communication uses authenticated HTTP requests with a dynamic key regenerated on every startup. **Never bypass or replace this authentication**, as doing so could allow other applications or websites to call the internal APIs.

NativePHP automatically applies the global `PreventRegularBrowserAccess` middleware to production builds. This middleware restricts route access to requests originating from the WebView that started the application.

The PHP process runs with the current user's permissions and can therefore access anything available to that user. Every operation involving files, processes, or system resources must be validated and restricted to the minimum required scope. Other installed software could also use the bundled PHP executable, so users should only install applications from trusted sources.

Before publishing, review `cleanup_env_keys` in `config/nativephp.php` and verify that it removes all credentials used exclusively during the build or publication process. This project removes `GITHUB_*`, `AWS_*`, `AZURE_*`, `DO_SPACES_*`, Apple credentials, and other sensitive values.

Read the complete [NativePHP Desktop 2 security guide](https://nativephp.com/docs/desktop/2/digging-deeper/security) before distributing a release.

## Desktop Build

The build process packages Laravel, the frontend, Electron, and the required runtime into a distributable application. It targets one operating system at a time.

Before building a release:

1. Run the tests and `npm run build`.
2. Increment `NATIVEPHP_APP_VERSION` in `.env`.
3. Review the migrations because NativePHP only runs them on installed systems when the version changes.
4. Configure the appropriate code signing for Windows or macOS.
5. Test the installer on every target platform.

Build for the current platform and architecture:

```bash
php artisan native:build
```

Build for a specific operating system:

```bash
php artisan native:build mac
php artisan native:build win
php artisan native:build linux
```

Cross-compilation is not supported for every platform combination. Test artifacts on the operating system where they will be distributed. On macOS, the application must be signed and notarized to run correctly on other devices and receive automatic updates.

## Publishing to GitHub Releases

Hellen Suite uses the public [barbosa89/hellen-suite](https://github.com/barbosa89/hellen-suite) repository as its publishing and update provider.

### Local configuration

Add these variables to the local `.env` file:

```dotenv
NATIVEPHP_APP_VERSION=1.0.0
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github

GITHUB_OWNER=barbosa89
GITHUB_REPO=hellen-suite
GITHUB_PRIVATE=false
GITHUB_TOKEN=github_pat_REPLACE_WITH_THE_REAL_TOKEN
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_CHANNEL=latest
GITHUB_RELEASE_TYPE=draft
```

The `.env` file is excluded from Git. Never add the real token to `.env.example`, `config/nativephp.php`, this README, or any other versioned file. NativePHP removes `GITHUB_*` variables from the `.env` file included in the final application bundle.

Because the repository is public, do not define `GITHUB_AUTOUPDATE_TOKEN`. Installed applications can access public releases without authentication; `GITHUB_TOKEN` only authorizes artifact uploads during publication.

### Obtaining `GITHUB_TOKEN`

Create a [fine-grained personal access token](https://github.com/settings/personal-access-tokens/new) with the following configuration:

| Field | Value |
| --- | --- |
| Token name | `Hellen Suite NativePHP Publisher` |
| Resource owner | `barbosa89` |
| Repository access | `Only select repositories` |
| Selected repositories | `hellen-suite` |
| Contents | `Read and write` |
| Metadata | `Read-only`, assigned automatically |

Choose an expiration date, generate the token, and copy it immediately; GitHub only displays it once. Store it as `GITHUB_TOKEN` in the local `.env` file or as a CI secret.

Clear cached configuration after changing these variables:

```bash
php artisan config:clear
```

### Publishing a release

1. Increment `NATIVEPHP_APP_VERSION`; for example, from `1.0.0` to `1.1.0`.
2. Run the tests, build the frontend, and test `php artisan native:build`.
3. Create a draft release on GitHub.
4. If use the `v`-prefixed version as its tag; for version `1.1.0`, create `v1.1.0`.
5. Publish the artifacts for each platform with `native:publish`.
6. Verify the attached artifacts and publish the release.
7. Validate the update from an installation of the previous version.

```bash
php artisan native:publish

# Or specify the target operating system
php artisan native:publish mac
php artisan native:publish win
php artisan native:publish linux
```

Running `native:publish` again while the release remains a draft updates its artifacts. Draft releases are unavailable to users and must be published after verification.

### Troubleshooting repository detection

NativePHP Desktop may fail during `native:publish` with this message even when `GITHUB_OWNER` and `GITHUB_REPO` are correctly defined:

```text
Cannot detect repository by .git/config. Please specify "repository" in the package.json.
```

This is a [known NativePHP Desktop issue](https://github.com/NativePHP/desktop/issues/102). Laravel reads `NATIVEPHP_UPDATER_ENABLED=true` from `.env`, but NativePHP does not pass that value to the Electron Builder process. Electron Builder consequently receives no `publish` configuration and tries to infer the repository from `.git/config` inside `vendor/nativephp/desktop/resources/electron`, where no Git repository exists.

Clear the Laravel configuration cache and provide the variable directly to the publishing process:

```bash
php artisan config:clear

# Current platform and architecture
NATIVEPHP_UPDATER_ENABLED=true php artisan native:publish

# Windows x64
NATIVEPHP_UPDATER_ENABLED=true php artisan native:publish win x64
```

Do not fix this by adding `repository` to `vendor/nativephp/desktop/resources/electron/package.json`. Files inside `vendor` are dependency internals and any local change will be lost on the next Composer installation or update.

The upstream fix is for NativePHP's `BuildCommand::getEnvironmentVariables()` to pass the effective updater state to Electron Builder:

```php
'NATIVEPHP_UPDATER_ENABLED' => config('nativephp.updater.enabled') ? 'true' : 'false',
```

### Troubleshooting NSIS on Apple Silicon

Publishing a Windows installer from an Apple Silicon Mac may fail with an error similar to:

```text
Cannot spawn .../electron-builder/nsis-3.0.4.1/.../mac/makensis:
Error: spawn Unknown system error -86
```

macOS error `-86` means that the executable uses an unsupported CPU architecture. Electron Builder's NSIS package includes an Intel `x86_64` build of `makensis`, while Apple Silicon Macs run `arm64`. Rosetta 2 is therefore required to execute this tool during a Windows cross-build.

Install Rosetta using Apple's system updater and follow the prompts:

```bash
softwareupdate --install-rosetta
```

Verify that the cached NSIS executable can run:

```bash
"$HOME/Library/Caches/electron-builder/nsis-3.0.4.1/nsis-3.0.4.1-1mx3n/mac/makensis" -VERSION
```

It should print the NSIS version instead of `bad CPU type in executable`. Then retry the publication with the NativePHP updater workaround:

```bash
php artisan config:clear
NATIVEPHP_UPDATER_ENABLED=true php artisan native:publish win x64
```

If the same error remains after Rosetta is installed, remove only the generated NSIS cache so Electron Builder can download it again:

```bash
rm -rf "$HOME/Library/Caches/electron-builder/nsis-3.0.4.1"
```

Building and publishing from a Windows machine or Windows CI runner is the alternative that avoids cross-compilation and Rosetta entirely.

## Release Documentation

See [NATIVEPHP_BUILD_PUBLICACION_ACTUALIZACIONES.md](NATIVEPHP_BUILD_PUBLICACION_ACTUALIZACIONES.md) for the complete guide to signing, building, publishing, automatic updates, updater events, and migrations.

## Security Vulnerabilities

If you discover a security vulnerability within Phenix, please send an e-mail to Omar Barbosa via [contacto@omarbarbosa.com](mailto:contacto@omarbarbosa.com). All security vulnerabilities will be promptly addressed.

## License

The Phenix framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
