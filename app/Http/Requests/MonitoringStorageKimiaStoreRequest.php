<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringStorageKimiaStoreRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $numericFields = [
            'volume',
        ];

        $preparedData = [];

        foreach ($numericFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $cleanedValue = trim(str_replace(' ', '', $this->input($field)));
                if ($cleanedValue !== '') {
                    $preparedData[$field] = str_replace(',', '.', $cleanedValue);
                } else {
                    $preparedData[$field] = null;
                }
            }
        }

        $this->merge($preparedData);
    }

    public function rules(): array
    {
        return [
            'production_batch_id' => 'required|exists:production_batches,id',
            'batch_range' => 'required',
            'storage' => 'nullable|string',
            'nomor_blending' => 'required',
            'volume' => 'nullable|numeric',
            'jenis_sampel' => 'required|string|in:Awal,Outlet,Tengah,Akhir,Inlet,After Drain',
        ];
    }

    public function attributes()
    {
        return [
            'batch_range' => 'Batch',
            'storage' => 'Storage',
            'nomor_blending' => 'Nomor Blending',
            'volume' => 'Volume',
            'jenis_sampel' => 'Jenis Sampel',
        ];
    }
}
