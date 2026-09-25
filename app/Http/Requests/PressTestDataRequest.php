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
            'nama_analis_field' => 'nullable|string|max:255',
            'variant'           => 'nullable|string|max:255',
            'variant_name'      => 'required_without:variant|nullable|string|max:255',
            'batas'             => 'nullable',
            'mesin_press_test'  => 'nullable',
            'ok_min'            => 'nullable|numeric',
            'ok_max'            => 'nullable|numeric',
            'bocor_min'         => 'nullable|numeric',
            'bocor_max'         => 'nullable|numeric',
            'gap'               => 'nullable|numeric',
            'note'              => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_analis_field' => 'Nama Analis Field',
            'variant'           => 'Variant',
            'variant_name'      => 'Variant Name',
            'batas'             => 'Batas',
            'mesin_press_test'  => 'Mesin Press Test',
            'ok_min'            => 'OK Min',
            'ok_max'            => 'OK Max',
            'bocor_min'         => 'Bocor Min',
            'bocor_max'         => 'Bocor Max',
            'gap'               => 'Gap',
            'note'              => 'Note',
        ];
    }
}
