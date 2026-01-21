<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringStorageKimiaUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'nacl',
            'bj',
            'visco',
            'tn',
            'aw',
            'buih',
            'ph',
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
            'visco' => 'required|numeric',
            'nacl' => 'required|numeric|min:0|max:100',
            'bj' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'aw' => 'required|numeric',
            'tn' => 'nullable|numeric',
            'organo' => 'nullable|string',
            'buih' => 'nullable|numeric',
            'aroma' => 'required|string',
            'kristal' => 'nullable|string',
            'endapan' => 'nullable|string',
            'status_disposition' => 'required',
            'disposition' => 'nullable|in:Release,Release Bersyarat,Resampling,Reject,Repro,Jalan Bareng,Leveling',
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
            'visco' => 'Visco',
            'nacl' => 'Nacl',
            'bj' => 'Bj',
            'ph' => 'pH',
            'aw' => 'AW',
            'tn' => '%TN',
            'buih' => 'Buih',
            'organo' => 'Organo',
            'buih' => 'Buih',
            'aroma' => 'Aroma',
            'kristal' => 'Kristal',
            'endapan' => 'Endapan',
            'status_disposition' => 'Status',
        ];
    }
}
