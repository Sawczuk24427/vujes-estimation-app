<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimation;
use App\Models\Project;
use Illuminate\Validation\Rule;

class EstimationController extends Controller
{
    public function index(Request $request){
        $userId = $request->query('user_id');
        $estimations = Estimation::whereHas('project.client', function ($query) use ($userId){
            $query->where('user_id',$userId);
        })->with('project.client')->get();

        return response()->json($estimations);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title'=>[
                'required',
                'string',
                Rule::unique('estimations')->where('project_id', $request->project_id)
            ],
            'type'=>'required|in:fixed,hourly',
            'price'=>'required_if:type,fixed|nullable|numeric|min:0',
            'hours'=>'required_if:type,hourly|nullable|integer|min:1',
            'hourly_rate'=>'required_if:type,hourly|nullable|numeric|min:0',
            'project_id'=>'required|exists:projects,id'
        ]);

        if($validated['type'] === 'hourly'){
            $validated['price'] = $validated['hours'] * $validated['hourly_rate'];
        }

        Estimation::create($validated);
        return response()->json(['message'=>'Estimation saved']);
    }

    public function update(Request $request, $id){
        $estimation = Estimation::findOrFail($id);
        $validated = $request->validate([
            'title'=>[
                'required',
                'string',
                Rule::unique('estimations')
                ->where('project_id', $request->project_id)
                ->ignore($estimation->id)
            ],
            'type'=>'required|in:fixed,hourly',
            'price' => 'required_if:type,fixed|nullable|numeric|min:0',
            'hours' => 'required_if:type,hourly|nullable|integer|min:1',
            'hourly_rate' => 'required_if:type,hourly|nullable|numeric|min:0',
            'project_id'=>'required|exists:projects,id'
        ]);

        if ($validated['type'] === 'hourly') {
            $validated['price'] = $validated['hours'] * $validated['hourly_rate'];
        }

        $estimation->update($validated);
        return response()->json(['message'=>"Estimation updated"]);

    }

    public function destroy($id){
            $estimation = Estimation::findOrFail($id);
            $estimation->delete();
            return response()->json(['message'=>'Estimation deleted']);
            
        }
}
