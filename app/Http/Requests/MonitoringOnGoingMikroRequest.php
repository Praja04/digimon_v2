<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringOnGoingMikroRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tanggal_produksi' => 'required|date',
            'storage' => 'required|string',
            'nomor_po' => 'required|string',
            'variant' => 'required|string',
            'no_filler' => 'required|string',
            'koding' => 'nullable|max:5',
            'jam_koding' => 'required',
            'jenis_sampel_1' => 'required|string',
            'filling_date' => 'required|date',
        ];

        if ($this->variant) {
            $isKempuOrJeriken = stripos($this->variant, 'kempu') !== false ||
                stripos($this->variant, 'jeriken') !== false;

            if ($isKempuOrJeriken) {
                $rules['no_kempu_jeriken'] = 'required|integer';
                $rules['running_number'] = 'required|string';
            } else {
                $rules['no_kempu_jeriken'] = 'nullable|integer';
            }
        } else {
            $rules['no_kempu_jeriken'] = 'nullable|integer';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'tanggal_produksi' => 'Tanggal Produksi',
            'storage' => 'Storage',
            'nomor_po' => 'Nomor PO',
            'variant' => 'Variant',
            'no_filler' => 'Nomor Filler / Mesin',
            'no_kempu_jeriken' => 'Nomor Kempu / Jeriken',
            'running_number' => 'Running Number',
            'koding' => 'Koding',
            'jam_koding' => 'Jam Koding',
            'jenis_sampel_1' => 'Jenis Sampel 1',
            'filling_date' => 'Tanggal Filling',
        ];
    }
}
