<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        return new ProjectResource($project);

    }

    public function index(Request $request)
    {
        $user_id = auth()->id();
        $projects = Project::whereHas('client', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->with('client')
            ->get();

        return ProjectResource::collection($projects);

    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('update', $project);
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('delete', $project);
        $project->delete();

        return response()->json(['message' => 'Project Deleted'], 200);
    }
}
