<?php

namespace Tests\Feature;

use App\Models\Anomalie;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarAnomalyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'name' => 'fleet-manager',
            'password' => 'secret',
            'service_sigle' => 'SNR',
        ]);

        $this->actingAs($user);
    }

    public function test_car_anomaly_can_be_opened_and_closed(): void
    {
        Car::create(['matricule' => 'BUS001']);

        $this->postJson('/cars/BUS001/anomalies', [
            'description' => 'mecanique',
        ])->assertCreated();

        $anomaly = Anomalie::firstOrFail();
        $this->assertDatabaseHas('cars', [
            'matricule' => 'BUS001',
            'disponibilite' => false,
        ]);

        $this->postJson('/cars/BUS001/anomalies', [
            'description' => 'technique',
        ])->assertStatus(409);

        $this->patchJson("/anomalies/{$anomaly->id}")->assertOk();

        $this->assertDatabaseHas('anomalies', [
            'id' => $anomaly->id,
            'fin' => today()->toDateString(),
        ]);
        $this->assertDatabaseHas('cars', [
            'matricule' => 'BUS001',
            'disponibilite' => true,
        ]);
    }
}
