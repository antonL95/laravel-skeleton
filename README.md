# Laravel React Starter Kit

A modern, full-stack Laravel application skeleton with a React/Inertia frontend, a Filament admin panel, passkey + two-factor authentication, payments, real-time features, an AI SDK, and a production-ready Docker setup.

## Overview

- **Backend**: Laravel 13 (PHP 8.5)
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

## Authentication

Authentication is provided by Laravel Fortify and managed from the Settings → Security page:

- Email/password login, registration, password reset, and email verification
- Two-factor authentication (TOTP) with recovery codes
- **Passkeys (WebAuthn)**: register platform authenticators (Face ID, Touch ID, Windows Hello, security keys), sign in passwordlessly from the login page, and manage passkeys from settings
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

Unit and feature tests run against in-memory SQLite. Run them with `bin/dev test`; see Local development below.

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

## Contributing

- Follow the conventions in `.claude/rules/`
- Format and lint with `bin/dev composer lint` (Pint, Rector, oxlint, oxfmt)
- Make sure `bin/dev composer test:types`, `bin/dev composer test:arch` and `bin/dev test` pass before committing

## Local development

The local stack runs in Docker on OrbStack. No PHP, Composer, Bun or Node runs on the Mac: every install, build and test runs inside a container. Secrets never touch the disk. `bin/dev` reads them from Bitwarden Secrets Manager and hands them only to `php`, `queue`. Every other container and every one off Composer or Bun run gets no secrets.

### One time setup

1. Install [OrbStack](https://orbstack.dev/) and the Bitwarden Secrets Manager CLI `bws` from the [Bitwarden SDK releases](https://github.com/bitwarden/sdk-sm/releases).
2. Store the machine account access token in the macOS Keychain, once per Mac (the command prompts for it): `security add-generic-password -a "$USER" -s bws-dev -w`.
3. Create `.dev.local` in the project root (it is gitignored) with the id of the Bitwarden project that holds the dev secrets: `echo 'BWS_PROJECT_ID=<project id>' > .dev.local`.
4. The Bitwarden project is shared by several applications, so this application's secrets carry the `LARAVEL_SKELETON_` prefix, which `bin/dev` removes before the stack starts: `LARAVEL_SKELETON_APP_KEY`.
5. Copy `.env.example` to `.env`. It holds only non secret configuration.
6. Start the stack and install the dependencies:
   ```bash
   bin/dev up
   bin/dev composer install
   bin/dev bun install
   bin/dev artisan migrate
   ```

The stack serves the application at https://laravel-skeleton.orb.local, Vite at https://vite.laravel-skeleton.orb.local and Reverb at https://reverb.laravel-skeleton.orb.local. OrbStack issues trusted certificates for these names on the Mac and inside the containers. From the Mac, the services are reachable by name: Mailpit at http://mailpit.laravel-skeleton.orb.local:8025.

### Daily commands

| Command | What it does |
|---------|--------------|
| `bin/dev up` | Builds the images and starts the stack with secrets from Bitwarden. The queue worker stays off |
| `bin/dev up --workers` | Also starts the queue worker. It can call outside services and paid APIs, so start it only when you need it |
| `bin/dev workers:stop` | Stops the queue worker |
| `bin/dev restart` | Recreates the running app containers (`php`, `queue`) with freshly fetched secrets |
| `bin/dev down` | Stops the stack and keeps the data volumes. Stacks never start on their own, so after a reboot run `bin/dev up` in the projects you work on |
| `bin/dev down --others` | Stops every other `bin/dev` stack on this machine, of any project, and leaves this one running |
| `bin/dev secrets:check` | Lists which of the expected secrets Bitwarden holds, by name only |
| `bin/dev logs [service]` | Follows the logs |
| `bin/dev compose ...` | Runs any `docker compose` command against this checkout's stack |
| `bin/dev artisan ...` | Runs Artisan in the `php` container |
| `bin/dev tinker` | Opens Tinker in the `php` container |
| `bin/dev shell` | Opens bash in the `php` container |
| `bin/dev test` | Runs `composer test:unit` in the `tools` container |
| `bin/dev composer ...` | Runs Composer in a one off `tools` container with a random throwaway `APP_KEY`, for example `bin/dev composer lint` |
| `bin/dev bun ...` | Runs Bun in a one off `bun` container, for example `bin/dev bun add <package>` |
| `bin/dev tools ...` | Runs a command in the `tools` container with a random throwaway `APP_KEY` |

Always go through `bin/dev`, including `bin/dev compose` instead of a plain `docker compose`. A plain `docker compose up` recreates the app containers without secrets, and only `bin/dev` checks that `.env` has not been changed to point Compose somewhere else.

To add a secret, create it in Bitwarden with the `LARAVEL_SKELETON_` prefix, add its name without the prefix to `x-app-secrets` in `compose.dev.yaml` and run `bin/dev restart`.

### Worktrees

Every git worktree gets its own stack named after its folder, served at `https://laravel-skeleton-<folder>.orb.local`. Inside a new worktree run `bin/dev worktree:init` (it copies `.env` with the worktree's URLs and `.dev.local`), then the install commands above. `bin/dev worktree:list` shows the stacks, `bin/dev worktree:remove <name> [--force]` deletes a worktree together with its containers and volumes (it keeps the branch and refuses uncommitted changes without `--force`), and `bin/dev worktree:prune` deletes stacks whose worktree was removed some other way.

### Files the containers cannot change

The project is mounted read and write, because the containers install dependencies, format code and generate files. Files that run on the Mac outside Docker are mounted read only on top of it: `.git`, `.claude`, `.gemini`, `.github`, `AGENTS.md`, `CLAUDE.md`, `GEMINI.md`, `.mcp.json`, `.idea`, `bin`, `docker`, `compose.dev.yaml`, `Dockerfile`, `docker-compose.yaml`. Package code running in a container can therefore not plant a git hook or change what `bin/dev` or Compose starts. A new file of that kind needs an entry in `x-project-volumes` in `compose.dev.yaml`, repeated in the services that list their volumes one by one.

Supply chain protection: `bunfig.toml` refuses npm versions younger than seven days, `trustedDependencies` lists the only packages allowed to run install scripts, and Composer's `policy` blocks packages with security advisories or a malware flag.

## License

This project is licensed under the MIT License.
