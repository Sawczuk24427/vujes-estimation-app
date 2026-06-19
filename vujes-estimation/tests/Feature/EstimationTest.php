<?php

use App\Models\Client;
use App\Models\Estimation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create an hourly estimation and calculate total price', function () {

    $project = Project::factory()->create();

    $payload = [
        'project_id' => $project->id,
        'title' => 'Sklep Internetowy',
        'type' => 'hourly',
        'hours' => 20,
        'hourly_rate' => 100,
        'project_id' => 1,
    ];

    $response = $this->postJson('/api/estimations', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('estimations', [
        'project_id' => $project->id,
        'title' => 'Sklep Internetowy',
        'type' => 'hourly',
        'price' => 2000,
    ]);

});

it('requires valid data to create estimation', function () {

    $response = $this->postJson('/api/estimations', []);

    $response->assertStatus(422); 

    $response->assertJsonValidationErrors(['title', 'project_id', 'type']);
});

it('can delete own estimation', function () {
    
    $me = User::factory()->create();

    
    $client = Client::factory()->create(['user_id' => $me->id]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $estimation = Estimation::factory()->create(['project_id' => $project->id]);

    
    $this->actingAs($me);

    
    $response = $this->deleteJson('/api/estimations/'.$estimation->id);

    
    $response->assertStatus(200);
});

it('cannot delete an estimation belonging to another user', function () {
    
    $hacker = User::factory()->create();
    $victim = User::factory()->create();

    
    $client = Client::factory()->create(['user_id' => $victim->id]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $estimation = Estimation::factory()->create(['project_id' => $project->id]);

    
    $this->actingAs($hacker);

    
    $response = $this->deleteJson('/api/estimations/'.$estimation->id);

    
    $response->assertStatus(403);

    
    $this->assertDatabaseHas('estimations', ['id' => $estimation->id]);
});

it('rejects hourly variables when type is fixed', function () {
    $project = Project::factory()->create();

    $payload = [
        'project_id' => $project->id,
        'name' => 'Próba Oszustwa',
        'type' => 'fixed',
        'price' => 5000,
        'hours' => 10,
        'hourly_rate' => 100,
    ];

    $response = $this->postJson('/api/estimations', $payload);


    $response->assertStatus(422);
});
