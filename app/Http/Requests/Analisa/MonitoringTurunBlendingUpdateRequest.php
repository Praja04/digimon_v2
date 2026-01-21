<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringTurunBlendingUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'visco',
            'nacl',
            'aw',
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
            'aw' => 'required|numeric',
            'organo' => 'required|string',
            'status_disposition' => 'required',
            'disposition' => 'nullable|in:Release,Release Bersyarat,Resampling,Adjustment,Reject,Repro,Jalan Bareng,Leveling',
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
            'aw' => 'AW',
            'organo' => 'Organo',
            'status_disposition' => 'Status',
        ];
    }
}
