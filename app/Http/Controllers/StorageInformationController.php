<?php

namespace App\Http\Controllers;

use App\Models\MonitoringStorageKimia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StorageInformationController extends Controller
{
    public function index()
    {
        $storageData = MonitoringStorageKimia::with(['productionBatch', 'additionalBatches'])
            ->whereNotNull('storage')
            ->orderBy('storage', 'asc')
            ->get();

        $tanks = $this->transformStorageData($storageData);

        return view('app.storage-information.index', compact('tanks'));
    }

    public function getData()
    {
        $storageData = MonitoringStorageKimia::with(['productionBatch', 'additionalBatches'])
            ->whereNotNull('storage')
            ->orderBy('storage', 'asc')
            ->get();

        $tanks = $this->transformStorageData($storageData);

        return response()->json([
            'success' => true,
            'data' => $tanks
        ]);
    }

    private function transformStorageData($storageData)
    {
        $tanks = [];

        // Define all storage positions
        $allStorages = [
            'A1',
            'A2',
            'A3',
            'A4',
            'A5',
            'B1',
            'B2',
            'B3',
            'B4',
            'B5',
            'C1',
            'C2',
            'C3',
            'C4',
            'C5',
            'D1',
            'D2',
            'D3',
            'D4',
            'D5'
        ];

        // Mapping storage ke kode huruf (sesuai gambar papan tulis)
        $storageCodeMap = [
            'A1' => 'P',
            'A2' => 'Q',
            'A3' => 'R',
            'A4' => 'S',
            'A5' => 'T',
            'B1' => 'K',
            'B2' => 'L',
            'B3' => 'M',
            'B4' => 'N',
            'B5' => 'O',
            'C1' => 'F',
            'C2' => 'G',
            'C3' => 'H',
            'C4' => 'I',
            'C5' => 'J',
            'D1' => 'A',
            'D2' => 'B',
            'D3' => 'C',
            'D4' => 'D',
            'D5' => 'E'
        ];

        foreach ($allStorages as $storageCode) {
            $storage = $storageData->firstWhere('storage', $storageCode);

            if ($storage) {
                $tank = $this->buildTankData($storage, $storageCode, $storageCodeMap);
            } else {
                $tank = $this->buildEmptyTank($storageCode, $storageCodeMap);
            }

            $tanks[] = $tank;
        }

        return $tanks;
    }

    private function buildTankData($storage, $storageCode, $storageCodeMap)
    {
        $productVariant = $storage->productionBatch->variant ?? 'N/A';
        $batchDate = $storage->productionBatch->date
            ? Carbon::parse($storage->productionBatch->date)->format('d/m/y')
            : '-';

        // Tentukan status display dan CSS class
        $statusInfo = $this->getStatusInfo($storage);

        // Generate line (dummy untuk sementara)
        $line = $this->getLineForStorage($storageCode);

        // Build batch number
        $batchNumber = $this->getBatchNumber($storage);

        $tank = [
            'id' => $storageCode . ' (' . $storageCodeMap[$storageCode] . ')',
            'line' => $line,
            'prod' => $productVariant,
            'tgl' => $batchDate,
            'vol' => number_format($storage->volume ?? 0, 0, ',', '.'),
            'st' => $statusInfo['status'],
            'css' => $statusInfo['css'],
        ];

        // Tambahkan batch number jika ada
        if ($batchNumber) {
            $tank['batch'] = $batchNumber;
        }

        // Tambahkan info batch tambahan jika ada (untuk jalan bareng/leveling)
        if ($storage->additionalBatches->count() > 0) {
            $batchNumbers = $storage->additionalBatches->pluck('batch')->implode(', ');
            $tank['batch_info'] = $batchNumbers;
        }

        return $tank;
    }

    private function buildEmptyTank($storageCode, $storageCodeMap)
    {
        return [
            'id' => $storageCode . ' (' . $storageCodeMap[$storageCode] . ')',
            'line' => '',
            'prod' => 'Standby',
            'tgl' => '',
            'vol' => '',
            'st' => 'EMPTY',
            'css' => 'bg-cleaning',
        ];
    }

    private function getStatusInfo($storage)
    {
        $disposition = $storage->disposition;
        $status = $storage->status;

        // Mapping disposition ke status display
        switch ($disposition) {
            case 'Release':
                return [
                    'status' => 'DONE PASTEUR',
                    'css' => 'bg-done'
                ];

            case 'Release Bersyarat':
                return [
                    'status' => 'RELEASE BERSYARAT',
                    'css' => 'bg-warning'
                ];

            case 'Resampling':
                return [
                    'status' => 'RESAMPLING',
                    'css' => 'bg-progress'
                ];

            case 'Adjustment':
                return [
                    'status' => 'ADJUSTMENT',
                    'css' => 'bg-progress'
                ];

            case 'Reject':
                return [
                    'status' => 'REJECT',
                    'css' => 'bg-danger'
                ];

            case 'Repro':
                return [
                    'status' => 'REPRO',
                    'css' => 'bg-warning'
                ];

            case 'Jalan Bareng':
                return [
                    'status' => 'JALAN BARENG',
                    'css' => 'bg-progress'
                ];

            case 'Leveling':
                return [
                    'status' => 'LEVELING',
                    'css' => 'bg-progress'
                ];

            default:
                // Jika tidak ada disposition, cek status
                if ($status == 'OK') {
                    return [
                        'status' => 'ON PROGRESS',
                        'css' => 'bg-progress'
                    ];
                } else if ($status == 'NOT OK') {
                    return [
                        'status' => 'NOT OK',
                        'css' => 'bg-warning'
                    ];
                } else {
                    return [
                        'status' => 'STANDBY',
                        'css' => 'bg-cleaning'
                    ];
                }
        }
    }

    private function getLineForStorage($storageCode)
    {
        // Dummy line assignment (bisa disesuaikan dengan logic bisnis)
        // Misal: A1-A4, B1, B3, B4, C1-C4, D1-D4 = Line 1
        // A5, B2, B5, C5, D5 = Line 2

        $line2Storages = ['A5', 'B2', 'B5', 'C5', 'D5'];

        return in_array($storageCode, $line2Storages) ? 'L2' : 'L1';
    }

    private function getBatchNumber($storage)
    {
        if ($storage->productionBatch && $storage->productionBatch->batch_range) {
            return $storage->productionBatch->batch_range;
        }

        return null;
    }
}
