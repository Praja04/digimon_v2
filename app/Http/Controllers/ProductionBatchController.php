<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionBatchRequest;
use App\Models\ProductionBatch;

class ProductionBatchController extends Controller
{
    public function index()
    {
        return view('app.productionbatch.menu');
    }

    public function create()
    {
        return view('app.productionbatch.create');
    }

    public function store(ProductionBatchRequest $request)
    {
        try {
            if (preg_match('/(\d+)\s*-\s*(\d+)/', $request->batch_range, $match)) {
                $start = (int) $match[1];
                $end = (int) $match[2];
            } else {
                $start = $end = (int) $request->batch_range;
            }

            $batches = range($start, $end);
            $chunks = array_chunk($batches, 10);

            foreach ($chunks as $group) {
                ProductionBatch::create([
                    'po_number' => $request->po_number,
                    'variant' => $request->variant,
                    'date' => $request->date,
                    'batch_range' => min($group) . '-' . max($group),
                    'description' => $request->description,
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
