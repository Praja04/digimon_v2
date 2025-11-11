<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringOngoingKimiaRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'visco',
            'brix',
            'aw',
            'nacl',
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
            'berat_jenis' => 'required',
            'visco' => 'required|numeric|max:20',
            'brix' => 'required|numeric|min:0|max:100',
            'aw' => 'required|numeric|max:20',
            'nacl' => 'required|numeric|min:0|max:100',
            'ph' => 'nullable|numeric',
            'color' => 'required|string',
            'organo' => 'required|string|max:20',
            'status_disposition' => 'required',
            'disposition' => 'nullable',
            'disposition_remarks' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'berat_jenis' => 'Berat Jenis',
            'visco' => 'Visco',
            'brix' => 'Brix',
            'aw' => 'AW',
            'nacl' => 'Nacl',
            'bj' => 'Bj',
            'ph' => 'pH',
            'organo' => 'Organo',
            'color' => 'Warna',
            'status_disposition' => 'Status',
        ];
    }
}
