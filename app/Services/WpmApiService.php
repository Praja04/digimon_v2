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

    public function getMasterBarang(?string $query = null): array
    {
        $queryParams = [];
        $trimmedQuery = trim((string) $query);

        if ($trimmedQuery !== '') {
            $queryParams['q'] = $trimmedQuery;
        }

        $cacheKey = 'wpm_master_barang_' . md5(json_encode($queryParams));

        try {
            $response = Http::timeout(8)
                ->withOptions([
                    'proxy' => '',
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])
                ->get($this->masterBarangUrl, $queryParams);

            $response->throw();

            $payload = $response->json();

            $data = [];
            if (isset($payload['data']['data']) && is_array($payload['data']['data'])) {
                $data = $payload['data']['data'];
            } elseif (isset($payload['data']) && is_array($payload['data'])) {
                $data = $payload['data'];
            } elseif (is_array($payload)) {
                $data = $payload;
            }

            if (!empty($data)) {
                cache()->put($cacheKey, $data, now()->addMinutes(15));
            }

            return $data;
        } catch (\Throwable $exception) {
            $cached = cache()->get($cacheKey);

            if (!empty($cached) && is_array($cached)) {
                return $cached;
            }

            throw $exception;
        }
    }
}