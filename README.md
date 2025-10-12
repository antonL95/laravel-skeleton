# Laravel React Starter Kit

A modern, full-stack Laravel application with React frontend, featuring comprehensive admin panel, payment processing, social authentication, real-time features, and production-ready tooling.

## Overview

This is a production-ready Laravel application skeleton that combines:
- **Backend**: Laravel 12 with PHP 8.4
- **Frontend**: React 19 with TypeScript and Inertia.js
- **Admin Panel**: Filament 4 with comprehensive CRUD interfaces
- **Payments**: Laravel Cashier with Stripe integration
- **Social Authentication**: Laravel Socialite
- **Real-time**: Laravel Reverb for WebSocket communication
- **Monitoring**: Laravel Nightwatch for application monitoring
- **Styling**: Tailwind CSS 4 with modern component libraries
- **Testing**: Pest 4 with browser testing capabilities

## Requirements

### System Requirements
- **PHP**: ^8.4
- **Node.js**: Latest LTS version
- **Database**: PostgreSQL (default) or MySQL
- **Redis**: For caching and queues (optional)
- **Composer**: PHP dependency manager
- **npm**: Node.js package manager

### Optional Services
- **AWS S3** or **DigitalOcean Spaces**: For file storage
- **Stripe Account**: For payment processing
- **Google OAuth**: For social authentication
- **Sentry**: For error tracking
- **Laravel nightwatch**: For observability

## Setup & Installation

### 1. Clone and Install Dependencies

```bash
git clone https://github.com/antonL95/laravel-skeleton
cd laravel-skeleton
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup

```bash
# Create SQLite database (default for development)
touch database/database.sqlite

# Or configure PostgreSQL in .env:
# DB_CONNECTION=pgsql
# DB_DATABASE=your_database_name
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

php artisan migrate --seed
```

### 4. Build Frontend Assets

```bash
npm run build
# or for development
npm run dev
```

## Development Commands

### Available Scripts (Composer)

```bash
# Run all tests and linting
composer test

# Run individual test suites
composer test:unit
composer test:arch
composer test:types

# Code formatting and refactoring
composer lint
```

### Laravel Artisan Commands

```bash
# Transform TypeScript definitions
php artisan typescript:transform
```

## Environment Variables

### Stripe Integration
- `STRIPE_KEY`: Stripe publishable key
- `STRIPE_SECRET`: Stripe secret key
- `STRIPE_WEBHOOK_SECRET`: Webhook endpoint secret
- `CASHIER_CURRENCY`: Default currency (EUR, USD)
- `CASHIER_CURRENCY_LOCALE`: Currency locale

### Storage (AWS S3 / DigitalOcean)
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`: AWS credentials
- `AWS_DEFAULT_REGION`, `AWS_BUCKET`: AWS S3 configuration
- `DO_ACCESS_KEY_ID`, `DO_SECRET_ACCESS_KEY`: DigitalOcean credentials
- `DO_DEFAULT_REGION`, `DO_BUCKET`, `DO_ENDPOINT`: DO Spaces configuration

### Authentication
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`: Google OAuth
- `GOOGLE_REDIRECT_URI`: OAuth redirect URL

### Real-time Features (Reverb)
- `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`: Reverb configuration
- `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`: WebSocket server settings

### Monitoring (Nightwatch)
- `NIGHTWATCH_TOKEN`: Authentication token
- `NIGHTWATCH_PORT`: Monitoring port (default: 2408)
- `NIGHTWATCH_REQUEST_SAMPLE_RATE`: Sampling rate for requests

### Optional Services
- `SENTRY_LARAVEL_DSN`: Error tracking

## Testing

The project uses **Pest 4** for testing with comprehensive coverage:

### Test Suites
- **Unit Tests**: `tests/Unit/` - Isolated component testing
- **Feature Tests**: `tests/Feature/` - Full application flow testing
- **Browser Tests**: `tests/Browser/` - End-to-end browser testing
- **Architecture Tests**: Enforces coding standards and architectural rules

### Test Configuration
- Tests use SQLite in-memory database
- Browser tests available for UI testing
- Architecture tests ensure code quality

## Project Structure

```
├── app/
│   ├── Actions/           # Business logic actions
│   ├── Data/              # Data transfer objects (Spatie Laravel Data)
│   ├── Filament/          # Filament admin panel resources
│   │   ├── Admin/         # Admin panel components
│   │   └── Resources/     # CRUD resources
│   ├── Models/            # Eloquent models
│   └── ...
├── resources/
│   ├── js/
│   │   ├── Components/    # React components
│   │   ├── Pages/         # Inertia.js pages
│   │   ├── wayfinder/     # Type-safe routing
│   │   ├── app.tsx        # Main React entry point
│   │   └── ssr.tsx        # Server-side rendering entry
│   └── css/
│       └── app.css        # Tailwind CSS styles
├── tests/
│   ├── Unit/              # Unit tests
│   ├── Feature/           # Feature tests
│   └── Browser/           # Browser tests
├── docker-compose.yaml    # Docker production setup
├── Dockerfile            # Container configuration
└── ...
```

## Docker Support

Services included:
- **Web application** with PHP-FPM
- **Queue workers** for background jobs
- **Scheduler** for cron jobs
- **Nightwatch agent** for monitoring

## Key Features

### Admin Panel (Filament)
- Modern admin interface with CRUD operations
- User management and authentication
- Settings management with Spatie Laravel Settings
- Real-time notifications
- Advanced table filtering and sorting

### Frontend (React + Inertia)
- Server-side rendering (SSR) support
- Type-safe routing with Laravel Wayfinder
- Modern UI components (Radix UI, Headless UI)
- Real-time features with Laravel Echo
- Dark/light mode support

### Payment Processing
- Stripe integration via Laravel Cashier
- Subscription management
- Webhook handling
- Multi-currency support

### Development Tools
- **Laravel Pint**: Code formatting
- **PHPStan/Larastan**: Static analysis
- **Rector**: Code refactoring
- **ESLint + Prettier**: Frontend code quality
- **TypeScript**: Type safety

## Contributing

### Code Standards
- Follow PSR-12 for PHP code
- Use Prettier for JavaScript/TypeScript formatting
- Run `composer lint` before committing
- Ensure all tests pass with `composer test`

## License

This project is licensed under the MIT License. See the LICENSE file for details.
