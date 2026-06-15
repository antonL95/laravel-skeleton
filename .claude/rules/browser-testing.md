---
description: Selector convention and structure for Pest 4 browser tests
paths:
  - "tests/Browser/**/*.php"
  - "resources/js/pages/**/*.{ts,tsx}"
  - "resources/js/components/**/*.{ts,tsx}"
---

# Browser Testing Selectors

Browser tests live in `tests/Browser/` and use Pest 4's browser plugin. The plugin's `@name` selector resolves to `[data-testid="name"], [data-test="name"]` (see `vendor/pestphp/pest-plugin-browser/src/Support/GuessLocator.php`). Standardize on `data-testid` throughout this project; do not introduce `data-test`.

Tests target UI via stable `data-testid` attributes rather than visible text or CSS classes so that copy changes, styling changes, and component restructures do not break tests.

## Authoring

- Create browser tests with `php artisan make:test --pest --browser {Domain}/{Name}Test` to land them in `tests/Browser/{Domain}/`.
- Acquire an authenticated user via the helpers in `tests/Helpers/BrowserHelpers.php`: `actAsSubscribed()`, `actAsTrialExpired()`, `actAsOnTrial()`. Do not duplicate the `User::factory()->subscribed()->create() + actingAs()` dance inline.
- Always end an interaction chain with `->assertNoJavaScriptErrors()` so console errors fail the test.
- Use `route('...', absolute: false)` for navigation, never hardcoded paths.
- External HTTP is already faked globally via `tests/Pest.php` (`Saloon::fake([...])`). Add new fakes there if a test needs an integration that is not already covered.

## Selector Convention

Add `data-testid` to elements that browser tests need to target. Use kebab-case: feature noun first, then field or action, then any unique identifier (uuid, slug).

| Element | Example value | Pest selector |
|---|---|---|
| Page root container | `dashboard` | `@dashboard` |
| Primary CTA button | `create-invoice` | `@create-invoice` |
| Dialog or sheet root | `bank-account-edit` | `@bank-account-edit` |
| Form Select / Combobox / DatePicker trigger | `invoice-client-trigger` | `@invoice-client-trigger` |
| Form option / list item | `invoice-client-option-{uuid}` | `@invoice-client-option-{uuid}` |
| Tab / sidebar nav item | `settings-security` | `@settings-security` |
| Toast / status banner | `invoice-sent` | `@invoice-sent` |

Do not encode UI state (open/closed, disabled) in the value. Existing app-specific data attributes like `data-onboarding` (used by the in-app onboarding tour) are kept for their original purpose; add `data-testid` alongside them when an element also needs to be reachable from tests.

## What not to do

- Do not target Radix's internal class names, generated IDs, or aria attributes; they change between library versions.
- Do not assert against visible text strings that are user-facing copy when a stable `data-testid` exists. Reserve `assertSee` for truly stable headings, status messages, or text written by the user (entity names, etc.).
- Do not write smoke-only tests for an authenticated page (visit + assertNoJavaScriptErrors without interaction). Workflow tests must actually exercise the page. Public marketing pages may be smoke-only.
- Do not create new `actAs*` variants in individual test files; extend the helpers in `BrowserHelpers.php` instead.
- Do not introduce or keep `window.confirm` / `window.alert` / `window.prompt` in any component a test needs to reach. Native browser dialogs freeze the Pest browser extension and cannot be interacted with. Use the `Dialog` primitive from `@/components/ui/dialog` instead (see the rule in `frontend.md`).

## Running

- All browser tests: `composer test:browser`.
- Single file: `php artisan test --compact tests/Browser/{Domain}/{Name}Test.php`.
- Single test by name: `php artisan test --compact --filter='{test name fragment}'`.
- The full pipeline (`composer test`) runs lint, types, arch, unit suite, then browser suite as the last step.
