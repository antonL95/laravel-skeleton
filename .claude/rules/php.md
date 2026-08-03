---
description: PHP coding standards and conventions
paths:
  - "**/*.php"
---

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
  ```php
  public function __construct(public GitHub $github) { }
  ```
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    // ...
}
```

## Enums

- Keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Do not write comments in code. No inline `//` comments, no prose sentences in docblocks. Code should explain itself through naming and structure.
- The only docblock content allowed is machine-read annotation carrying information the native type system cannot express: `@param`, `@return`, `@var`, `@throws`, `@template`, generics, `array{...}` shapes, `@property`, `@method`, `@mixin`, `@deprecated`, and `/** @use ... */` on traits.
- Never write summary or description lines in a docblock. When a method carries a docblock for a type annotation, the annotation is the entire content.
- Delete any docblock that only repeats the native signature (for example `/** @param string $name */` when the parameter is already typed `string $name`).
- `TODO`, `FIXME`, `HACK`, `XXX`, commented-out code, and section-banner comments must not exist anywhere in the repo. Never add one, and delete any you touch, including in published config stubs. Track pending work in the issue tracker, not in the source.
- Do not write `{{-- --}}` comments in Blade templates. No section labels, no narration, no commented-out markup.
- A comment survives only when a tool acts on the code because of it. These are directives, not comments, and must stay: `@phpstan-*`, `@psalm-*`, `@codeCoverageIgnore`, and a `#!` shebang on an executable script.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.
- If the same `array{…}` shape is repeated three or more times in a
  file (webhook handlers, integration payloads, batch jobs),
  promote it to a Spatie Data class under `app/Data/Integrations/` (or
  the nearest matching domain namespace). Build the typed object once
  via `Data::from($payload)` and pass it to the handlers instead of
  repeating the PHPDoc shape. `App\Data\Integrations\RevenueCat\WebhookEvent`
  is the reference implementation.

## Laravel Pint

- Run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

## Exceptions

- Exceptions should be named in the imperative mood, e.g., `OrderAlreadyShippedException`.
- Custom domain exceptions should extend `Exception` and implement `App\Contracts\DomainException`.
- Custom exceptions should always include the `report()` method, `render()` method, and `context()` method.
- Exceptions should always have a `public` constructor with a very descriptive message.

### Reporting behavior

The `report()` method controls whether the exception is reported to Nightwatch as a real error or just logged informally. Choose the appropriate behavior based on the exception type:

- **System errors** (infrastructure failures, DB errors, external service failures): `report()` should return `true` to let Nightwatch handle reporting. Examples: PDF generation failures, DB transaction failures, storage failures.
- **Expected application states** (user input errors, missing data, intermittent AI failures): `report()` should call `Log::info($this->getMessage(), $this->context())` and return `false`. These are not system errors and should not clutter Nightwatch. Examples: bad CSV upload, missing budget period, AI classification failure.

Note: `render()` is only called during HTTP request handling. In queues and console commands, only `report()` and `context()` are used.

### System error example

  ```php
  final class InvoicePdfGenerationFailedException extends \Exception implements \App\Contracts\DomainException
  {
      public function __construct(
          private readonly Invoice $invoice,
          ?\Throwable $previous = null,
      ) {
          parent::__construct(
              sprintf('Failed to generate PDF for invoice #%s', $this->invoice->number),
              0,
              $previous,
          );
      }

      public function report(): bool
      {
          return true;
      }

      public function render(): JsonResponse|RedirectResponse
      {
          if (request()->expectsJson()) {
              return response()->json(['message' => $this->getMessage()], 422);
          }

          return back()->with('error', __('messages.exceptions.invoice_pdf_generation_failed'));
      }

      /**
       * @return array<string, mixed>
       */
      public function context(): array
      {
          return [
              'invoice_id' => $this->invoice->id,
              'user_id' => $this->invoice->user_id,
          ];
      }
  }
  ```

### Informational exception example

  ```php
  final class BankStatementParsingFailedException extends \Exception implements \App\Contracts\DomainException
  {
      public function __construct(
          private readonly string $filePath,
          private readonly string $format,
          ?\Throwable $previous = null,
      ) {
          parent::__construct(
              sprintf('Failed to parse %s bank statement: %s', $this->format, $this->filePath),
              0,
              $previous,
          );
      }

      public function report(): bool
      {
          Log::info($this->getMessage(), $this->context());

          return false;
      }

      public function render(): JsonResponse|RedirectResponse
      {
          if (request()->expectsJson()) {
              return response()->json(['message' => $this->getMessage()], 422);
          }

          return back()->with('error', __('messages.exceptions.bank_statement_parsing_failed'));
      }

      /**
       * @return array<string, mixed>
       */
      public function context(): array
      {
          return [
              'file_path' => $this->filePath,
              'format' => $this->format,
          ];
      }
  }
  ```

## Actions

- Actions are single-purpose classes that perform a single task.
- Actions should be placed in the `app/Actions` directory and domain subdirectories.
- Actions should be named in the imperative mood, e.g., `CreateUser`, `SendEmail`, `ProcessPayment`.
- Actions should implement custom exceptions for unhappy path scenarios and unexpected errors.
- Actions should be immutable and final.
- Actions can have a constructor for dependency injection if it is necessary.
- Actions should have a `handle()` method that performs the action and returns nothing or a value - with a docblock for potential thrown exceptions.
