<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringDailyTankStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'storage' => 'required|string',
            'sampling_point' => 'required|string',
            'jenis_analisa' => 'required|string',
            'jenis_sample' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'storage' => 'Storage',
            'sampling_point' => 'Sampling Point',
            'jenis_analisa' => 'Jenis Analisa',
            'jenis_sample' => 'Jenis Sample',
        ];
    }
}
