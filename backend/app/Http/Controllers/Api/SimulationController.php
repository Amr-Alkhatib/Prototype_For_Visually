<?php

namespace App\Http\Controllers\Api;

use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SimulationController
{
    /**
     * Get all simulations for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $per_page = $request->query('per_page', 15);
            $sort_by = $request->query('sort_by', 'created_at');
            $order = $request->query('order', 'desc');

            $simulations = $request->user()
                ->simulations()
                ->orderBy($sort_by, $order)
                ->paginate($per_page);

            return response()->json($simulations, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new simulation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'configuration' => 'required|array',
            'results' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $simulation = Simulation::create([
                'user_id' => $request->user()->id,
                'name' => $request->name ?? 'Simulation ' . now()->format('Y-m-d H:i'),
                'description' => $request->description,
                'configuration' => $request->configuration,
                'results' => $request->results,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'message' => 'Simulation saved successfully',
                'simulation' => $simulation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show a specific simulation
     */
    public function show(Request $request, $id)
    {
        try {
            $simulation = Simulation::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            return response()->json($simulation, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Simulation not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a simulation
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'configuration' => 'sometimes|array',
            'results' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $simulation = Simulation::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $simulation->update($request->only(['name', 'description', 'configuration', 'results', 'notes']));

            return response()->json([
                'message' => 'Simulation updated successfully',
                'simulation' => $simulation,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Simulation not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a simulation
     */
    public function destroy(Request $request, $id)
    {
        try {
            $simulation = Simulation::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $simulation->delete();

            return response()->json([
                'message' => 'Simulation deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Simulation not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete simulations
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:simulations,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Simulation::whereIn('id', $request->ids)
                ->where('user_id', $request->user()->id)
                ->delete();

            return response()->json([
                'message' => 'Simulations deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
