**File Name:** ``
**name**: Thermal alarm for PCR transfer

description:
Technical specification for playing an alert sound and presenting a warning when the thermal heating timer completes, so laboratory analysts can promptly move tubes to the PCR machine.

user sotry: 
As an [analis laboratorium]
I want [mendengar alarm peringatan dari sistem]
So that [saya bisa segera memindahkan tabung ke mesin PCR]

**Prompt:** "Create a feature that plays an audible alarm and displays a warning in the thermal validation UI when the heating period is complete, ensuring the laboratory analyst hears the alert and can move the sample tube immediately to the PCR machine."

**Context File:** - `routes/web.php`
- `app/Http/Controllers/ThermalController.php`
- `app/Http/Requests/StoreThermalLogRequest.php`
- `app/Models/ThermalLog.php`
- `app/Models/Sample.php`
- `resources/views/thermal/create.blade.php`

**Skills:** "lims-app - Best practices for developing LIMS applications with PHP, Blade, and Laravel"

**Task:** Generate code for the following user story: "As a [analis laboratorium] I want [sistem menampilkan timer hitung mundur] So that [saya tidak lupa mengangkat sampel dan durasi pemanasan tidak berlebih]"

**Input:** - `@parameter sample_id` (required, current sample context)
- `@parameter temperature_celsius` (required, validated thermal value)
- `@parameter timer_duration_seconds` (client-side countdown duration)
- `@parameter remaining_seconds` (client-side timer state)
- `@parameter alarm_enabled` (client-side toggle for audible warning)

**Output:** - `@return Blade view 'thermal.create'` with an audible alarm and visual completion warning
- `@return alarm playback` when the timer completes or enters the final warning threshold
- `@return visible notification` instructing the analyst to move the tube to the PCR machine
- `@return clear action controls` to acknowledge the alert and proceed with the workflow

**Rules:** - `validation`: server-side validation remains limited to thermal record fields; alarm behavior is purely client-side and must not bypass or alter existing backend form validation.
- `business logic`: trigger the alarm when the thermal countdown completes or when the final minute warning threshold is reached; ensure the alert is noticeable with sound and UI highlights; retain the current thermal validation flow and require analyst acknowledgement before proceeding to the next step.

**What Changed:** - Added a specification for audible alarm behavior in the thermal validation UI.
- Defined the required warning threshold, alarm playback, and PCR transfer notification.
- Kept the existing backend thermal validation and storage workflow unchanged while enhancing the analyst alert experience.

**Commit Message:** "feat: add audible thermal completion alarm for PCR transfer readiness"