# End-to-End Testing with Playwright

This directory contains end-to-end tests for the Nebulae Cymbals Factory web application using Playwright.

## Test Structure

- `auth.setup.js` - Authentication setup for tests that require login
- `auth.spec.js` - Tests for authentication flows (login, logout)
- `units.spec.js` - Tests for Units management
- `product-categories.spec.js` - Tests for Product Categories management
- `products.spec.js` - Tests for Products management
- `transactions.spec.js` - Tests for Transactions management
- `dashboard.spec.js` - Tests for Dashboard functionality
- `customers.spec.js` - Tests for Customer management
- `users.spec.js` - Tests for User management and role-based access
- `reports.spec.js` - Tests for Report generation and exports

## Running Tests

You can run the tests using the following npm scripts:

```bash
# Run all tests
npm run test:e2e

# Run tests with UI mode (interactive)
npm run test:e2e:ui

# Run tests in debug mode
npm run test:e2e:debug

# View test reports
npm run test:e2e:report
```

## Running Specific Tests

To run a specific test file:

```bash
npx playwright test tests/e2e/units.spec.js
```

To run tests with a specific browser:

```bash
npx playwright test --project=chromium
npx playwright test --project=firefox
npx playwright test --project=webkit
```

## Authentication

Tests that require authentication use the authentication state saved by `auth.setup.js`. 
Make sure to update the login credentials in this file to match your environment.

## Configuration

The Playwright configuration is in `playwright.config.js` in the root directory. 
You can modify settings like browsers, viewport size, and other options there.

## Generating Test Reports

After running tests, you can view the HTML report:

```bash
npm run test:e2e:report
```

## Test Environment Setup

### Prerequisites

1. Make sure your Laravel application is properly set up with database migrations and seeders
2. Ensure you have the following seeders in place (as per your project memory):
   - Permission seeders: ProductCategoryPermissionSeeder, UnitPermissionSeeder, ProductPermissionSeeder, CustomerPermissionSeeder, TransactionPermissionSeeder, TransactionItemPermissionSeeder
   - Data seeders: ProductCategorySeeder, UnitSeeder, CustomerSeeder
   - Role seeder: RoleTableSeeder with roles (Super Admin, Kepala Gudang, Admin Gudang)

### Preparing Test Data

1. Run migrations and seeders to ensure your database has test data:

```bash
php artisan migrate:fresh --seed
```

2. Update the login credentials in `auth.setup.js` to match a valid user in your database

### Installing Playwright Dependencies

If you haven't already installed Playwright browsers, run:

```bash
npx playwright install
```

## Troubleshooting

If tests fail due to UI changes:

1. Run tests in debug mode: `npm run test:e2e:debug`
2. Use the Playwright Inspector to identify the issue
3. Update selectors or test logic as needed

### Common Issues

- **Authentication failures**: Make sure the credentials in `auth.setup.js` match a valid user
- **Element not found**: UI may have changed, update the selectors in the test files
- **Timeouts**: Increase timeout values in `playwright.config.js` if your application is slow to respond
- **Database constraints**: Ensure your test database has the necessary data from seeders

For more information, see the [Playwright documentation](https://playwright.dev/docs/intro).
