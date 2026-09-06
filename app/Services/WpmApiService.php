<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WpmApiService
{
    private string $masterBarangUrl =
        'http://10.11.10.130:8087/api/wpm/master-barang';

    public function getMasterBarang(): array
    {
        $response = Http::timeout(10)
            ->get($this->masterBarangUrl);

        $response->throw();

        return $response->json();
    }
}