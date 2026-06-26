# CoolWear Fashion Ecommerce

CoolWear is a Laravel 12 fashion ecommerce application with a customer storefront, shopping cart, checkout, customer account area, wishlist, shipping integration, and an admin dashboard for catalog and order operations.

## Stack

- PHP 8.2+
- Laravel 12
- Blade templates
- Vite
- Tailwind CSS 4
- MySQL or MariaDB
- Composer and npm

## Main Structure

```text
app/Http/Controllers     Request controllers for storefront, account, checkout, shipping, and admin flows
app/Models               Eloquent models for catalog, cart, orders, customers, and wishlist
app/Services             External integrations such as ViettelPost and VietQR
resources/views          Blade views for customer and admin screens
resources/css            Tailwind/Vite CSS entry
resources/js             Vite JavaScript entry
routes/web.php           Web route definitions
database/migrations      Database schema
agent/                   Agent rules and project context
```

## Architecture Direction

The project is moving toward modular MVC inside one Laravel application. Keep request handling in controllers, shared layout data in view composers, larger workflows in services, and rendering in Blade views.

This is not split into real microservices yet. Treat modules such as Catalog, Cart, Checkout, Orders, Customers, Shipping, and Admin as internal domains within the same Laravel app.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

Do not commit real `.env` values or service credentials.

## Development

```bash
php artisan serve
npm run dev
```

Composer also defines a combined development command:

```bash
composer dev
```

## Verification

```bash
php artisan test
npm run build
```

The Composer test script also clears Laravel config before running tests:

```bash
composer test
```

## Notes For Agents

Read `AGENTS.md` and `agent/project-context.md` before making implementation changes. The `.agents/skills` folder may contain reusable skills copied from other projects; use only the skills relevant to the active Laravel task.
