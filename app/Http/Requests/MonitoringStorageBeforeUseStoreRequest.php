<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringStorageBeforeUseStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'storage' => 'required',
            'jenis_sample' => 'required',
            'waktu_selesai_pemakaian' => 'required',
            'estimasi_kadaluarsa' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'storage' => 'Storage',
            'jenis_sample' => 'Jenis sample',
            'waktu_selesai_pemakaian' => 'Waktu selesai pemakaian',
            'estimasi_kadaluarsa' => 'Estimasi kadaluarsa'
        ];
    }
}
