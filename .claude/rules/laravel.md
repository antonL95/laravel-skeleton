---
description: Laravel framework conventions and patterns
paths:
  - "**/*.php"
---

# Laravel

CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (migrations, controllers, models, etc.). List available commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. Also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Leverage Laravel's ORM capabilities.
- Prevent N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.
- When modifying a column, the migration must include all attributes that were previously defined on the column. Otherwise, they will be dropped.
- Migrations must never implement a `down()` method. If one is present, delete it. Migrations are forward-only.
- Laravel 12 allows limiting eagerly loaded records natively: `$query->latest()->limit(10);`.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need other things, using `list-artisan-commands` to check available options for `php artisan make:model`.
- Casts should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

### UUID Convention

- Every new model that is exposed to the frontend or appears in URLs must use the `HasUuid` trait (`app/Concerns/HasUuid.php`).
- Migrations for such models must include a `uuid` column: `$table->uuid()->unique()->after('id');`
- The auto-increment `id` remains the primary key. The `uuid` is a secondary unique column used for external identification (URLs, Inertia props, frontend state).
- Foreign keys continue to reference integer `id` columns. Never use `uuid` as a foreign key.
- Internal-only tables (pivots, jobs, caches) do not need UUIDs.
- Never send numerical `id` values to the frontend. Always use `uuid` for identifying records in Inertia props, API responses, URLs, and frontend state.

### APIs & DTOs (no Eloquent API Resources)

- This project does **not** use Eloquent API Resources. There is no `app/Http/Resources` directory and no `JsonResource` subclasses; do not introduce them.
- Data passed to Inertia pages must use Spatie Data DTOs with the `#[TypeScript]` attribute. See `frontend.md` → "Data passed to frontend must use DTOs".

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers.
- Controllers always need to be lean and extract logic into action classes.
- Controllers should stick with the crud pattern -> `index`, `show`, `store`, `update`, `destroy`, `create`, `edit`.
- If actions are not CRUD-like, use single-action controller with `__invoke` method.

### Form requests: `prepareForValidation()` for defaults

If a field has a default value, set it in `prepareForValidation()` via
`$this->merge([...])` and make the rule `required` / `required|boolean`,
rather than letting the controller read `$request->input('field', default)`.
This keeps the contract visible in the FormRequest, lets the validator
reject bad input, and means controllers only ever read via
`$request->validated(...)`. `StoreReceiptRequest` is the reference
implementation.

### Model parameter ⇒ `$this->authorize(...)`

Every public controller method under `App\Http\Controllers\App\**` that
receives an `App\Models\*` parameter must call `$this->authorize(...)`
somewhere in its body. The architectural test in
`tests/Unit/ArchitecturalTest.php` (`controllers taking a model
parameter call authorize()`) enforces this and will fail CI otherwise.
Models without a registered `Policy` are allow-listed in the test with
a TODO — add a policy before dropping the entry.

## Actions vs. Services vs. Repositories

Pick by responsibility, not by preference. Naming drift between these three
is a recurring source of confusion for new contributors.

- **Action** (`app/Actions/<Domain>/<Verb><Subject>.php`). One user intent,
  one public `handle()`. Stateless except for injected dependencies. May
  orchestrate several services. Example: `CreateClient`, `DismissInsight`,
  `CanonicalizeMerchants`. Names are imperative (`Create…`, `Update…`,
  `Cancel…`). Actions are the layer controllers and jobs call into.
- **Service** (`app/Services/<Domain>/<Noun>Service.php`). Stateless,
  reusable across multiple use cases. Usually nouns + `Service` suffix.
  May wrap a third-party API, encapsulate shared domain logic, or batch
  computations. Example: `MerchantMatchingService`, `VatTrackingService`.
  Services get injected into actions, jobs, and other services — not into
  controllers directly (keep controllers lean; let an action compose
  services).
- **Repository** (`app/Repositories/<Noun>Repository.php`). Encapsulates
  raw SQL, driver-specific queries (pgvector, pg_trgm, full-text search),
  or any data-access concern a service wants to keep isolated from its
  orchestration logic. Repositories expose domain-meaningful query
  methods, not bare SQL strings to the caller. Example:
  `MerchantSimilarityRepository`.

If you're about to add a sixth method to a service and the class is growing
multi-concern, split it: pure SQL goes to a repository, user-intent
orchestration goes to an action, and the service keeps cross-cutting
reusable helpers.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### `retry_after` must exceed the longest `#[Timeout]` on that connection

The redis queue connection has two flavours in `config/queue.php`:
- `redis` — default, `REDIS_QUEUE_RETRY_AFTER=1200`. Hosts every job
  with `#[Timeout] <= 900`. Worker runs with `--timeout=1000`.
- `redis-long-running` — `REDIS_LONG_RUNNING_QUEUE_RETRY_AFTER=3900`.
  Hosts jobs with `#[Timeout]` up to 3600 s
  (`SyncTransactionsFromBankAccounts`, `ProcessEuBankAccountsJob`).
  Worker runs with `--timeout=3600`.

If you add a job with `#[Timeout] > 900`, either raise the default
worker's `--timeout` and the matching `retry_after` (dangerous — short
jobs now wait that long between retries) or route the job to
`redis-long-running` via `$this->onConnection('redis-long-running')` in
its `__construct()`. Workers are defined in `docker-compose.yaml`
(`worker` + `worker-long-running`).

### `ShouldBeUnique` for idempotency-sensitive jobs

Jobs whose duplicate execution would cost real money, send duplicate
notifications, or write duplicate rows should implement `ShouldBeUnique`
with a sensible `uniqueId()` and a `$uniqueFor` greater than the job's
timeout. Existing examples: `SyncTransactionsFromBankAccounts` keyed on
`user_id`, `ProcessEuBankAccountsJob` on `user_bank_account_id`,
`GenerateAccountantExportJob` on `export_id`, `ProcessAiQueryJob` on
`query_id`, `ProcessTransactionsJob` on `sha256(sorted ids)`.

### Long-running jobs must implement `failed()`

Jobs with `#[Timeout] >= 300` that run unattended should implement
`public function failed(?Throwable $exception): void` and dispatch a
domain exception (see `ProcessTransactionsJob::failed()` +
`TransactionClassificationFailedException`). Without it, Nightwatch
never sees the exception context and `DomainException::report()` /
`context()` never run.

## Models: don't override `delete()` for non-destructive operations

If the user-facing "destroy" action is actually a soft-mark, cascade, or
status change, expose that as a dedicated method (`dismiss()`, `cancel()`,
`archive()`, …) and leave `Model::delete()` to do a real Eloquent delete.
`RecurringItem::dismiss()` is the canonical example — it sets
`status => Dismissed` and cascades to RecurringSuggestion insights,
while callers who genuinely need to remove the row still use `->delete()`.

Overriding `delete()` to do anything other than a real delete confuses
everyone who relies on `->delete()` meaning "gone from the DB".

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Laravel 12 Structure

- Middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, check the stack with `bin/dev compose ps`, or build the assets with `bin/dev bun run build`.

## Form requests

- Form requests always need to include `rules()` and `authorize()` methods.
- Form requests always use arrays for validation rules.
- If there is logic that depends on valid data before doing its own validation it should be in the `after()` method.
- Form requests must include a `messages()` method with the `#[\Override]` attribute and `@return array<string, string>` PHPDoc.
- Validation messages must be localized using `__()`.
- Messages should be clear, user-friendly, and tell the user what to fix. Do not just re-wrap Laravel's generic validation messages.
- Group related rules into a single meaningful message when it makes sense. For example, `required` + `string` + `in:...` for a currency field can share one message like "Please select a valid currency."
- Only write separate messages when the rules have genuinely different user-facing meanings (e.g. a `required` message vs a `max` message).
- Use translation keys from `messages.php` (e.g. `__('messages.validation.field_group.message_key')`) rather than inline English strings.
