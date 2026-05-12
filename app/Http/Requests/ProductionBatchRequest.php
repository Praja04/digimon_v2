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
            'po_number' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
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
