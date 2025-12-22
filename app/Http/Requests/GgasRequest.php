<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GgasRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'brix',
            'nacl',
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
            'nacl' => 'nullable|numeric|min:0|max:100',
            'organo' => 'required|string',
            'status_disposition' => 'required',
            'disposition_remaks' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return  [
            'brix' => 'Brix',
            'nacl' => 'NACL',
            'organo' => 'Organo',
            'status_disposition' => 'Status',
            'disposition_remaks' => 'Catatan disposisi',
        ];
    }
}
