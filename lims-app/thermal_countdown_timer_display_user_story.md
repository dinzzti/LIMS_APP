File Name: `thermal_countdown_timer_display_user_story.md`
name: Thermal countdown timer display

description:
Technical specification for showing a countdown timer during thermal heating validation so laboratory analysts can keep heating duration under control.

user sotry:
As a [analis laboratorium]
I want [melihat timer hitung mundur]
So that [durasi pemanasan tidak berlebih]

Prompt: "Create a feature that displays a countdown timer on the thermal heating validation screen, with clear remaining time, warning behavior for the final minute, and integration with sample context so analysts do not overheat samples."

Context File: - `routes/web.php`
- `app/Http/Controllers/ThermalController.php`
- `app/Http/Requests/StoreThermalLogRequest.php`
- `app/Models/ThermalLog.php`
- `app/Models/Sample.php`
- `resources/views/thermal/create.blade.php`

Skills: "lims-app - Best practices for developing LIMS applications with PHP, Blade, and Laravel"

Task: Generate code for the following user story: "As a [analis laboratorium] I want [sistem menampilkan timer hitung mundur] So that [saya tidak lupa mengangkat sampel dan durasi pemanasan tidak berlebih]"

Input: - @parameter sample_id (required, current sample context)
- @parameter temperature_celsius (required, validated thermal input)
- @parameter timer_duration_seconds (client-side countdown duration)
- @parameter remaining_seconds (client-side timer state)
- @parameter timer_action (client-side start/pause/reset actions)

Output: - @return Blade view containing a countdown timer component and remaining time display
- @return warning UI when the remaining time enters the final minute
- @return confirmation mechanism for analysts to acknowledge timer expiry and reset if needed
- @return preserved thermal validation workflow with existing sample and temperature persistence

Rules: - validation: keep backend validation focused on `sample_id` existence and `temperature_celsius` range; timer state is maintained on the client and does not affect server-side form validation.

business logic: show an active countdown immediately when the thermal validation screen loads or when heating begins; highlight remaining time, trigger a warning state at 60 seconds or less, and keep the timer visible alongside the temperature entry form so the analyst can avoid overheating.

What Changed: - added a specification for a front-end countdown timer in `resources/views/thermal/create.blade.php` using Alpine.js state and controls.
- kept `ThermalController::create()` and `StoreThermalLogRequest` unchanged for backend persistence while enhancing the thermal validation UX.
- clarified that warning behavior is client-side only and the timer must not bypass existing validation rules.

Commit Message: "feat: add thermal countdown timer display to thermal validation screen"