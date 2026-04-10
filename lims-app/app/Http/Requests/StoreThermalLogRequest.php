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
            'temperature_celsius' => 'required|numeric|max:95.0',
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
            'temperature_celsius.numeric' => 'Suhu pemanasan harus berupa angka.',
            'temperature_celsius.max' => 'Suhu pemanasan tidak boleh melebihi 95.0°C karena RNA akan rusak.',
        ];
    }
}