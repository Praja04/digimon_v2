<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PressTestDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_analis' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'batas' => 'required',
            'mesin' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_analis' => 'Nama Analis',
            'variant' => 'Variant',
            'batas' => 'Batas',
            'mesin' => 'Mesin',
        ];
    }
}
