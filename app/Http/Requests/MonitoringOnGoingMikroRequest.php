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
        return [
            'storage' => 'required|string',
            'nomor_po' => 'required|string',
            'variant' => 'required|string',
            'no_filler' => 'required|string',
            'no_kempu_jeriken' => 'required|string',
            'koding' => 'required|string',
            'jam_koding' => 'required',
            'jenis_sampel_1' => 'required|string',
            'filling_date' => 'required|date',
        ];
    }

    public function attributes(): array
    {
        return [
            'storage' => 'Storage',
            'nomor_po' => 'Nomor PO',
            'variant' => 'Variant',
            'no_filler' => 'Nomor Filler',
            'no_kempu_jeriken' => 'Nomor Kempu / Jeriken',
            'koding' => 'Koding',
            'jam_koding' => 'Jam Koding',
            'jenis_sampel_1' => 'Jenis Sampel 1',
            'filling_date' => 'Tanggal Filling',
        ];
    }
}
