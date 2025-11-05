<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringDailyTankKimiaUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'nacl',
            'bj',
            'visco',
            'aw',
            'buih',
            'ph',
        ];

        $preparedData = [];

        foreach ($numericFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $cleanedValue = str_replace(' ', '', $this->input($field));
                $preparedData[$field] = str_replace(',', '.', $cleanedValue);
            }
        }

        $this->merge($preparedData);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:monitoring_daily_tank,id',
            'brix' => 'required|string',
            'nacl' => 'required|string',
            'bj' => 'required|string',
            'visco' => 'required|string',
            'aw' => 'required|string',
            'ph' => 'nullable|string',
            'buih' => 'nullable|string',
            'organo' => 'required|string|max:255',
            'endapan' => 'nullable|string|max:255',
            'color' => 'required|exists:colors,id',
            'status_parameter' => 'required|in:OK,NOT OK',
            'status_disposisi' => 'required|in:RELEASE,RELEASE BERSYARAT,TIDAK STD',
            'tindakan_lanjutan' => 'nullable|in:Drain,Release Bersyarat',
            'alasan_disposisi' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID Monitoring',
            'brix' => 'Brix',
            'nacl' => 'Nacl',
            'bj' => 'Bj',
            'visco' => 'Visco',
            'aw' => 'AW',
            'buih' => 'Buih',
            'ph' => 'pH',
            'organo' => 'Organo',
            'endapan' => 'Endapan',
            'color' => 'Warna',
            'status_parameter' => 'Status Parameter',
            'status_disposisi' => 'Status Disposisi',
            'tindakan_lanjutan' => 'Tindakan Lanjutan',
            'alasan_disposisi' => 'Alasan Disposisi',
        ];
    }
}
