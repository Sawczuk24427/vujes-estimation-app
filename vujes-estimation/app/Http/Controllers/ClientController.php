<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        $client = Client::create($validated);

        return response()->json(['message' => 'Client saved successfully!', 'client' => $client], 201);
    }

    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        $clients = Client::where('user_id', $user_id)->get();

        return response()->json($clients, 201);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('update', $client);
        $validated = $request->validated();
        $client->update($validated);

        return response()->json(['message' => 'Client Updated', 'client'=>$client], 201);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('delete', $client);
        $client->delete();

        return response()->json(['message' => 'Client Deleted'], 201);
    }
}
