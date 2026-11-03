---
description: Test enforcement, Pest framework, and HTTP integration testing
paths:
  - "tests/**/*.php"
---

# Testing

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` skill when working with Pest or testing-related tasks.

## Test Model Creation

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, use `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Saloon Integration Testing

- Use `Saloon::fake([])` to mock HTTP requests.
- When testing a request, use `Saloon::fake([RequestToTest::class => MockResponse::make(...)])`. Always use associative arrays to map requests to responses.

## PostgreSQL Habits

Tests run on PostgreSQL, which rejects what SQLite used to let through.

- Never assert a literal id. Sequences are not rolled back with the transaction; assert on the created model's id.
- When asserting order, make the query order deterministic with a secondary `orderBy('id')`, or sort in the test.
- Pass booleans as `true`/`false`, never `1`/`0`, and integers as integers.
- A `date` column compared to a Carbon timestamp compares by date, so the boundary day is included.
- String length limits are enforced. A value longer than a `varchar(n)` column fails the insert.
- Use the query builder or `whereLike` instead of raw SQL with MySQL backticks or case sensitive `like`.
- `Embeddings::fake()` runs for every feature and browser test in `tests/Pest.php`. Override it in a test when the vectors matter.
