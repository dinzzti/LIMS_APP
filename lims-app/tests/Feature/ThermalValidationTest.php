<?php

namespace Tests\Feature;

use App\Models\Sample;
use App\Models\ThermalLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThermalValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Successfully store thermal log with valid temperature
     */
    public function test_store_thermal_log_with_valid_temperature(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 85.5,
        ]);

        $response->assertRedirect(route('thermal.create'));
        $response->assertSessionHas('success', 'Data suhu pemanasan berhasil disimpan.');
        $this->assertDatabaseHas('thermal_logs', [
            'sample_id' => $sample->id,
            'temperature_celsius' => 85.5,
        ]);
    }

    /**
     * Test: Successfully store thermal log at boundary (exactly 95.0°C)
     */
    public function test_store_thermal_log_at_boundary_temperature(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 95.0,
        ]);

        $response->assertRedirect(route('thermal.create'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('thermal_logs', [
            'sample_id' => $sample->id,
            'temperature_celsius' => 95.0,
        ]);
    }

    /**
     * Test: Reject thermal log with temperature exceeding 95.0°C
     */
    public function test_reject_thermal_log_with_temperature_over_limit(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 95.1,
        ]);

        $response->assertSessionHasErrors('temperature_celsius');
        $response->assertSessionHas('errors');
        
        $errors = session('errors');
        $this->assertStringContainsString('95.0°C', $errors->first('temperature_celsius'));
        $this->assertStringContainsString('RNA akan rusak', $errors->first('temperature_celsius'));
        
        $this->assertDatabaseMissing('thermal_logs', [
            'sample_id' => $sample->id,
            'temperature_celsius' => 95.1,
        ]);
    }

    /**
     * Test: Reject thermal log with temperature far exceeding limit
     */
    public function test_reject_thermal_log_with_temperature_far_over_limit(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 100.0,
        ]);

        $response->assertSessionHasErrors('temperature_celsius');
        $this->assertDatabaseMissing('thermal_logs', [
            'sample_id' => $sample->id,
            'temperature_celsius' => 100.0,
        ]);
    }

    /**
     * Test: Reject thermal log with empty temperature
     */
    public function test_reject_thermal_log_with_empty_temperature(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => '',
        ]);

        $response->assertSessionHasErrors('temperature_celsius');
        $errors = session('errors');
        $this->assertStringContainsString('wajib diisi', $errors->first('temperature_celsius'));
    }

    /**
     * Test: Reject thermal log with empty sample_id
     */
    public function test_reject_thermal_log_with_empty_sample_id(): void
    {
        $response = $this->post(route('thermal.store'), [
            'sample_id' => '',
            'temperature_celsius' => 85.0,
        ]);

        $response->assertSessionHasErrors('sample_id');
        $errors = session('errors');
        $this->assertStringContainsString('wajib diisi', $errors->first('sample_id'));
    }

    /**
     * Test: Reject thermal log with non-numeric temperature
     */
    public function test_reject_thermal_log_with_non_numeric_temperature(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->post(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 'abc',
        ]);

        $response->assertSessionHasErrors('temperature_celsius');
        $errors = session('errors');
        $this->assertStringContainsString('angka', $errors->first('temperature_celsius'));
    }

    /**
     * Test: Reject thermal log with invalid sample_id
     */
    public function test_reject_thermal_log_with_invalid_sample_id(): void
    {
        $response = $this->post(route('thermal.store'), [
            'sample_id' => 9999,
            'temperature_celsius' => 85.0,
        ]);

        $response->assertSessionHasErrors('sample_id');
        $errors = session('errors');
        $this->assertStringContainsString('tidak ditemukan', $errors->first('sample_id'));
    }

    /**
     * Test: Create form page loads successfully
     */
    public function test_thermal_create_page_loads_successfully(): void
    {
        $samples = Sample::factory(3)->create();

        $response = $this->get(route('thermal.create'));

        $response->assertStatus(200);
        $response->assertViewIs('thermal.create');
        $response->assertViewHas('samples');
    }

    /**
     * Test: API JSON response for successful thermal log creation
     */
    public function test_api_store_thermal_log_returns_json(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->postJson(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 85.5,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'sample_id',
                'temperature_celsius',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonPath('data.temperature_celsius', '85.50');
    }

    /**
     * Test: API JSON response for validation error
     */
    public function test_api_validation_error_returns_json(): void
    {
        $sample = Sample::factory()->create();

        $response = $this->postJson(route('thermal.store'), [
            'sample_id' => $sample->id,
            'temperature_celsius' => 95.1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('temperature_celsius');
    }
}
