<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThermalLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sample_id' => 'required|integer|exists:samples,id',
            'temperature_celsius' => 'required|numeric|min:95.0|max:98.0|decimal:0,1',
            'duration_minutes'    => 'required|integer|min:5|max:30',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sample_id.required' => 'ID sampel wajib diisi.',
            'sample_id.integer' => 'ID sampel harus berupa angka.',
            'sample_id.exists' => 'ID sampel tidak ditemukan dalam database.',
            'temperature_celsius.required' => 'Suhu pemanasan wajib diisi.',
            'temperature_celsius.numeric' => 'Suhu pemanasan harus berupa angka standar, gunakan titik sebagai pemisah desimal',
            'temperature_celsius.max' => 'Suhu pemanasan tidak boleh melebihi 98.0°C karena RNA akan rusak.',
            'temperature_celsius.min' => 'Suhu pemanasan tidak boleh kurang dari 95.0°C untuk memastikan denaturasi yang efektif.',
            'temperature_celsius.decimal' => 'Format suhu maksimal hanya boleh memiliki 1 angka di belakang koma.',
            'duration_minutes.required' => 'Durasi pemanasan wajib diisi.',
            'duration_minutes.integer'  => 'Durasi harus berupa bilangan bulat (menit).',
            'duration_minutes.min'      => 'Durasi minimal 5 menit untuk inaktivasi efektif.',
            'duration_minutes.max'      => 'Durasi maksimal 30 menit untuk mencegah degradasi RNA.',
        ];
    }
}
