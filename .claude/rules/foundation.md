---
description: Global project conventions, structure, and communication guidelines
---

# Foundation Rules

## Conventions

- Follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## JavaScript Runtime

- Bun is the JavaScript runtime and package manager. Always use Bun; never use npm, npx, yarn, or pnpm. The lockfile is `bun.lock` (there is no `package-lock.json`).
- Install with `bun install`, add/remove with `bun add` / `bun add -d` / `bun remove`, run scripts with `bun run <script>`, run one-off binaries with `bunx <tool>`.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, check that the stack is up with `bin/dev compose ps`: Vite runs in its `bun` container. Ask them.

## Documentation Files

- Only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Formatting and testing

- Run `composer lint` before finalizing changes to ensure your code matches the project's expected formatting. (includes Pint, Rector, oxlint, oxfmt)
- Run `composer test` to run all tests. (includes oxlint, oxfmt check, tsc typecheck, pint test, phpstan test, rector test, pest test suite)

## PhpStan

- Run `composer test:types` before finalizing changes to ensure your code matches the project's expected type-safety.

## Rector

- Run `composer refactor` before finalizing changes to ensure your code matches the project's expected refactoring.

### Database

Tests run on PostgreSQL 18, the same engine as production. Local runs use the `laravel_test` database in the stack's `pgsql` container; the container's init script creates it with the four parallel worker databases, and `bin/dev composer db:test` recreates them on a stack whose volume already existed. CI runs a `pgvector/pgvector:pg18` service. `Tests\Support\TestDatabaseGuard` refuses any database whose name does not end in `_test` or `_test_test_<worker>`.

`RefreshDatabase` loads `database/schema/pgsql-schema.sql` once per worker. After adding a migration, run it against the test database and regenerate the dump: `bin/dev tools env APP_ENV=testing php artisan migrate --force`, then `bin/dev tools env APP_ENV=testing php artisan schema:dump`. A dump holds no rows, so a test that needs data seeds it itself; rows inserted by a data migration do not reach the tests.

Every script that touches the test database goes through `scripts/with-test-lock.sh`, which allows one run at a time per stack.

