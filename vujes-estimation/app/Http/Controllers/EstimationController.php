<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstimationRequest;
use App\Http\Requests\UpdateEstimationRequest;
use App\Http\Resources\EstimationResource;
use App\Models\Estimation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EstimationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $estimations = Estimation::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('project.client')->get();

        return EstimationResource::collection($estimations);
    }

    public function store(StoreEstimationRequest $request)
    {
        $validated = $request->validated();

        if ($validated['type'] === 'hourly') {
            $validated['price'] = $validated['hours'] * $validated['hourly_rate'];
        }

        $estimation = Estimation::create($validated);

        return new EstimationResource($estimation);
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

        return new EstimationResource($estimation);

    }

    public function destroy($id)
    {
        $estimation = Estimation::findOrFail($id);
        Gate::authorize('delete', $estimation);
        $estimation->delete();

        return response()->json(['message' => 'Estimation deleted'], 200);

    }
}
