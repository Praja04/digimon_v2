<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RMPMStoreRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'no_spb' => $this->no_spb !== null ? trim($this->no_spb) : null,
            'jenis'  => $this->jenis !== null ? trim($this->jenis) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis' => 'required|string|max:100',
            'tanggal_kedatangan' => 'required|date',
            'supplier' => 'required|string',
            'asal_bahan' => 'required|string',
            'no_plat' => 'required|string',
            'no_spb' => [
                'required',
                'string',
                Rule::unique('identitas_rm', 'no_spb')->where(function ($query) {
                    $cleanJenis = strtolower(trim($this->jenis ?? ''));
                    return $query->whereRaw('LOWER(TRIM(jenis)) = ?', [$cleanJenis]);
                }),
            ],
            'jumlah_kedatangan' => 'required|numeric',
            'lot_batch' => 'required|string',
            'standart_bps' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'no_spb.unique' => 'Nomor SPB ini sudah pernah diinput untuk jenis bahan tersebut. Kombinasi SPB dan material tidak boleh duplikat.',
        ];
    }

    public function attributes()
    {
        return [
            'jenis' => 'Jenis',
            'tanggal_kedatangan' => 'Tanggal kedatangan',
            'supplier' => 'Supplier',
            'asal_bahan' => 'Asal bahan',
            'no_plat' => 'No plat',
            'no_spb' => 'No spb',
            'jumlah_kedatangan' => 'Jumlah kedatangan',
            'lot_batch' => 'Lot batch',
            'standart_bps' => 'Standart BPS',
        ];
    }
}
