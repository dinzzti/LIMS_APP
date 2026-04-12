---
name: lims-app
description: "Get best practices for developing Laboratory Information Management System (LIMS) applications with PHP, Blade, and Laravel."
---

# LIMS Application Best Practices

Your goal is to help me write high-quality, maintainable Laboratory Information Management System (LIMS) applications using PHP, Blade templating, and Laravel framework.

## Project Setup & Structure

- **Framework:** Use Laravel as the primary web framework for robust application structure and conventions.
- **Package Manager:** Use Composer for dependency management with a well-maintained `composer.json`.
- **Templating Engine:** Use Blade templating engine for all views, leveraging its powerful directives and component system.
- **Package Structure:** Organize code by domain/feature (e.g., `app/Modules/Samples`, `app/Modules/Users`, `app/Modules/Reports`) rather than purely by layer.
- **Environment Configuration:** Use `.env` file for environment-specific settings with proper `.env.example` documentation.

## Dependency Injection & Components

- **Service Container:** Leverage Laravel's service container for dependency injection throughout the application.
- **Service Providers:** Use service providers to bootstrap and register application services.
- **Eloquent Models:** Use Eloquent ORM models with proper relationships for database interactions.
- **Immutability:** Use accessors and mutators for data transformation; keep model logic focused on domain concerns.

## Configuration

- **Externalized Configuration:** Use `config/` directory files for application configuration, loaded via environment variables.
- **Type-Safe Properties:** Use environment variables with type casting in configuration files for safety.
- **Profiles:** Use multiple `.env` files (`env.local`, `.env.production`) to manage environment-specific configurations.
- **Secrets Management:** Never hardcode secrets. Use environment variables or Laravel's secure key management practices.

## Web Layer (Controllers & Views)

- **RESTful APIs & Web Routes:** Design clear and consistent RESTful endpoints using Laravel's routing system.
- **Blade Components:** Create reusable Blade components for common UI patterns in LIMS applications (sample forms, result tables, etc.).
- **Data Transfer:** Use form requests (`FormRequest` classes) for request validation and data transfer.
- **Validation:** Use Laravel's validation with custom rules for domain-specific business logic (sample validation, test parameter constraints).
- **Error Handling:** Implement a global exception handler using Laravel's built-in exception handling for consistent error responses.

## Service Layer

- **Business Logic:** Encapsulate LIMS-specific business logic within service classes (e.g., `SampleProcessingService`, `ResultCalculationService`).
- **Statelessness:** Services should be stateless and focused on specific domains.
- **Transaction Management:** Use database transactions for critical operations involving multiple queries.

## Data Layer (Eloquent & Repositories)

- **Eloquent Models:** Define models with proper relationships (hasMany, belongsTo, belongsToMany) representing LIMS entities (samples, tests, results).
- **Null Safety:** Use nullable casting and proper null handling in model attributes.
- **Query Optimization:** Use eager loading with `with()` to prevent N+1 query problems.
- **Repository Pattern:** Consider using repositories for complex query logic, wrapping Eloquent models appropriately.

## Blade Templating

- **Template Organization:** Organize Blade files by feature/module in `resources/views/` directory structure.
- **Blade Components:** Use Blade component classes for dynamic, reusable UI elements.
- **Directives:** Leverage Blade directives (`@if`, `@foreach`, `@auth`, `@can`) for clean, readable templates.
- **Layout Inheritance:** Use layout files with `@extends` and `@section` for consistent page structure.
- **Data Display:** Use Blade's echo syntax (`{{ }}`) for displaying model data safely.

## Logging

- **Laravel Logging:** Use Laravel's logging system configured in `config/logging.php`.
- **Contextual Logging:** Log important LIMS events (sample received, test completed, results approved) with appropriate context.
  ```php
  Log::info('Sample processed', ['sample_id' => $sample->id, 'test_type' => $testType]);
  ```
- **Log Channels:** Use different log channels for different application concerns.

## Testing

- **PHPUnit:** Use PHPUnit as the primary testing framework (included with Laravel).
- **Feature Tests:** Write feature tests for API endpoints and web routes using Laravel's HTTP testing utilities.
- **Unit Tests:** Test individual service classes and business logic in isolation.
- **Database Testing:** Use Laravel's database testing traits (RefreshDatabase, DatabaseTransactions) for reliable test isolation.
- **Mocking:** Use Mockery for mocking external dependencies and complex interactions.

## Security & Compliance

- **Authentication:** Use Laravel's built-in authentication system or Passport for API authentication.
- **Authorization:** Use policies and gates for controlling access to LIMS resources and operations.
- **SQL Injection Prevention:** Always use parameterized queries through Eloquent or query builder.
- **CSRF Protection:** Enable CSRF protection for all POST/PUT/DELETE requests via Blade forms.
- **Data Privacy:** Implement proper access controls for sensitive laboratory data (patient information, test results).

## Performance Optimization

- **Caching:** Use Laravel's caching system for frequently accessed LIMS data (test parameters, lab configurations).
- **Queues:** Offload long-running operations (report generation, data exports) to queues using Laravel's job system.
- **Database Indexing:** Add appropriate indexes on frequently queried columns in LIMS-related tables.
