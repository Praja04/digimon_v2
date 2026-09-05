<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WpmApiService
{
    public function getMasterBarang(): array
    {
        $url = config(
            'services.wpm.master_barang_url'
        );

        if (empty($url)) {
            throw new RuntimeException(
                'Konfigurasi URL API WPM belum tersedia.'
            );
        }

        $response = Http::acceptJson()
            ->connectTimeout(10)
            ->timeout(30)
            ->retry(2, 500)
            ->get($url);

        if ($response->failed()) {
            throw new RuntimeException(
                'API WPM gagal diakses. HTTP status: '
                . $response->status()
            );
        }

        $result = $response->json();

        if (
            ! is_array($result)
            || ($result['success'] ?? false) !== true
        ) {
            throw new RuntimeException(
                $result['message']
                ?? 'Respons API WPM tidak valid.'
            );
        }

        return is_array($result['data'] ?? null)
            ? $result['data']
            : [];
    }
}