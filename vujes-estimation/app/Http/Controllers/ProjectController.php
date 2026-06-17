<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create($validated);

        return response()->json(['message' => 'Project saved', 'project'=>$project], 201);

    }

    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        $projects = Project::whereHas('client', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->with('client')
            ->get();

        return response()->json($projects, 201);

    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('update', $project);
        $validated = $request->validated();

        $project->update($validated);

        return response()->json(['message' => 'Project Updated', 'project'=>$project], 201);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('delete', $project);
        $project->delete();

        return response()->json(['message' => 'Project Deleted'], 201);
    }
}
