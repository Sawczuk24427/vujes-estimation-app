<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class EstimationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $estimations = Estimation::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('project.client')->get();

        return response()->json($estimations, 201);
    }

    public function store(StoreEstimationRequest $request)
    {
        $validated = $request->validated();

        if ($validated['type'] === 'hourly') {
            $validated['price'] = $validated['hours'] * $validated['hourly_rate'];
        }

        $estimation = Estimation::create($validated);

        return response()->json(['message' => 'Estimation saved', 'estimation'=> $estimation], 201);
    }

    public function update(UpdateEstimationRequest $request, $id)
    {
        $estimation = Estimation::findOrFail($id);
        Gate::authorize('update', $estimation);
        $validated = $request->validated();

        if ($validated['type'] === 'hourly') {
            $validated['price'] = $validated['hours'] * $validated['hourly-rate'];
        }

        $estimation->update($validated);

        return response()->json(['message' => 'Estimation updated', 'estimation'=>$estimation], 201);

    }

    public function destroy($id)
    {
        $estimation = Estimation::findOrFail($id);
        Gate::authorize('delete', $estimation);
        $estimation->delete();

        return response()->json(['message' => 'Estimation deleted'], 201);

    }
}
