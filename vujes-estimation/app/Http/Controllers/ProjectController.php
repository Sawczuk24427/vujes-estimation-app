<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('projects')->where('client_id', $request->client_id)],
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id']);

        Project::create($validated);

        return response()->json(['message' => 'Project saved']);

    }

    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        $projects = Project::whereHas('client', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })
            ->with('client')
            ->get();

        return response()->json($projects);

    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('projects')
                    ->where('client_id', $request->client_id)
                    ->ignore($project->id)],
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project->update($validated);

        return response()->json(['message' => 'Project Updated']);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project Deleted']);
    }
}
