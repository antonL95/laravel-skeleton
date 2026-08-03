---
description: Inertia.js, React, Tailwind CSS, and Wayfinder frontend development
paths:
  - "resources/js/**/*.{ts,tsx}"
  - "**/*.css"
---

# Frontend Development

## Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages`. Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` skill when working with Inertia client-side patterns.

### Inertia v3 features

- The project runs Inertia **v3** (`@inertiajs/react ^3`, `inertiajs/inertia-laravel ^v3`) wired through the `@inertiajs/vite` plugin (page resolution and the React root are injected by the plugin, so `resources/js/app.tsx` only calls `createInertiaApp({...})` — there is no manual `resolve`/`setup` and no `ssr.tsx`).
- Use all Inertia features through v3: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, prefetching, `useHttp`, `setLayoutProps`.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

## Inertia + React

- Use `router.visit()` or `<Link>` for navigation instead of traditional links.
- Forms: both the Inertia v3 `<Form>` component (see `pages/auth/login.tsx`) and the `useForm` helper are used in this codebase. For new programmatic flows the `useHttp` hook is available (used by the passkey settings section).

## Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- ALWAYS use `search-docs` tool for version-specific Tailwind CSS documentation. Never rely on training data.
- Activate `tailwindcss-development` skill when working with Tailwind CSS or styling-related tasks.
- Formatting/linting is Oxc: `bun run format` (oxfmt, sorts Tailwind classes) and `bun run lint` (oxlint). Never reach for Prettier or ESLint.

## TypeScript types

DTOs passed to the frontend are auto-generated from PHP, never hand-written.

- Generated from PHP Data classes (`app/Data/`) into `resources/js/types/php-types.d.ts` by `spatie/laravel-typescript-transformer` (configured in `app/Providers/TypeScriptTransformerServiceProvider.php`).
- These are **global ambient types** (`declare namespace App`), available everywhere without an `import from` statement.
- Namespace: `App.Data.*` for data classes, `App.Enums.*` for enums.
- Import using the `import =` namespace alias syntax:
  ```tsx
  import PasskeyData = App.Data.PasskeyData;
  import FlashMessageType = App.Enums.FlashMessageType;
  ```
- Regenerated automatically by Vite when `app/Data/**/*.php` files change, or manually via `php artisan typescript:transform`.

### Data passed to frontend must use DTOs

- All data passed from controllers to Inertia pages must use Spatie LaravelData classes with the `#[TypeScript]` attribute. Never pass raw arrays or inline anonymous shapes to Inertia.
- The frontend must use the auto-generated `App.Data.*` types for page props. Never define local `type`/`interface` aliases that duplicate what a DTO already provides.
- If a DTO does not exist for the data you need to pass, create one in `app/Data/` with the `#[TypeScript]` attribute, then regenerate types.

## Confirmations and dialogs

- Never use native browser dialogs (`window.confirm`, `window.alert`, `window.prompt`) for destructive actions, confirmations, or any user-facing prompt. They block the JS thread, cannot be styled, and cannot be exercised by Pest browser tests (which freeze the extension session).
- Use the `Dialog` primitive from `@/components/ui/dialog` for prompts and confirmations (forms, info panels, destructive confirmations). Wire it to a `useState` boolean and split the confirm into two handlers: `onClick={() => setOpen(true)}` on the trigger button, the action handler on the confirm button.
- Always add stable `data-testid` attributes to the trigger and confirm buttons so browser tests can drive the flow. Reference: `resources/js/pages/settings/partials/passkeys-section.tsx`.

## Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. **This project relocates the generated output to `resources/js/wayfinder/`** (laravel/wayfinder `dev-next` + `scripts/patch-wayfinder.php`), so imports use `@/wayfinder/...`, not the upstream default `@/actions` / `@/routes`.

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Controller actions: `import { update } from '@/wayfinder/App/Http/Controllers/Settings/PasswordController';`
- Fortify/vendor controllers: `import AuthenticatedSessionController from '@/wayfinder/Laravel/Fortify/Http/Controllers/AuthenticatedSessionController';`
- Named routes: `import { dashboard, register } from '@/wayfinder/routes';` and grouped routes `import { store } from '@/wayfinder/routes/passkey';`
- Usage: `<Form {...store.form()}>`, `router.delete(destroy(uuid).url)`, `edit().url`.
- Parameter binding: detects route keys (`{passkey:uuid}`) — `destroy({ uuid })` or `destroy(uuid)`.
- Query merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL; `null` removes params.
- Run `php artisan wayfinder:generate` after route changes (the Vite plugin also regenerates on build/dev).

## Internationalization (i18n)

User-facing copy is translated, never hardcoded in components.

- Get the translator with `const t = useTranslation();` from `@/hooks/use-translation`, then call `t('dot.key')`.
- Named params: `t('settings.profile.greeting', { name })` replaces `:name` placeholders in the string.
- Keys live in `lang/en/messages.php`, exposed to React via the Inertia shared prop `translations.messages` (typed on `SharedData`).
- Add a key to `messages.php` before referencing it. `t()` returns the raw key as a fallback when a translation is missing, so a missing key is silent in the UI.

## CRUD pages

- For a new CRUD feature, create only an `index.tsx` page and, in a `partials/` folder for the feature, a single form component that handles both create and edit, shown inside a `Dialog`. See the project front-end guideline for the canonical example.

## Comments

- Do not write comments in TS or TSX files. No JSDoc prose, no `{/* Section */}` JSX labels, no code narration. Code should explain itself through naming and structure.
- `TODO`, `FIXME`, `HACK`, `XXX`, commented-out code, and `{/* Section */}` banner labels must not exist anywhere in the repo. Never add one, and delete any you touch. Track pending work in the issue tracker, not in the source.
- In a `.ts` or `.tsx` file the signature carries the type, so a JSDoc block has nothing to add. In a plain `.js` file with no TypeScript to carry them, a JSDoc `@type`, `@param`, or `@returns` annotation is allowed and is the only JSDoc that is.
- Do not write `/* */` comments in CSS files.
- A comment survives only when a tool acts on the code because of it. These are directives, not comments, and must stay: `@ts-expect-error`, `@ts-ignore`, `@ts-nocheck`, `// @ts-check`, `/// <reference ... />` triple-slash directives, the `/** @jsxImportSource ... */` pragma, bundler pragmas (`/* @vite-ignore */`, `/* webpackChunkName: "..." */`), formatter pragmas (`// prettier-ignore`, `// oxfmt-ignore`), `/*! */` preserved-license blocks, `/* stylelint-disable-next-line <rule> */` in CSS, and oxlint suppressions naming a specific rule.

## File & component naming

- Filenames are **kebab-case** for pages, components, and hooks (e.g. `passkeys-section.tsx`, `use-translation.tsx`).
- Pages and components use `export default function PascalName(...)`. Hooks use a named `export function useThing()`.
- Use `.tsx` only when the file emits JSX or exports React types; otherwise `.ts`.
