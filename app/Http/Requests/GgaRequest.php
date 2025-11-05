<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GgaRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'nacl',
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

    public function rules(): array
    {
        return [
            'brix' => 'required|numeric|min:0|max:100',
            'nacl' => 'required|numeric|min:0|max:100',
            'color' => 'required|string|max:20',
            'status_disposition' => 'required',
            'disposition_remaks' => 'nullable|string|max:255',
            'adjustment_qty_air' => 'nullable|numeric',
            'adjustment_qty_garam' => 'nullable|numeric',
            'adjustment_qty_gula' => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        return  [
            'brix' => 'Brix',
            'nacl' => 'NACL',
            'color' => 'Warna',
            'status_disposition' => 'Status',
            'disposition_remaks' => 'Catatan disposisi',
        ];
    }
}
