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
        try {
            $response = Http::timeout(8)
                ->withOptions([
                    'proxy' => '',
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])
                ->get($this->masterBarangUrl);

            $response->throw();

            $payload = $response->json();
            $data = $payload['data'] ?? (is_array($payload) ? $payload : []);

            if (!empty($data)) {
                cache()->put('wpm_master_barang_cache', $data, now()->addHours(12));
            }

            return $data;
        } catch (\Throwable $exception) {
            $cached = cache()->get('wpm_master_barang_cache');

            if (!empty($cached) && is_array($cached)) {
                return $cached;
            }

            throw $exception;
        }
    }
}