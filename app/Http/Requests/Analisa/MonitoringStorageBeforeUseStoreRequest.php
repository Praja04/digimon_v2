<?php

namespace App\Http\Requests\Analisa;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringStorageBeforeUseStoreRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'visco',
            'brix',
            'aw',
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
            'visco' => 'required|numeric',
            'brix' => 'required|numeric|min:0|max:100',
            'aw' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'visco' => 'Visco',
            'brix' => 'Brix',
            'aw' => 'AW',
        ];
    }
}
