<laravel-boost-guidelines>
=== .ai/bun rules ===

<bun-guidelines>
Bun is the JavaScript runtime and package manager for this project. Always use Bun. Never use npm, npx, yarn, or pnpm.

- Install dependencies with `bun install` (the lockfile is `bun.lock`; there is no `package-lock.json`).
- Add or remove packages with `bun add` / `bun add -d` / `bun remove`.
- Run package.json scripts with `bun run <script>` (e.g. `bun run dev`, `bun run build`, `bun run build:ssr`, `bun run types`, `bun run lint`, `bun run format`).
- Run one-off binaries with `bunx <tool>` instead of `npx <tool>`.

Linting and formatting use the Oxc toolchain, not ESLint or Prettier:
- `bun run lint` runs `oxlint`.
- `bun run format` runs `oxfmt` (and `bun run format:check` runs `oxfmt --check`).
</bun-guidelines>

=== .ai/controllers rules ===

<controllers-guidelines>
Controllers should only be either RESTful resource controllers (following Laravel's conventional actions and route names) or single-action invokable controllers when the behavior does not strictly fit RESTful resource operations.
</controllers-guidelines>

=== .ai/front-end rules ===

<front-end-guidelines>
Whenever you are creating a new page on the front-end and it has a crud functionality.
You should create only index.tsx page and in the partials folder for the feature you should create a form that is both for edit and create functionality.
The form should be shown as a dialog.
Avoid creating interfaces in the component. Use laravel-data for creating a type definition on the backend and use it in the component. using `php artisan typescript:transform` command.

    <code-example language="tsx" description="Example of a index page">
        import TableAction from '@/components/table-action';
        import { Button } from '@/components/ui/button';
        import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
        import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
        import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
        import AppLayout from '@/layouts/app-layout';
        import StockMarketForm from '@/pages/stock-market/partials/stock-market-form';
        import { destroy } from '@/wayfinder/routes/user-stock-market';
        import { Head, router } from '@inertiajs/react';
        import { TrendingUp } from 'lucide-react';
        import { useState } from 'react';
        import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;

        export default function Index({ columns, rows }: { columns: string[]; rows?: UserStockMarketData[] }) {
        const [open, setOpen] = useState(false);
        const [openEdit, setOpenEdit] = useState(false);
        const [selectedRow, setSelectedRow] = useState<UserStockMarketData | null>(null);

            const handleDelete = (row: UserStockMarketData) => {
            router.delete(destroy(row.id).url);
            };
            const handleEditClick = (row: UserStockMarketData) => {
            setSelectedRow(row);
            setOpenEdit(true);
            };

            return (
            <AppLayout>
                <Head title="Stock market portfolio" />

                <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                    {!rows || rows.length === 0 ? (
                    <Empty>
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <TrendingUp />
                            </EmptyMedia>
                            <EmptyTitle>No stock holdings yet</EmptyTitle>
                            <EmptyDescription>
                                Track your stock portfolio by adding ticker symbols (e.g., AAPL, TSLA) along with the number of shares you own. We'll
                                automatically fetch current prices and calculate your holdings.
                            </EmptyDescription>
                        </EmptyHeader>
                        <EmptyContent>
                            <Button onClick={() => setOpen(true)}>Add stock ticker</Button>
                        </EmptyContent>
                    </Empty>
                    ) : (
                    <>
                    <div className={`flex items-center space-x-2 self-end`}>
                        <Button onClick={() => setOpen(true)}>Add stock ticker</Button>
                    </div>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                {columns.map((column: string) => (
                                <TableHead key={column}>{column}</TableHead>
                                ))}
                                <TableHead className="relative w-0">
                                    <span className="sr-only">Actions</span>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {rows?.map((row: UserStockMarketData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.ticker}</TableCell>
                                <TableCell>{row.amount}</TableCell>
                                <TableCell>{row.balance}</TableCell>
                                <TableCell>
                                    <TableAction row={row} destroy={() => handleDelete(row)} handleEditClick={() => handleEditClick(row)} />
                                </TableCell>
                            </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </>
                )}
                </div>

                <Dialog open={open} onOpenChange={() => setOpen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add stock ticker</DialogTitle>
                    </DialogHeader>
                    <StockMarketForm closeModal={() => setOpen(false)} />
                </DialogContent>
                </Dialog>
                <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update stock ticker</DialogTitle>
                    </DialogHeader>
                    {selectedRow && <StockMarketForm stockMarket={selectedRow} closeModal={() => setOpenEdit(false)} />}
                </DialogContent>
                </Dialog>
            </AppLayout>
            );
            }

    </code-example>

    <code-example language="tsx" description="Example of a form component">
        import { Button } from '@/components/ui/button';
        import { FormMessage } from '@/components/ui/form';
        import { Input } from '@/components/ui/input';
        import { Label } from '@/components/ui/label';
        import { store, update } from '@/wayfinder/actions/App/Http/Controllers/App/UserStockMarketController';
        import { useForm } from '@inertiajs/react';
        import { Loader2 } from 'lucide-react';
        import { FormEventHandler } from 'react';
        import StockMarketData = App.Data.App.StockMarket.StockMarketData;
        import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;

        export default function StockMarketForm({ stockMarket, closeModal }: { stockMarket?: UserStockMarketData; closeModal: () => void }) {
        const { data, setData, errors, submit, processing, reset } = useForm<StockMarketData>({
            ticker: stockMarket?.ticker || '',
            amount: stockMarket?.amount || 0,
            });

            const handleSubmit: FormEventHandler = (e) => {
            e.preventDefault();

            if (stockMarket !== undefined) {
            submit(update(stockMarket.id), {
            onSuccess: () => {
            setTimeout(() => {
            reset();
            closeModal();
            }, 100);
            },
            only: ['rows', 'flash'],
            });
            } else {
            submit(store(), {
            onSuccess: () => {
            setTimeout(() => {
            reset();
            closeModal();
            }, 100);
            },
            only: ['rows', 'flash'],
            });
            }
            };

            return (
            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                <div className="grid gap-2">
                    <Label htmlFor={'ticker'}>Ticker</Label>
                    <Input
                        id={'ticker'}
                        name={'ticker'}
                        value={data.ticker}
                        onChange={(e) => {
                    setData('ticker', e.target.value);
                    }}
                    autoFocus={true}
                    />
                    {errors.ticker !== undefined && <FormMessage>{errors.ticker}</FormMessage>}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor={'amount'}>Amount</Label>
                    <Input
                        id={'amount'}
                        name={'amount'}
                        value={data.amount}
                        type={'number'}
                        step={'0.001'}
                        onChange={(e) => {
                    setData('amount', Number.parseFloat(e.target.value));
                    }}
                    />
                    {errors.amount !== undefined && <FormMessage>{errors.amount}</FormMessage>}
                </div>

                <div className="flex items-center gap-4">
                    <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                        {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {stockMarket !== undefined ? 'Update' : 'Create'}
                    </Button>
                </div>
            </form>
            );
            }
    </code-example>
</front-end-guidelines>

=== .ai/localization rules ===

<localization-guidelines>
Instead of hard coding strings in the front-end always use use-translation hook and localize them in the backend. use the dot notation to access the string. eg. `const t = useTranslation();` `t('auth.login');`
</localization-guidelines>

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- filament/filament (FILAMENT) - v5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/ai (AI) - v0
- laravel/cashier (CASHIER) - v16
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/nightwatch (NIGHTWATCH) - v1
- laravel/octane (OCTANE) - v2
- laravel/pint (PINT) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- laravel/socialite (SOCIALITE) - v5
- laravel/wayfinder (WAYFINDER) - v
- livewire/livewire (LIVEWIRE) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- @inertiajs/react (INERTIA_REACT) - v3
- @laravel/echo-react (ECHO_REACT) - v2
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- laravel-echo (ECHO) - v2

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `bun run build` or ask the user to run `bun run dev` or `composer run dev`.

=== octane/core rules ===

# Laravel Octane

This application uses Laravel Octane, a long-running PHP server. The application bootstraps once and handles many requests within the same process.

- Never store request-specific state in singletons or static properties, because it can leak across requests.
- Use `config('octane.server')` to detect the active driver (`swoole`, `roadrunner`, or `frankenphp`).
- Prefer scoped bindings (`$this->app->scoped()`) over singletons for per-request services.

When working on Octane-specific features (concurrency, shared tables, memory, driver configuration, testing), invoke `octane-development` for detailed rules.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== wayfinder/v rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
