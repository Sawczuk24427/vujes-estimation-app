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

    $response->assertStatus(200);

    $this->assertDatabaseHas('estimations', [
        'project_id' => $project->id,
        'title' => 'Sklep Internetowy',
        'type' => 'hourly',
        'price' => 2000,
    ]);

});

it('requires valid data to create estimation', function () {

    $response = $this->postJson('/api/estimations', []);

    $response->assertStatus(422); // 422 - błąd walidacji

    $response->assertJsonValidationErrors(['title', 'project_id', 'type']);
});

it('can delete own estimation', function () {
    // 1. Tworzymy NASZEGO użytkownika
    $me = User::factory()->create();

    // 2. Budujemy łańcuszek, WYMUSZAJĄC nasze ID w Kliencie!
    $client = Client::factory()->create(['user_id' => $me->id]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $estimation = Estimation::factory()->create(['project_id' => $project->id]);

    // 3. Logujemy się jako MY
    $this->actingAs($me);

    // 4. Próbujemy usunąć NASZĄ wycenę
    $response = $this->deleteJson('/api/estimations/'.$estimation->id);

    // 5. Powinno przejść z sukcesem!
    $response->assertStatus(200);
});

it('cannot delete an estimation belonging to another user', function () {
    // 1. Tworzymy dwóch różnych użytkowników
    $hacker = User::factory()->create();
    $victim = User::factory()->create();

    // 2. Tworzymy wycenę, która należy do ofiary (poprzez klienta i projekt)
    $client = Client::factory()->create(['user_id' => $victim->id]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $estimation = Estimation::factory()->create(['project_id' => $project->id]);

    // 3. Logujemy się do API jako "haker"
    $this->actingAs($hacker);

    // 4. Próbujemy usunąć cudzą wycenę
    $response = $this->deleteJson('/api/estimations/'.$estimation->id);

    // 5. Oczekujemy, że serwer da nam po łapkach (Błąd 403 - Forbidden)
    $response->assertStatus(403);

    // 6. Upewniamy się, że wycena ofiary nadal bezpiecznie leży w bazie
    $this->assertDatabaseHas('estimations', ['id' => $estimation->id]);
});

it('rejects hourly variables when type is fixed', function () {
    $project = Project::factory()->create();

    $payload = [
        'project_id' => $project->id,
        'name' => 'Próba Oszustwa',
        'type' => 'fixed',
        'price' => 5000,
        // Użytkownik próbuje przemycić dane dla godzinówki!
        'hours' => 10,
        'hourly_rate' => 100,
    ];

    $response = $this->postJson('/api/estimations', $payload);

    // Oczekujemy błędu walidacji
    $response->assertStatus(422);
    // (Opcjonalnie) Możesz sprawdzić, czy wypluło błąd dla konkretnego pola
});
