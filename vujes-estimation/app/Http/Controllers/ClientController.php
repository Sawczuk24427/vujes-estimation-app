<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    public function store(StoreClientRequest $request)
{
    $validated = $request->validated();

    $validated['user_id'] = auth()->id();

    $client = Client::create($validated);

    return new ClientResource($client);
}

    public function index(Request $request)
    {
        $clients = Client::where('user_id', auth()->id())->get();

        return ClientResource::collection($clients);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('update', $client);
        $client->update($request->validated());

        return new ClientResource($client);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('delete', $client);
        $client->delete();

        return response()->json(['message' => 'Client Deleted'], 200);
    }
}
