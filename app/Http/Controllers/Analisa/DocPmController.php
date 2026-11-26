<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\LaporanPackagingExport;
use App\Http\Controllers\Controller;
use App\Models\PackagingIncoming;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocPmController extends Controller
{
    /**
     * Tampilkan Halaman Form & Preview Dokumen Hasil Pemeriksaan Packaging Material
     * Mendukung data Final, Draft, maupun Kedatangan Baru (Menunggu Sampling)
     */
    public function index(Request $request)
    {
        $filterJenis = $request->input('jenis'); // 'karton', 'pouch', 'inner', atau null

        $query = PackagingIncoming::with([
            'jenisIncoming',
            'jenisMaterial',
            'supplier',
            'uom',
            'samplingStatus',
            'packagingKartonSampling.creator',
            'packagingKartonSampling.updater',
            'packagingKartonSamplingDraft',
            'kartonBct',
            'packagingPouchSampling.creator',
            'packagingPouchSampling.updater',
            'packagingPouchSamplingDraft',
            'packagingInnerOuterSampling.creator',
            'packagingInnerOuterSampling.updater',
            'packagingInnerOuterSamplingDraft',
        ])
        ->orderBy('tanggal_kedatangan', 'desc')
        ->orderBy('id', 'desc');

        if ($filterJenis) {
            $query->whereHas('jenisIncoming', function ($q) use ($filterJenis) {
                if ($filterJenis === 'karton') {
                    $q->where('nama', 'like', '%Kardus%')->orWhere('nama', 'like', '%Karton%');
                } elseif ($filterJenis === 'pouch') {
                    $q->where('nama', 'like', '%Pouch%');
                } elseif ($filterJenis === 'inner') {
                    $q->where('nama', 'like', '%Inner%')->orWhere('nama', 'like', '%Outer%');
                }
            });
        }

        $incomingList = $query->get();

        $selectedId = $request->input('id');
        if (!$selectedId || !$incomingList->contains('id', $selectedId)) {
            // Pilih item pertama yang ada, baik sudah final, draft, maupun incoming baru
            $selectedId = $incomingList->first()?->id;
        }

        $docData = null;
        if ($selectedId) {
            $docData = $this->getDocumentData($selectedId);
        }

        return view('app.doc_pm.index', [
            'incomingList' => $incomingList,
            'selectedId'   => $selectedId,
            'filterJenis'  => $filterJenis,
            'docData'      => $docData,
        ]);
    }

    /**
     * Download Excel Dokumen Pemeriksaan PM (Final maupun Draft)
     */
    public function exportExcel(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return redirect()->back()->with('error', 'Silakan pilih data kedatangan Packaging Material terlebih dahulu.');
        }

        $incoming = PackagingIncoming::with([
            'jenisIncoming',
            'jenisMaterial',
            'supplier',
            'uom',
            'samplingStatus',
            'packagingKartonSampling.creator',
            'packagingKartonSampling.updater',
            'packagingKartonSamplingDraft',
            'kartonBct',
            'packagingPouchSampling.creator',
            'packagingPouchSampling.updater',
            'packagingPouchSamplingDraft',
            'packagingInnerOuterSampling.creator',
            'packagingInnerOuterSampling.updater',
            'packagingInnerOuterSamplingDraft',
        ])->findOrFail($id);

        $namaJenis = strtolower($incoming->jenisIncoming?->nama ?? 'PM');
        $prefix = 'PM';
        if (str_contains($namaJenis, 'pouch')) {
            $prefix = 'POUCH';
        } elseif (str_contains($namaJenis, 'karton') || str_contains($namaJenis, 'kardus')) {
            $prefix = 'KARTON';
        } elseif (str_contains($namaJenis, 'inner') || str_contains($namaJenis, 'outer')) {
            $prefix = 'INNER_SACHET';
        }

        $cleanSpb = preg_replace('/[^A-Za-z0-9_\-]/', '_', $incoming->no_spb ?? 'SPB');
        $dateStr = $incoming->tanggal_kedatangan ? Carbon::parse($incoming->tanggal_kedatangan)->format('Ymd') : date('Ymd');
        $fileName = "HASIL_PENGECEKAN_{$prefix}_{$cleanSpb}_{$dateStr}.xlsx";

        return Excel::download(new LaporanPackagingExport($incoming), $fileName);
    }

    /**
     * Ekstrak dan format data untuk preview di Blade View
     * Mendukung data Final, Draft, atau Blanko (Menunggu Sampling)
     */
    protected function getDocumentData(int $id): ?array
    {
        $incoming = PackagingIncoming::with([
            'jenisIncoming',
            'jenisMaterial',
            'supplier',
            'uom',
            'samplingStatus',
            'packagingKartonSampling.creator',
            'packagingKartonSampling.updater',
            'packagingKartonSamplingDraft',
            'kartonBct',
            'packagingPouchSampling.creator',
            'packagingPouchSampling.updater',
            'packagingPouchSamplingDraft',
            'packagingInnerOuterSampling.creator',
            'packagingInnerOuterSampling.updater',
            'packagingInnerOuterSamplingDraft',
        ])->find($id);

        if (!$incoming) return null;

        $namaJenis = strtolower($incoming->jenisIncoming?->nama ?? '');
        $category = 'inner';
        $formNo = 'FRM/QRP/02/001/001-00';
        $title = 'FORM HASIL PENGECEKAN INNER SACHET';

        if (str_contains($namaJenis, 'pouch')) {
            $category = 'pouch';
            $formNo = 'FRM/QRP/02/002/001-01';
            $title = 'HASIL PENGECEKAN POUCH';
        } elseif (str_contains($namaJenis, 'karton') || str_contains($namaJenis, 'kardus')) {
            $category = 'karton';
            $formNo = 'FRM/QRP/02/003/001-01';
            $title = 'HASIL PENGECEKAN KARTON';
        }

        $samplingFinal = match ($category) {
            'karton' => $incoming->packagingKartonSampling,
            'pouch'  => $incoming->packagingPouchSampling,
            'inner'  => $incoming->packagingInnerOuterSampling,
        };

        $samplingDraft = match ($category) {
            'karton' => $incoming->packagingKartonSamplingDraft,
            'pouch'  => $incoming->packagingPouchSamplingDraft,
            'inner'  => $incoming->packagingInnerOuterSamplingDraft,
        };

        $sampling = $samplingFinal ?? $samplingDraft;
        $isFinal = (bool) $samplingFinal;
        $isDraft = (bool) (!$samplingFinal && $samplingDraft);

        return [
            'incoming'  => $incoming,
            'category'  => $category,
            'formNo'    => $formNo,
            'title'     => $title,
            'sampling'  => $sampling,
            'isFinal'   => $isFinal,
            'isDraft'   => $isDraft,
            'bct'       => $incoming->kartonBct,
        ];
    }
}
