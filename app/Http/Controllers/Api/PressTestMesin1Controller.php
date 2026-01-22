<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PressTestMesin1;
use Illuminate\Http\Request;

class PressTestMesin1Controller extends Controller
{
    public function index()
    {
        try {
            $pressTests = PressTestMesin1::orderBy('created_at', 'desc')->first();

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $pressTests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAll()
    {
        try {
            $pressTests = PressTestMesin1::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $pressTests,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'variant' => 'required|string|max:255',
                'jarak' => 'required',
                'batas' => 'nullable',
                'status' => 'nullable|string|max:20',
            ]);

            $pressTest = PressTestMesin1::create($validated);

            return response()->json([
                'message' => 'Data created successfully.',
                'data' => $pressTest,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
