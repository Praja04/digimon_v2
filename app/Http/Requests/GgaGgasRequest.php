<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GgaGgasRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'production_batch_id' => 'required|exists:production_batches,id',
            'batch_number' => 'required|integer',
            'dissolver_number' => 'required',
            'type' => 'required|in:GGA,GGAS',
        ];
    }

    public function attributes()
    {
        return [
            'batch_number' => 'Nomor Batch',
            'dissolver_number' => 'Nomor Dissolver',
            'type' => 'Tipe',
        ];
    }
}
