# Laravel React Starter Kit

A modern, full-stack Laravel application skeleton with a React/Inertia frontend, a Filament admin panel, passkey + two-factor authentication, payments, real-time features, an AI SDK, and a production-ready Docker setup.

## Overview

- **Backend**: Laravel 13 (PHP 8.4+)
- **Frontend**: React 19 + TypeScript 6 + Inertia.js 3 (via the `@inertiajs/vite` plugin), bundled with Vite 8
- **Admin Panel**: Filament 5
- **Authentication**: Laravel Fortify (login, registration, password reset, email verification, two-factor TOTP, and **passkeys / WebAuthn**) plus Laravel Socialite (Google OAuth)
- **Payments**: Laravel Cashier 16 with Stripe
- **Real-time**: Laravel Reverb (WebSockets) with Laravel Echo
- **AI**: `laravel/ai` (the official Laravel AI SDK)
- **Monitoring**: Laravel Nightwatch
- **Styling**: Tailwind CSS 4
- **Type-safe routing**: Laravel Wayfinder
- **Server runtime**: Laravel Octane (FrankenPHP) with Inertia SSR
- **Testing**: Pest 4 (unit, feature, and browser tests)

## Requirements

- **PHP**: 8.4+ (the Docker images use PHP 8.5)
- **Composer**: PHP dependency manager
- **Bun**: JavaScript runtime and package manager (this project uses Bun, not npm/yarn/pnpm; the lockfile is `bun.lock`)
- **Database**: PostgreSQL (default) or SQLite for local development
- **Redis**: caching and queues (recommended)

### Optional Services

- **AWS S3** or **DigitalOcean Spaces**: file storage
- **Stripe**: payment processing
- **Google OAuth**: social authentication
- **Laravel Nightwatch**: observability

## Setup & Installation

### 1. Clone and install dependencies

```bash
git clone https://github.com/antonL95/laravel-skeleton
cd laravel-skeleton
composer install
bun install
```

### 2. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database setup

```bash
# SQLite (default for local development)
touch database/database.sqlite

# Or configure PostgreSQL in .env:
# DB_CONNECTION=pgsql
# DB_DATABASE=your_database_name
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

php artisan migrate
```

### 4. Run the app

```bash
# Start the full local stack (server, queue, logs, Vite) in one command
composer run dev

# Or build assets directly
bun run build       # client build
bun run build:ssr   # client + SSR build
bun run dev         # Vite dev server
```

## Development Commands

### Composer scripts

```bash
# Run the full quality gate: lint (Pint, oxfmt, oxlint, tsc, Rector), types (PHPStan), arch, and unit/feature tests
composer test

# Individual suites
composer test:unit     # Pest unit + feature
composer test:arch     # architecture tests
composer test:types    # PHPStan / Larastan

# Auto-fix formatting and refactors (Pint, oxlint, oxfmt, Rector)
composer lint
```

### Bun scripts

```bash
bun run build         # production client build
bun run build:ssr     # client + SSR build
bun run dev           # Vite dev server
bun run typecheck     # tsc --noEmit
bun run lint          # oxlint --fix
bun run format        # oxfmt
```

### Artisan helpers

```bash
php artisan typescript:transform   # generate TS types from app/Data DTOs (App.Data.*)
php artisan wayfinder:generate     # generate type-safe route helpers (resources/js/wayfinder)
```

## Authentication

Authentication is provided by Laravel Fortify and managed from the Settings → Security page:

- Email/password login, registration, password reset, and email verification
- Two-factor authentication (TOTP) with recovery codes
- **Passkeys (WebAuthn)** — register platform authenticators (Face ID, Touch ID, Windows Hello, security keys), sign in passwordlessly from the login page, and manage passkeys from settings
- Google OAuth via Laravel Socialite

## Environment Variables

### Stripe (Cashier)
- `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`
- `CASHIER_CURRENCY`, `CASHIER_CURRENCY_LOCALE`

### Storage (AWS S3 / DigitalOcean Spaces)
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`
- `DO_ACCESS_KEY_ID`, `DO_SECRET_ACCESS_KEY`, `DO_DEFAULT_REGION`, `DO_BUCKET`, `DO_ENDPOINT`

### Authentication
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `PASSKEYS_USER_HANDLE_SECRET` (optional; defaults to the application key)

### Real-time (Reverb)
- `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
- `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`

### AI (laravel/ai)
- Provider keys as configured in `config/ai.php` (e.g. `OPENAI_API_KEY`)

### Monitoring (Nightwatch)
- `NIGHTWATCH_TOKEN`, `NIGHTWATCH_REQUEST_SAMPLE_RATE`

## Testing

The project uses **Pest 4**:

- **Unit tests**: `tests/Unit/`
- **Feature tests**: `tests/Feature/` (auth, settings, passkeys, dashboard, etc.)
- **Browser tests**: `tests/Browser/` (end-to-end via Playwright/Chromium)
- **Architecture tests**: `tests/Unit/ArchitecturalTest.php`

Tests run against SQLite (in-memory for feature/unit). Run everything with `composer test`.

## Project Structure

```
├── app/
│   ├── Actions/            # Single-purpose action classes (incl. Fortify actions)
│   ├── Data/               # Spatie LaravelData DTOs (#[TypeScript] -> App.Data.*)
│   ├── Filament/Admin/     # Filament admin panel resources
│   ├── Http/Controllers/   # Controllers (RESTful resource / single-action)
│   ├── Models/             # Eloquent models (User, Passkey, ...)
│   └── Providers/          # App, Fortify, Filament, TypeScriptTransformer providers
├── resources/
│   ├── js/
│   │   ├── components/      # React components (incl. shadcn/ui in components/ui)
│   │   ├── pages/           # Inertia pages
│   │   ├── layouts/         # Layouts (app, auth, settings)
│   │   ├── hooks/           # React hooks (use-translation, ...)
│   │   ├── wayfinder/       # Generated type-safe routes (@/wayfinder/*)
│   │   ├── types/           # Generated DTO/types
│   │   └── app.tsx          # Entry point (SSR handled by @inertiajs/vite)
│   └── css/app.css          # Tailwind CSS
├── .claude/rules/           # Path-scoped AI coding guidelines
├── tests/{Unit,Feature,Browser}/
├── docker-compose.yaml      # Production stack (Coolify/Traefik)
├── Dockerfile               # Multi-stage build (worker / web / ssr)
└── .github/workflows/       # CI (lint, build, types, arch, tests, browser shards)
```

## Docker

Multi-stage `Dockerfile` (PHP 8.5, FrankenPHP) builds three targets and `docker-compose.yaml` runs them:

- **app** (`web`): Octane + FrankenPHP behind Traefik
- **worker**: queue worker
- **scheduler**: `schedule:work`
- **ssr**: Inertia SSR server (`inertia:start-ssr --runtime=bun`, port 13714)
- **nightwatch-agent**: monitoring agent

## Key Features

- **Admin panel** (Filament 5): CRUD resources, settings (Spatie Laravel Settings), notifications
- **Frontend** (React + Inertia 3): SSR, type-safe routing (Wayfinder), shadcn/ui + Radix UI, dark/light mode, i18n via `useTranslation`
- **Payments** (Cashier): subscriptions, webhooks, multi-currency
- **Passkeys & 2FA** via Fortify

## Development Tools

- **Bun**: JavaScript runtime / package manager
- **Laravel Pint**: PHP formatting
- **Larastan (PHPStan)**: static analysis
- **Rector**: automated refactoring
- **oxlint + oxfmt**: TypeScript/React linting and formatting (Oxc toolchain)
- **TypeScript**: type safety

## Contributing

- Follow the conventions in `.claude/rules/`
- Format and lint with `composer lint` (Pint, Rector, oxlint, oxfmt)
- Ensure `composer test` passes before committing

## License

This project is licensed under the MIT License.
