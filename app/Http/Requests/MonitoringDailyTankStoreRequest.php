<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringDailyTankStoreRequest extends FormRequest
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
            'sampling_point' => 'required|string',
            'jenis_analisa' => 'required|string',
            'jenis_sample' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'tanggal_produksi' => 'Tanggal Produksi',
            'storage' => 'Storage',
            'nomor_po' => 'Nomor PO',
            'sampling_point' => 'Sampling Point',
            'jenis_analisa' => 'Jenis Analisa',
            'jenis_sample' => 'Jenis Sample',
        ];
    }
}
