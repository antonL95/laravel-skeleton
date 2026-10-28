# Laravel Skeleton

## Package Versions

- php - 8.5
- filament/filament - v5
- inertiajs/inertia-laravel - v3
- laravel/ai - v0
- laravel/cashier - v16
- laravel/fortify - v1
- laravel/framework - v13
- laravel/nightwatch - v1
- laravel/octane - v2
- laravel/pint - v1
- laravel/prompts - v0
- laravel/reverb - v1
- laravel/socialite - v5
- laravel/tinker - v3
- laravel/wayfinder - dev-next
- livewire/livewire - v4
- larastan/larastan - v3
- laravel/boost - v2
- laravel/mcp - v0
- laravel/pail - v1
- laravel/sail - v1
- spatie/laravel-data - v4
- spatie/laravel-typescript-transformer - v3
- pestphp/pest - v4
- phpunit/phpunit - v12
- rector/rector - v2
- @inertiajs/react - v3
- @inertiajs/vite - v3
- react - v19
- tailwindcss - v4
- vite - v8
- typescript - v7
- @laravel/vite-plugin-wayfinder - v0
- oxlint - v1
- oxfmt - v0
- laravel-echo - v2

## Skills

Activate domain-specific skills when working in that area:

- `pest-testing` - When writing/debugging tests
- `tailwindcss-development` - When styling components
- `wayfinder-development` - When referencing backend routes in frontend
- `inertia-react-development` - When building React/Inertia pages
- `fortify-development` - When working on authentication or passkeys
- `cashier-stripe-development` - When working on billing
- `ai-sdk-development` - When working with AI/LLM integrations

## Rules

Detailed guidelines are in `.claude/rules/` - they load automatically based on file context:

| Rule | Scope |
|------|-------|
| `foundation.md` | Global conventions |
| `boost.md` | Laravel Boost MCP tools |
| `dev-environment.md` | Docker stack, `bin/dev` and the local URLs |
| `php.md` | PHP files (`**/*.php`) |
| `laravel.md` | Laravel patterns (`**/*.php`) |
| `octane.md` | Octane runtime constraints (`**/*.php`) |
| `pint.md` | Pint formatting (`**/*.php`) |
| `testing.md` | Tests (`tests/**/*.php`) |
| `frontend.md` | Frontend (`resources/js/**/*.{ts,tsx}`, `**/*.css`) |
| `filament.md` | Filament (`app/Filament/**/*.php`) |
| `ai-sdk.md` | Laravel AI SDK (`**/*.php`) |
| `browser-testing.md` | Browser tests (`tests/Browser/**/*.php`, `resources/js/**/*.{ts,tsx}`) |

Comment restrictions live with the language they apply to: `php.md` for PHP, Blade, and PHPDoc; `frontend.md` for TS, TSX, and CSS.
