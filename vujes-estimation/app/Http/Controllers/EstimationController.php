<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimattion;
use Illuminate\Validation\Rule;

class EstimationController extends Controller
{
    public function index(Request $request){
        $project_id = $request->query('project_id');
        $estimations = Estimation::whereHas('project.client', function ($query) use ($userId){
            $query->where('user_id',$userId);
        })->with('project')->get();

        return response()->json($estimations);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title'=>[
                'required',
                'string',
                Rule::unique('estimations')->where('project_id', $request->project_id)
            ],
            'price'=>'required|numeric|min:0',
            'project_id'=>'required|exists:projects,id'
        ]);

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
            'price'=>'required|numeric|min:0',
            'project_id'=>'required|exists:projects,id'
        ]);

        $estimation->update($validated);
        return response()->json(['message'=>"Estimation updated"]);

    }

    public function destroy($id){
            $estimation = Estimation::findOrFail($id);
            $estimation->delete();
            return response->json(['message'=>'Estimation deleted']);
            
        }
}
