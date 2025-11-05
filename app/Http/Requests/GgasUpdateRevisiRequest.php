<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GgasUpdateRevisiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_productbatch_ggas' => 'required|integer|exists:production_batches,id',
            'batch_number_ggas' => 'required|string',
            'revisi_ggas' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'id_productbatch_ggas' => 'Production Batch ID',
            'batch_number_ggas' => 'Nomor Batch',
            'revisi_ggas' => 'Revisi GGA'
        ];
    }
}
