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

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

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
