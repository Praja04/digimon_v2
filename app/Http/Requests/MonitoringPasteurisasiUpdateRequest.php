<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringPasteurisasiUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'volume_revisi',
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
            'storage' => 'nullable|string',
            'no_blending_revisi' => 'required',
            'volume_revisi' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'storage' => 'Storage',
            'no_blending_revisi' => 'Nomor Blending',
            'volume_revisi' => 'Volume'
        ];
    }
}
