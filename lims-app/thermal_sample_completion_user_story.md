**File Name:** ``
**name**: Sample thermal completion transition

description:
Technical specification for allowing laboratory analysts to mark a thermal sample as complete so it can move into the PCR testing stage.

user sotry:
As a [analis laboratorium]
I want [mengubah status sampel menjadi selesai]
So that [sampel berpindah ke tahap uji PCR]

**Prompt:** "Create a feature that enables laboratory analysts to mark a thermal sample as finished, update the sample status in the LIMS, and route the sample into the PCR workflow with clear confirmation and audit trail support."

**Context File:** - `routes/web.php`
- `app/Http/Controllers/ThermalController.php`
- `app/Http/Controllers/SampleController.php`
- `app/Models/Sample.php`
- `app/Models/ThermalLog.php`
- `app/Http/Requests/StoreThermalLogRequest.php`
- `resources/views/thermal/create.blade.php`
- `resources/views/samples/show.blade.php`

**Skills:** "lims-app - Best practices for developing LIMS applications with PHP, Blade, and Laravel"

**Task:** Generate code for the following user story: "As a [analis laboratorium] I want [mengubah status sampel menjadi selesai] So that [sampel berpindah ke tahap uji PCR]"

**Input:** - `@parameter sample_id` (required, identifies the sample to transition)
- `@parameter status` (required, new sample phase value such as `thermal_complete` or `ready_for_pcr`)
- `@parameter completed_at` (optional, timestamp marking thermal completion)
- `@parameter analyst_id` (authenticated user context for audit trail)

**Output:** - `@return HTTP redirect` to a sample summary or PCR queue page after successful status update
- `@return Blade view` with confirmation and updated sample status badge
- `@return updated sample record` with new phase/status and optional completion timestamp
- `@return audit log entry` or flash message recording the status transition

**Rules:** - `validation`: ensure `sample_id` exists, the sample is currently eligible for completion, and the requested status is within allowed transitions such as `thermal_pending → thermal_complete → ready_for_pcr`.
- `business logic`: only allow the status transition after thermal validation is complete; update the sample workflow state to indicate readiness for PCR; record the analyst who completed the transition and prevent repeating completion on already finalized samples.

**What Changed:** - Added a sample status transition flow from thermal processing to PCR-ready in the LIMS domain.
- Defined controller actions and view expectations for updating sample status and confirming the PCR handoff.
- Preserved the existing thermal validation and timer UI while enabling explicit sample completion once thermal heating is validated.

**Commit Message:** "feat: add sample completion transition from thermal processing to PCR stage"**File Name:** `thermal_sample_completion_user_story.md`
**name**: Sample thermal completion status transition

description:
Technical specification for allowing laboratory analysts to mark a sample as finished with thermal processing so it can progress to PCR testing.

user sotry 2.5:
As a [analis laboratorium] 
I want [mengubah status sampel menjadi selesai] 
So that [sampel berpindah ke tahap uji PCR] 

**Prompt:** "Create a feature that enables laboratory analysts to mark a thermal sample as complete after heating validation, update the sample status in the LIMS, and route the sample into the PCR test stage with a clear confirmation and audit trail."

**Context File:** - `routes/web.php`
- `app/Http/Controllers/ThermalController.php`
- `app/Http/Controllers/SampleController.php` (or a new controller action for status transition)
- `app/Models/Sample.php`
- `app/Models/ThermalLog.php`
- `resources/views/thermal/create.blade.php`
- `resources/views/samples/show.blade.php` or `resources/views/thermal/complete.blade.php`

**Skills:** "lims-app - Best practices for developing LIMS applications with PHP, Blade, and Laravel"

**Task:** Generate code for the following user story: "As a [analis laboratorium] I want [sistem menampilkan timer hitung mundur] So that [saya tidak lupa mengangkat sampel dan durasi pemanasan tidak berlebih]"

**Input:** - `@parameter sample_id` (required, identifies the thermal sample record)
- `@parameter status` (required, target status value such as `thermal_complete` or `ready_for_pcr`)
- `@parameter completed_at` (optional timestamp for when the thermal step finished)
- `@parameter user_id` or authenticated analyst context (required for audit logging)

**Output:** - `@return HTTP redirect` to the sample summary or PCR workflow page after successful status update
- `@return Blade view` with confirmation message and updated sample status badge
- `@return sample record updated` with new status and optional completion timestamp
- `@return audit log entry` or session flash message indicating the thermal step is complete

**Rules:** - `validation`: ensure `sample_id` exists, the sample is currently in the correct thermal phase, and the requested status transition is allowed; validate `status` against an allowed set like `['thermal_pending', 'thermal_complete', 'ready_for_pcr']`.
- `business logic`: only allow completion when the sample has passed thermal validation; transition sample state from thermal processing to PCR-ready; record the analyst who performed the status change and prevent duplicate completion actions on already finalized samples.

**What Changed:** - Added a status transition flow from thermal processing to PCR-ready in the LIMS domain.
- Defined controller responsibilities for updating `Sample` status and returning a confirmation UI.
- Introduced a new or extended Blade view for sample completion confirmation and PCR handoff.
- Kept existing thermal validation and timer UI separate while enabling explicit sample completion once heating is done.

**Commit Message:** "feat: add sample completion transition from thermal processing to PCR stage"
