<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProductionBatchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'po_number' => [
                'required',
                'numeric',
                'digits:8',
                Rule::unique('production_batches', 'po_number')
                ->ignore($this->route('id')) // 🔥 ini penting
            ],
            'variant' => 'required|string|max:255',
            'date' => 'required|date',
            'batch_range' => 'required|string|max:255',
            'description' => 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Id',
            'po_number' => 'Nomor PO',
            'variant' => 'Varian',
            'date' => 'Tanggal',
            'batch_range' => 'Rentang Batch Masak',
            'description' => 'Keterangan'
        ];
    }
}
