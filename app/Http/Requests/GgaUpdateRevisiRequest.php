<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GgaUpdateRevisiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_productbatch_gga' => 'required|integer|exists:production_batches,id',
            'batch_number_gga' => 'required|string',
            'revisi_gga' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'id_productbatch_gga' => 'Production Batch ID',
            'batch_number_gga' => 'Nomor Batch',
            'revisi_gga' => 'Revisi GGA'
        ];
    }
}
