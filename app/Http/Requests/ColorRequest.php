<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->id ?? null;
        return [
            'code' => 'nullable|string|max:255|unique:colors,code,' . $id,
            'name' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Kode',
            'name' => 'Nama',
        ];
    }
}
