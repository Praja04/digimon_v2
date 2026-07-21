<?php

namespace App\Http\Controllers;

use App\Models\JenisIncoming;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IncomingController extends Controller
{
    public function index(string $jenis = 'inner'): View
    {
        $jenisSlug = Str::slug($jenis);

        /*
         * Tidak menggunakan filter kategori = Incoming,
         * karena kategori di database berisi Inner, Outer,
         * Karton, dan Others.
         */
        $jenisIncoming = JenisIncoming::query()
            ->orderBy('id')
            ->get()
            ->first(function (JenisIncoming $item) use ($jenisSlug): bool {
                return Str::slug($item->nama) === $jenisSlug;
            });

        abort_if(
            ! $jenisIncoming,
            404,
            'Jenis incoming tidak ditemukan.'
        );

        /*
         * Untuk sekarang hanya Inner yang memiliki data.
         * Jenis lainnya menampilkan layout yang sama dengan tabel kosong.
         */
        $data = $jenisSlug === 'inner'
            ? $this->getInnerData()
            : collect();

        return view('incoming.index', [
            'jenisIncoming' => $jenisIncoming,
            'data' => $data,
        ]);
    }

    private function getInnerData(): Collection
    {
        return collect([
            (object) [
                'no_spb' => '9000777251',
                'jenis_incoming' => 'Inner',
                'jenis_material' => 'Inner Yellow Bean 20G Lunch Box',
                'status' => 'Belum Sampling',
            ],
            (object) [
                'no_spb' => '9001000471',
                'jenis_incoming' => 'Inner',
                'jenis_material' => 'Inner Yellow Bean 20G Lunch Box',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '9001000478',
                'jenis_incoming' => 'Inner',
                'jenis_material' => 'Inner Black Bean 400G Lunch Box',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '123453672',
                'jenis_incoming' => 'Inner',
                'jenis_material' => '',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '123453672',
                'jenis_incoming' => 'Inner',
                'jenis_material' => '',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '123453672',
                'jenis_incoming' => 'Inner',
                'jenis_material' => '',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '123453672',
                'jenis_incoming' => 'Inner',
                'jenis_material' => '',
                'status' => 'Sudah Sampling',
            ],
            (object) [
                'no_spb' => '123453672',
                'jenis_incoming' => 'Inner',
                'jenis_material' => '',
                'status' => 'Sudah Sampling',
            ],
        ]);
    }
}