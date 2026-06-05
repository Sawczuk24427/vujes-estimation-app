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
}
