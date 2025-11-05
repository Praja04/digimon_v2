<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RMPMStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis' => 'required|in:Gula Tebu,Gula Kelapa,Gula,Garam',
            'tanggal_kedatangan' => 'required|date',
            'supplier' => 'required|string',
            'asal_bahan' => 'required|string',
            'no_plat' => 'required|string',
            'no_spb' => 'required|string',
            'jumlah_kedatangan' => 'required|numeric',
            'lot_batch' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'jenis' => 'Jenis',
            'tanggal_kedatangan' => 'Tanggal kedatangan',
            'supplier' => 'Supplier',
            'asal_bahan' => 'Asal bahan',
            'no_plat' => 'No plat',
            'no_spb' => 'No spb',
            'jumlah_kedatangan' => 'Jumlah kedatangan',
            'lot_batch' => 'Lot batch',
        ];
    }
}
