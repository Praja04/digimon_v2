<?php

namespace App\Http\Requests\ShelfLife;

use Illuminate\Foundation\Http\FormRequest;

class SampleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_produksi' => 'required|date',
            'storage' => 'required|string',
            'nomor_po' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'tanggal_produksi' => 'Tanggal produksi',
            'storage' => 'storage',
            'nomor_po' => 'nomor po',
        ];
    }
}
