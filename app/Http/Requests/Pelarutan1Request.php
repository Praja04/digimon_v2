<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Pelarutan1Request extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'nacl',
            'adjustment_qty_gula_tebu',
            'adjustment_qty_gula_kelapa'
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
            'id' => 'required|integer|exists:pelarutan_1,id',
            'brix' => 'required|numeric|min:0|max:100',
            'nacl' => 'required|numeric|min:0|max:100',
            'organo' => 'required|string',
            'status_disposition' => 'required',
            'disposition_remark' => 'nullable|string|max:255',
            'adjustment_qty_gula_tebu' => 'nullable|numeric',
            'adjustment_qty_gula_kelapa' => 'nullable|numeric',
            'disposition' => 'nullable|string',
            'revisi' => 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID Pelarutan 1',
            'brix' => 'Brix',
            'nacl' => 'NACL',
            'organo' => 'Organo',
            'status_disposition' => 'Status',
            'disposition_remark' => 'Catatan disposisi',
            'adjustment_qty_gula_tebu' => 'Adjustment Gula Tebu',
            'adjustment_qty_gula_kelapa' => 'Adjustment Gula Kelapa',
            'disposition' => 'Disposisi',
        ];
    }
}