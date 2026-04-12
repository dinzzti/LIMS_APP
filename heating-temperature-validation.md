---
name: RNA Heating Temperature Validation
description: "Implement heating temperature validation system for Laboratory Analyst to ensure RNA quality preservation."
---

# User Story: RNA Heating Temperature Validation

**Sebagai** Analis Laboratorium  
**Saya ingin** memasukkan dan divalidasi suhu pemanasan  
**Agar** RNA tidak rusak akibat suhu berlebih

---

## Prompt

Create a feature that allows laboratory analysts to input heating temperatures for RNA samples with automatic validation to ensure temperatures stay within safe parameters. The system should prevent sample processing if temperature values exceed safe thresholds and provide clear feedback on validation errors.

## Context File

- `app/Models/Sample.php` - Sample/Specimen model
- `app/Models/HeatingTemperature.php` - Heating temperature tracking model
- `app/Services/TemperatureValidationService.php` - Temperature validation logic
- `app/Http/Requests/StoreHeatingTemperatureRequest.php` - Form request for validation
- `app/Http/Controllers/HeatingTemperatureController.php` - Controller handling temperature input
- `resources/views/samples/heating-temperature.blade.php` - UI form for temperature input
- `database/migrations/create_heating_temperatures_table.php` - Database schema

## Skills

- **lims-app** - Best practices for developing LIMS applications with PHP, Blade, and Laravel

---

## Task

Generate code for the following user story:

**As a** Analis Laboratorium  
**I want** memasukkan dan divalidasi suhu pemanasan  
**So that** RNA tidak rusak akibat suhu berlebih

---

## Input

- Sample ID or Specimen reference
- Heating temperature value (in Celsius)
- Test type/protocol being performed
- Timestamp of temperature entry

---

## Output

- Validation status (approved/rejected)
- Error messages if temperature is outside safe range
- Success notification with stored temperature record
- Log entry for audit trail
- Display of safe temperature range guidelines

---

## Rules

1. **Temperature Range Validation:**
   - Minimum safe temperature: 37°C
   - Maximum safe temperature: 95°C
   - RNA degradation threshold: > 100°C

2. **Validation Requirements:**
   - Temperature must be numeric and within defined range
   - Temperature input is required
   - Only allow whole number or decimal (max 2 decimal places)
   - Prevent sample processing if temperature validation fails

3. **Business Logic:**
   - Each heating session must be recorded with timestamp
   - Multiple heating temperatures can be logged per sample
   - Display warning if temperature approaches upper limit (> 90°C)
   - Archive heating temperature history for audit purposes

4. **Security & Data Integrity:**
   - Only authenticated laboratory analysts can input temperatures
   - Use authorization checks to verify user role
   - Implement soft deletes for historical tracking
   - Log all temperature entries with user information

5. **User Experience:**
   - Provide real-time validation feedback
   - Show acceptable temperature range in UI
   - Display visual indicators (green/yellow/red) for temperature status
   - Enable temperature input correction before final submission

---

## What Changed

**New Models:**

- `HeatingTemperature` model with relationships to `Sample` and `User`

**New Services:**

- `TemperatureValidationService` with `validateTemperature()` method

**New Controllers:**

- `HeatingTemperatureController` with store/update actions

**New Form Requests:**

- `StoreHeatingTemperatureRequest` with custom validation rules

**New Migrations:**

- `create_heating_temperatures_table` with sample_id, temperature, unit, analyst_id, timestamp

**New Blade Components:**

- Temperature input form component
- Temperature status badge component
- Temperature history table component

**New Views:**

- `samples/heating-temperature.blade.php` - Temperature input interface

---

## Commit Message

```
feat: add RNA heating temperature validation system

- Create HeatingTemperature model with sample relationship
- Implement TemperatureValidationService with range validation (37-95°C)
- Add HeatingTemperatureController for temperature data management
- Create form request validation with custom temperature rules
- Add Blade components for temperature input and status display
- Implement temperature history tracking and audit logging
- Add soft deletes for historical record preservation
- Include role-based authorization for analyst access
- Add real-time temperature validation feedback in UI
- Document safe temperature ranges for RNA processing
```
