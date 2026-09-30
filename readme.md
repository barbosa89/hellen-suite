# Hellen Suite

Hellen Suite es una aplicación para la administración de hoteles y negocios similares. Facilita los controles operativos y la ejecución de tareas administrativas y de gestión.

La aplicación se distribuye como web y como aplicación de escritorio mediante [NativePHP Desktop](https://nativephp.com/docs/desktop/2). Actualmente está en desarrollo y todavía no está preparada para producción.

Sitio web: [hellensuite.com](https://hellensuite.com)

## Stack

- PHP 8.3 o superior y Laravel 13.
- NativePHP Desktop 2 y Electron.
- Inertia 3 y Vue 3.
- Tailwind CSS 4 y Vite 8.
- SQLite.
- PHPUnit 12.

## Desarrollo

### Requisitos

- PHP, Composer y las extensiones requeridas por Laravel.
- Node.js y npm.
- SQLite.
- Herramientas de compilación de la plataforma para trabajar con Electron y generar aplicaciones nativas.

### Instalación inicial

Clonar el repositorio y ejecutar desde su raíz:

```bash
composer run setup
```

Este comando instala las dependencias de Composer y npm, crea `.env` a partir de `.env.example`, genera `APP_KEY`, ejecuta las migraciones y compila el frontend.

Para preparar las dependencias de NativePHP y Electron:

```bash
php artisan app:native:install --no-interaction
```

### Desarrollo web

```bash
composer run dev
```

El comando inicia el servidor web de Laravel, Vite y los logs de desarrollo. La base de datos web se encuentra en `database/database.sqlite`.

Si se crean migraciones nuevas, aplicarlas con:

```bash
php artisan migrate
```

### Desarrollo de escritorio

```bash
composer run native:dev
```

Este comando inicia la aplicación de Electron y Vite. La configuración de la ventana principal se encuentra en `app/Providers/NativeAppServiceProvider.php`.

NativePHP utiliza su propia base de datos durante la ejecución nativa. Las migraciones se aplican con:

```bash
php artisan native:migrate
```

### Calidad y pruebas

```bash
# Suite de pruebas
php artisan test --compact

# Formato del código PHP modificado
vendor/bin/pint --dirty --format agent

# ESLint del frontend; aplica correcciones automáticamente
npm run lint

# Build de producción del frontend
npm run build
```

El build de Vite descarga las fuentes configuradas en `vite.config.js`, por lo que necesita conexión de red.

## Build de escritorio

El build empaqueta Laravel, el frontend, Electron y el runtime requerido en una aplicación distribuible. Se genera un sistema operativo a la vez.

Antes de construir una versión:

1. Ejecutar las pruebas y `npm run build`.
2. Incrementar `NATIVEPHP_APP_VERSION` en `.env`.
3. Revisar las migraciones, porque NativePHP solo las ejecuta en los equipos instalados cuando cambia la versión.
4. Configurar la firma de código correspondiente a Windows o macOS.
5. Probar el instalador en cada plataforma objetivo.

Build para la plataforma y arquitectura actuales:

```bash
php artisan native:build
```

Build indicando el sistema operativo:

```bash
php artisan native:build mac
php artisan native:build win
php artisan native:build linux
```

La compilación cruzada no está soportada para todas las combinaciones. Los artefactos deben probarse en el sistema operativo donde se distribuirán. En macOS, la aplicación debe estar firmada y notarizada para funcionar correctamente en otros equipos y recibir actualizaciones automáticas.

## Publicación en GitHub Releases

Hellen Suite utiliza el repositorio público [barbosa89/hellen-suite](https://github.com/barbosa89/hellen-suite) como proveedor de publicación y actualizaciones.

### Configuración local

Añadir estas variables al `.env` local:

```dotenv
NATIVEPHP_APP_VERSION=1.0.0
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github

GITHUB_OWNER=barbosa89
GITHUB_REPO=hellen-suite
GITHUB_PRIVATE=false
GITHUB_TOKEN=github_pat_REEMPLAZAR_CON_EL_TOKEN_REAL
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_CHANNEL=latest
GITHUB_RELEASE_TYPE=draft
```

El `.env` está excluido de Git. El token real nunca debe añadirse a `.env.example`, `config/nativephp.php`, el README ni otro archivo versionado. NativePHP elimina las variables `GITHUB_*` del `.env` incluido en el paquete final.

Como el repositorio es público, `GITHUB_AUTOUPDATE_TOKEN` no debe definirse. Las aplicaciones instaladas pueden consultar las releases públicas sin autenticación; `GITHUB_TOKEN` solo autoriza la subida de artefactos durante la publicación.

### Obtener `GITHUB_TOKEN`

Crear un [fine-grained personal access token](https://github.com/settings/personal-access-tokens/new) con esta configuración:

| Campo | Valor |
| --- | --- |
| Token name | `Hellen Suite NativePHP Publisher` |
| Resource owner | `barbosa89` |
| Repository access | `Only select repositories` |
| Selected repositories | `hellen-suite` |
| Contents | `Read and write` |
| Metadata | `Read-only`, asignado automáticamente |

Elegir una fecha de expiración, generar el token y copiarlo inmediatamente; GitHub solo lo muestra una vez. Guardarlo como `GITHUB_TOKEN` en el `.env` local o como secreto de CI.

Después de cambiar las variables, limpiar la configuración cacheada:

```bash
php artisan config:clear
```

### Publicar una versión

1. Incrementar `NATIVEPHP_APP_VERSION`; por ejemplo, de `1.0.0` a `1.1.0`.
2. Ejecutar las pruebas, compilar el frontend y probar `php artisan native:build`.
3. Crear una release en borrador en GitHub.
4. Usar la versión con prefijo `v` como tag; para `1.1.0`, crear `v1.1.0`.
5. Publicar los artefactos de cada plataforma con `native:publish`.
6. Verificar los artefactos adjuntos y hacer pública la release.
7. Validar la actualización desde una instalación de la versión anterior.

```bash
php artisan native:publish

# O indicando el sistema operativo objetivo
php artisan native:publish mac
php artisan native:publish win
php artisan native:publish linux
```

Mientras la release permanezca en borrador, repetir `native:publish` actualiza sus artefactos. Una release borrador no está disponible para los usuarios; debe publicarse después de verificarla.

## Documentación de releases

La guía completa sobre firma, build, publicación, actualizaciones, eventos del updater y migraciones está en [NATIVEPHP_BUILD_PUBLICACION_ACTUALIZACIONES.md](NATIVEPHP_BUILD_PUBLICACION_ACTUALIZACIONES.md).
