<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlendingAwalStoreRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'volume',
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
            'production_batch_id' => 'required|exists:production_batches,id',
            'batch_start' => 'required|integer|different:batch_end',
            'batch_end' => 'required|integer',
            'storage' => 'nullable|string',
            'nomor_blending' => 'required',
            'volume' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'batch_start' => 'Batch Pertama',
            'batch_end' => 'Batch Kedua',
            'storage' => 'Storage',
            'nomor_blending' => 'Nomor Blending',
            'volume' => 'Volume'
        ];
    }
}
