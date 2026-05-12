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
            'id' => 'required|integer',
            'po_number' => 'required|numeric|digits:8|unique:production_batches,po_number',
            'variant' =>  'required|string|max:255',
            'date' => 'required|date',
            'batch_range' => 'required|string|max:255',
            'description' => 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'po_number' => 'Nomor PO',
            'variant' => 'Varian',
            'date' => 'Tanggal',
            'batch_range' => 'Rentang Batch Masak',
            'description' => 'Keterangan'
        ];
    }
}
