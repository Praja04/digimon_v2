<?php

namespace App\Http\Requests\ShelfLife;

use Illuminate\Foundation\Http\FormRequest;

class AnalysisKimiaStoreRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'shelf_life_sampling_detail_id' => 'required|exists:shelf_life_sampling_detail,id',
        ];

        if ($this->has('shift_analis') || $this->has('nama_analis')) {
            $rules['shift_analis'] = 'required|in:1,2,3';
            $rules['nama_analis'] = 'required|string|max:255';
        }

        if ($this->hasAny(['nacl', 'brix', 'aw', 'ph', 'bj', 'buih', 'aroma', 'color', 'organo', 'visco', 'total_nitrogen'])) {
            $detail = \App\Models\ShelfLifeSamplingDetail::find($this->shelf_life_sampling_detail_id);
            $bulanKe = $detail ? $detail->bulan_ke : null;

            $rules['nacl'] = 'required|string';
            $rules['brix'] = 'required|string';
            $rules['aw'] = 'required|string';
            $rules['ph'] = 'required|string';
            $rules['bj'] = 'required|string';
            $rules['buih'] = 'required|string';
            $rules['aroma'] = 'required|string|max:255';
            $rules['color'] = 'required|exists:colors,id';
            $rules['organo'] = 'required|string|max:255';

            $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
            if (!$hideVisco) {
                $rules['visco'] = 'required|string';
            }

            $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);
            if ($showTotalNitrogen) {
                $rules['total_nitrogen'] = 'required|string';
            }
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'shelf_life_sampling_detail_id' => 'ID Detail Sampling',
            'shift_analis' => 'Shift Analis',
            'nama_analis' => 'Nama Analis',
            'nacl' => 'NACL',
            'brix' => 'Brix',
            'aw' => 'AW',
            'ph' => 'pH',
            'bj' => 'BJ',
            'buih' => 'Buih',
            'aroma' => 'Aroma',
            'color' => 'Warna',
            'organo' => 'Organo',
            'visco' => 'Visco',
            'total_nitrogen' => 'Total Nitrogen',
        ];
    }
}
