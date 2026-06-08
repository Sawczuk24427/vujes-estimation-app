<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            
            'name' => [
                'required',
                'string',
                Rule::unique('clients')->where('user_id', $request->user_id)
            ],
        ]);

        $client = Client::create($validated);

        return response()->json(['message' => 'Client saved successfully!', 'client' => $client], 201);
    }

    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        $clients = Client::where('user_id', $user_id)->get();
        return response() -> json($clients);
    }

    public function update(Request $request, $id){
        $client = Client::findOrFail($id);
        $validated = $request->validate([
            'name'=> [
                'required',
                'string',
                Rule::unique('clients')->where('user_id', $client->user_id)->ignore($client->id)
            ],
            'email'=>'nullable|email',
            'phone'=>'nullable|string'
        ]);
        $client->update($validated);
        return response()->json(['message'=>'Client Updated']);
    }

    public function destroy($id){
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client Deleted']);
    }
}
