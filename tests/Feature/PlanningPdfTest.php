<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanningPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_snr_can_generate_a_branded_planning_pdf(): void
    {
        $user = User::create([
            'name' => 'fleet-manager',
            'password' => 'secret',
            'service_sigle' => 'SNR',
        ]);

        $response = $this->actingAs($user)->postJson('/plannings/pdf', [
            'date' => '2026-10-12',
            'hps' => '07:30',
            'data' => [[
                'ramassage' => [[
                    'HDR' => '07:45:00',
                    'trajet' => 'Centre-ville',
                ]],
                'emps' => [[
                    'matricule' => 'EMP001',
                    'nom' => 'Employé test',
                    'telephone' => '0102030405',
                    'lieu_ramassage' => 'Centre',
                    'pnt_ramassage_adm' => 'Entrée ADM',
                    'pnt_ramassage_dec' => 'Entrée décalée',
                ]],
            ]],
        ]);

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="planning_2026-10-12_07:30.pdf"');

        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('/Subtype /Image', $response->getContent());
    }
}
