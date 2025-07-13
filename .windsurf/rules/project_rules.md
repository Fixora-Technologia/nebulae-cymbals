---
trigger: always_on
---

# Windsurf Rules for Cymbal Manufacturing Factory Web Application

## General Coding Standards

-   Follow PSR-12 coding standards for consistent and readable code.
-   Use 4 spaces for indentation (no tabs).
-   Use camelCase for variable and function names.
-   Use PascalCase for class names.
-   Write clean, maintainable code with clear and concise comments for complex logic.
-   Document public classes and methods with PHPDoc comments.

## Naming Conventions

-   **Controllers**: Singular, PascalCase (e.g., `UserController`).
-   **Routes**: Plural, kebab-case (e.g., `/users`).
-   **Models**: Singular, PascalCase (e.g., `User`, `Product`).
-   **Tables**: Plural, snake_case (e.g., `users`, `products`).
-   **Columns**: snake_case (e.g., `first_name`, `product_name`).

## Project Structure

-   Use the standard Laravel directory structure:
    -   `app/Http/Controllers` for controllers.
    -   `app/Models` for Eloquent models.
    -   `app/Providers` for service providers.
    -   `database/migrations` for database migrations.
    -   `resources/views` for Blade templates.
-   For larger applications, consider Domain-Driven Design (DDD) to organize code into domains (e.g., `app/Domains/Inventory`).

## Best Practices

-   Avoid placing logic in `routes/web.php`; use controllers or middleware instead.
-   Minimize usage of vanilla PHP in Blade templates; use Blade components or partials for reusability.
-   Use Eloquent ORM for database operations.
-   Implement authentication using Laravel's built-in Auth system or Fortify for advanced features.
-   Use dependency injection where possible to improve testability and modularity.
-   Use form requests for validating incoming HTTP requests.
-   Use API resources for transforming Eloquent models when exposing data via API.
-   Implement events and listeners for decoupled business logic.
-   Use queues for handling time-consuming tasks asynchronously (e.g., sending emails or processing large datasets).

## Database

-   Use MySQL as the database.
-   Define relationships in models using Eloquent (e.g., `hasMany`, `belongsTo`).
-   Use migrations for database schema changes.
-   Optimize database queries with indexes for frequently accessed columns.
-   Use eager loading for relationships to avoid N+1 query problems.

## Frontend

-   Bootstrap 5.3.3 and SCSS for styling components.
-   Ensure responsiveness using Bootstrap's grid system.
-   Use Vite for asset bundling and management.
-   Use Chart.js for creating interactive and responsive charts on the dashboard.

## User Management

-   Use Spatie Laravel Permission for role-based access control.
-   Define roles (e.g., `admin`, `manager`, `staff`) and permissions in the database.
-   Use middleware or gates to check permissions in controllers (e.g., `$request->user()->can('view-users')`).

## Features

-   **Inventory Management**: Define a `Stock` model with relationships to `Product`. Include fields like `quantity`, `minimum_stock_threshold`.
-   **Transactions**: Create a `Transaction` model to log incoming and outgoing goods. Each transaction should update stock levels automatically.
-   **Dashboard**: Use Chart.js to display monthly sales charts (by quantity and value) with selectors for month and year. Include sales comparison charts over the last 12 months.
-   **Activity Logs**: Use Spatie Activitylog to log important events like transactions, user actions, and system changes.
-   **Data Export**: Use Maatwebsite/Laravel-Excel for exporting data to Excel and Dompdf for generating PDF reports.

## Performance

-   Cache frequently accessed data (e.g., product categories, user roles) using Laravel's caching system.
-   Use eager loading for relationships to reduce database queries.
-   Optimize database queries with indexes on frequently filtered columns.
-   Minimize heavy computations on the server by offloading to queues or background jobs.

## Security

-   Validate and sanitize all user input to prevent SQL injection, XSS, and CSRF attacks.
-   Use HTTPS for secure communication.
-   Protect against common web vulnerabilities using Laravel's built-in security features (e.g., CSRF protection, input validation).
-   Ensure sensitive data (e.g., passwords) is hashed and stored securely.

## Documentation

-   Document code with PHPDoc comments for public classes, methods, and complex logic.
-   Write clear and concise comments to explain non-obvious code.
-   Maintain a README file with project setup instructions, key features, and contribution guidelines.

## Additional Best Practices

-   Consider using Laravel's event system for handling business logic that needs to be decoupled (e.g., triggering notifications on low stock).
-   Use Laravel's queue system for asynchronous tasks like sending emails or processing large datasets.
-   Implement API versioning if the application exposes APIs to external systems.
-   Use environment variables for configuration (e.g., database credentials, API keys).
-   Regularly review and refactor code to maintain cleanliness and performance.
