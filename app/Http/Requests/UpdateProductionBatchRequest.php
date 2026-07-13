<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionBatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'po_number' => [
                'required',
                'numeric',
                'digits:8',
                Rule::unique('production_batches', 'po_number')
                    ->ignore($this->route('id'))
            ],
            'variant' => 'required|string|max:255',
            'date' => 'required|date',
            'batch_range' => 'required|string|max:255',
            'formulasi' => 'required|string|max:255',
            'description' => 'nullable'
        ];
    }
}
