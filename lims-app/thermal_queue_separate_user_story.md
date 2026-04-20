**File Name:** `thermal_queue_separate_user_story.md`
**name**: Separate thermal queue display

description:
Technical specification for providing laboratory analysts with a dedicated thermal queue list so sample pickup errors are minimized by separating queue management from the thermal input workflow.

user sotry:
As a [analis laboratorium]
I want [melihat daftar antrean termal secara terpisah]
So that [tidak terjadi kesalahan pengambilan sampel]

**Prompt:** "Create a feature that allows laboratory analysts to view a separate thermal queue list for pending samples, with clear sample metadata and direct navigation to the thermal validation form. This queue should reduce pickup mistakes by keeping thermal processing samples distinct from other workflow steps."

**Context File:** - `routes/web.php`
- `app/Http/Controllers/ThermalController.php`
- `app/Models/Sample.php`
- `app/Models/ThermalLog.php`
- `app/Http/Requests/StoreThermalLogRequest.php`
- `resources/views/thermal/create.blade.php`
- `resources/views/thermal/queue.blade.php`

**Skills:** "lims-app - Best practices for developing LIMS applications with PHP, Blade, and Laravel"

**Task:** Generate code for the following user story: "As a [analis laboratorium] I want [sistem menampilkan timer hitung mundur] So that [saya tidak lupa mengangkat sampel dan durasi pemanasan tidak berlebih]"

**Input:** - `@parameter queue_status` (optional filter to show pending/in_progress/completed thermal queue items)
- `@parameter search_term` (optional sample search by `sample_code` or `patient_name`)
- `@parameter selected_sample_id` (optional, used when opening a sample from the queue)

**Output:** - `@return Blade view 'thermal.queue'` showing a dedicated thermal queue list with sample metadata, status badges, and links to `thermal.create`
- `@return filtered queue data` from sample/thermal models for pending thermal processing
- `@return consistent navigation` between the queue and thermal validation form

**Rules:** - `validation`: ensure queue filter values are allowed and `selected_sample_id` exists in the database before opening the thermal validation workflow; the queue display itself is read-only and does not persist status changes directly.
- `business logic`: derive the separate queue from samples pending thermal processing or without completed `ThermalLog` entries; keep the queue presentation distinct from the active thermal input form to avoid sample pickup confusion; allow direct entry into the thermal validation step from a selected queue item.

**What Changed:** - Added a dedicated thermal queue feature to the LIMS flow, including a new `thermal.queue` view and queue filtering logic.
- Preserved the existing thermal validation flow in `ThermalController::create()` and `StoreThermalLogRequest` while introducing a separate queue navigation path.
- Clarified sample selection and queue display behavior to prevent thermal sample pickup mistakes.

**Commit Message:** "feat: add separate thermal queue view to reduce sample pickup errors"