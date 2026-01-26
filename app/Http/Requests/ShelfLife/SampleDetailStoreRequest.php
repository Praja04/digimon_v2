<?php

namespace App\Http\Requests\ShelfLife;

use Illuminate\Foundation\Http\FormRequest;

class SampleDetailStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->filled('id');

        return [
            'variant_fg' => $isUpdate ? 'sometimes|string' : 'required|string',
            'kelompok_sample' => 'required|string',
            'tanggal_filling' => 'required|date',
            'kelompok_tanggal' => 'required',
            'koding' => 'required|max:5',
            'jam_koding' => 'required|string',
            'bulan_ke' => $isUpdate ? 'sometimes|integer' : 'required|integer',
            'ruang_sl' => 'required|string',
            'bin_location' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'variant_fg' => 'Variant FG',
            'kelompok_sample' => 'Kelompok Sample',
            'tanggal_filling' => 'Tanggal Filling',
            'kelompok_tanggal' => 'Kelompok Tanggal',
            'koding' => 'Koding',
            'jam_koding' => 'Jam Koding',
            'bulan_ke' => 'Bulan Ke',
            'ruang_sl' => 'Ruang SL',
            'bin_location' => 'Bin Location',
        ];
    }
}
