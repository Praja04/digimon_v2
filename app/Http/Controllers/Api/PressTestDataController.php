<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PressTestData;
use Illuminate\Support\Facades\Validator;

class PressTestDataController extends Controller
{
    public function index()
    {
        try {
            $data = PressTestData::orderBy('created_at', 'desc')->first();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_analis' => 'required|string|max:255',
                'shift' => 'nullable|string|max:255',
                'variant' => 'required|string|max:255',
                'mesin' => 'required|string|max:255',
                'batas' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pressTestData = PressTestData::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data created successfully',
                'data' => $pressTestData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pressTestData = PressTestData::find($id);

            if (!$pressTestData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pressTestData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $pressTestData = PressTestData::find($id);

            if (!$pressTestData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_analis' => 'sometimes|required|string|max:255',
                'shift' => 'sometimes|nullable|string|max:255',
                'variant' => 'sometimes|required|string|max:255',
                'mesin' => 'sometimes|required|string|max:255',
                'batas' => 'sometimes|required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pressTestData->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'data' => $pressTestData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pressTestData = PressTestData::find($id);

            if (!$pressTestData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            $pressTestData->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
