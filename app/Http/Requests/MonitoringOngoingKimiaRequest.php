<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringOngoingKimiaRequest extends FormRequest
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
            'filling_date' => 'required|date',
            'jam_koding' => 'required',
            'jenis_sampel' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'storage' => 'Storage',
            'nomor_po' => 'Nomor PO',
            'variant' => 'Variant',
            'filling_date' => 'Tanggal Filling',
            'jam_koding' => 'Jam Koding',
            'jenis_sampel' => 'Jenis Sampel',
        ];
    }
}
