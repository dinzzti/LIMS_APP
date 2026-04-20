---
name: lims-app
description: 'Get best practices for developing Laboratory Information Management System (LIMS) applications with Laravel, PHP, Blade, and MySQL.'
---

# Laravel LIMS Best Practices

Your goal is to help me write high-quality, maintainable Laboratory Information Management System (LIMS) applications using PHP, Laravel 11, Blade templating, and MySQL.

## Project Setup & Structure
- **Build Tool:** Use Composer for dependency management with a well-maintained `composer.json` and lock file. Keep package versions compatible with Laravel 11 and PHP 8.1+.
- **Framework:** Use Laravel as the primary web framework with its built-in routing, middleware, service container, and Eloquent ORM.
- **Project Layout:** Organize code by feature/domain rather than by layer. Group related controllers, requests, models, views, and services under folders like `app/Http/Controllers/Thermal`, `app/Models`, `resources/views/thermal`, and `app/Services`.
- **Domain Context:** Model lab concepts explicitly: samples, thermal logs, PCR runs, result approvals, and user roles. Keep feature boundaries clear for sample intake, thermal validation, anomaly detection, and PCR result entry.
- **Environment:** Use `.env` for environment-specific settings, and provide `.env.example` for team onboarding. Store database, queue, and mail credentials there.

## Dependency Injection & Components
- **Service Container:** Leverage Laravel's IoC container for dependency injection in controllers, jobs, listeners, and services.
- **Constructor Injection:** Inject service classes, repositories, and helpers via controller constructors whenever possible. Avoid using facades directly in business logic.
- **Service Providers:** Register shared services, custom validation rules, and macros in service providers such as `AppServiceProvider`, `RouteServiceProvider`, or dedicated feature providers.
- **Blade Components:** Use Blade components for reusable UI patterns like sample cards, status badges, table rows, and alerts. Prefer class-based components for reusable LIMS UI.
- **Single Responsibility:** Keep controllers thin and delegate business logic to service classes, requests, or model methods.

## Configuration
- **Externalized Configuration:** Use `config/*.php` for application-specific settings such as lab temperature ranges, queue timeouts, and sample status codes.
- **Environment Variables:** Keep sensitive values out of source control and load them from `.env`. Use `config()` wrappers to access values safely.
- **Typed Configuration:** Use typed config values where possible, and cast env values using helper functions like `env('APP_DEBUG', false)`.
- **Profiles:** Use `.env.testing`, `.env.local`, and `.env.production` to manage environment variations for local development, test suites, and deployed instances.
- **Feature Toggles:** For lab workflow changes, consider config flags or feature toggles so new queue or timer behaviors can be enabled without code changes.

## Web Layer (Controllers & Requests)
- **RESTful Routes:** Design clear, resource-based routes in `routes/web.php` and `routes/api.php`. Use route model binding for samples, thermal logs, and PCR records.
- **Form Requests:** Use `FormRequest` classes for validation and authorization of incoming data. Examples include `StoreThermalLogRequest`, `StorePcrResultRequest`, and `UpdateSampleStatusRequest`.
- **Validation Rules:** Encapsulate domain validation in form requests with custom messages for laboratory workflows, such as valid temperature ranges, required sample IDs, and allowed status transitions.
- **Error Handling:** Use Laravel's exception handler for consistent web responses and flash messages. Provide user-friendly feedback for validation failures in Blade views.
- **Blade Views:** Use Blade templates with layout inheritance (`@extends`, `@section`) and partials for shared UI sections. Keep sample processing screens clean and accessible.
- **CSRF Protection:** Use `@csrf` in all web forms and enable CSRF middleware for POST, PUT, PATCH, and DELETE requests.

## Service Layer
- **Business Logic:** Encapsulate LIMS-specific processes in service classes such as `SampleProcessingService`, `ThermalValidationService`, or `PcrWorkflowService`.
- **Stateless Services:** Keep services stateless and focused on a single domain concept. Avoid storing request or session state inside service objects.
- **Transactions:** Use database transactions for multi-step operations like marking a sample complete and creating a thermal log, to prevent partial updates.
- **Domain Rules:** Keep core rules inside services or model methods, e.g. thermal duration thresholds, sample status transitions, and anomaly detection criteria.

## Data Layer (Eloquent & Repositories)
- **Eloquent Models:** Define models with clear relationships such as `Sample` ➜ `hasMany(ThermalLog)`, `ThermalLog` ➜ `belongsTo(Sample)`, and `PcrLog` ➜ `belongsTo(Sample)`.
- **Attributes & Casting:** Use `$fillable`, `$casts`, and accessors/mutators to manage sample attributes and temperature values cleanly.
- **Eager Loading:** Prevent N+1 queries with `with()` when loading related models in sample histories, thermal logs, and PCR result pages.
- **Repository Pattern:** Use repositories only for complex query or reporting logic, such as retrieving queue items, pending thermal logs, or patient summary reports.
- **Database Migrations:** Use Laravel migrations to version schema changes for samples, thermal_logs, pcr_logs, and users. Add indexes for frequently queried columns like `sample_code`, `status`, and `created_at`.
- **Soft Deletes:** Consider soft deletes for audit-worthy records if historical sample retention is required.

## Logging
- **Laravel Logging:** Use Laravel's logging configured in `config/logging.php` and choose channels appropriately for application, security, and audit logs.
- **Contextual Logging:** Log important LIMS events such as sample receipt, thermal validation completion, abnormal temperatures, and PCR approval with context data.
  ```php
  Log::info('Thermal log saved', ['sample_id' => $sample->id, 'temperature' => $log->temperature_celsius]);
  ```
- **Error Logging:** Capture unexpected exceptions and validation issues in the log for debugging lab workflow problems.
- **Audit Trail:** Use application events or dedicated audit tables to capture critical actions like status transitions and result approvals.

## Testing
- **PHPUnit/Pest:** Use PHPUnit or Pest for writing feature and unit tests in Laravel.
- **Feature Tests:** Write feature tests for web routes, sample workflow screens, thermal log submission, and queue behavior using Laravel's HTTP testing utilities.
- **Unit Tests:** Test service classes, form requests, and custom validation rules in isolation.
- **Database Testing:** Use `RefreshDatabase` or `DatabaseTransactions` to ensure reliable database state in tests.
- **Mocking:** Use Mockery or PHPUnit mocks for external services and long-running processes, while keeping core business logic testable.
- **Test Data:** Use factories for sample, thermal log, and pcr log fixtures. Seed test data for common workflows like sample intake and thermal processing.

## Asynchronous/Performance
- **Queues:** Use Laravel queues (`jobs`, `listeners`) for background tasks such as report generation, email notifications, and long-running PCR data imports.
- **Caching:** Cache frequently accessed lab settings, such as temperature ranges or sample type lookup values, using Laravel's cache system.
- **Pagination:** Use pagination for sample lists, thermal queues, and log histories to avoid rendering large result sets.
- **Optimization:** Use eager loading and query optimization for lab data retrieval. Add database indexes on sample identifiers and status columns.
- **Job Dispatching:** Dispatch non-blocking jobs for notifications or data sync tasks, and keep user-facing requests fast.
