<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WpmApiService
{
    private string $masterBarangUrl;

    public function __construct()
    {
        $this->masterBarangUrl = env(
            'WPM_API_URL',
            'http://10.11.10.130:8087/api/wpm/master-barang'
        );
    }

    public function getMasterBarang(): array
    {
        $response = Http::timeout(10)
            ->get($this->masterBarangUrl);

        $response->throw();

        $payload = $response->json();

        return $payload['data'] ?? (is_array($payload) ? $payload : []);
    }
}
