<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringPasteurisasiUpdateRequest extends FormRequest
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
            'endapan',
            'adjustment_qty_air',
            'adjustment_qty_garam',
            'adjustment_qty_gula'
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
            'brix' => 'required|numeric|min:0|max:100',
            'nacl' => 'required|numeric|min:0|max:100',
            'bj' => 'required|numeric',
            'visco' => 'required|numeric',
            'aw' => 'required|numeric',
            'buih' => 'required|numeric',
            'ph' => 'required|numeric',
            'organo' => 'required|string',
            'endapan' => 'required|numeric',
            'aroma' => 'required|string',
            'endapan' => 'required|string',
            'status_disposition' => 'required',
            'disposition' => 'nullable|in:Release,Release Bersyarat,Adjustment,Resampling,Reject,Repro,Jalan Bareng,Leveling',
            'disposition_remarks' => 'nullable|string|max:255',
            'adjustment_qty_air' => 'nullable',
            'adjustment_qty_garam' => 'nullable',
            'adjustment_qty_gula' => 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'brix' => 'Brix',
            'nacl' => 'Nacl',
            'bj' => 'Bj',
            'visco' => 'Visco',
            'aw' => 'AW',
            'buih' => 'Buih',
            'ph' => 'pH',
            'organo' => 'Organo',
            'endapan' => 'Endapan',
            'aroma' => 'Aroma',
            'status_disposition' => 'Status',
        ];
    }
}
