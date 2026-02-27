<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Pelarutan1UpdateRevisiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_productbatch_pelarutan_1' => 'required|integer|exists:production_batches,id',
            'batch_number_pelarutan_1' => 'required|string',
            'revisi_pelarutan_1' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'id_productbatch_pelarutan_1' => 'Production Batch ID',
            'batch_number_pelarutan_1' => 'Nomor Batch',
            'revisi_pelarutan_1' => 'Revisi Pelarutan 1'
        ];
    }
}
