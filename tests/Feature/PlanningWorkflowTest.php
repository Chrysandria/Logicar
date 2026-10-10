<?php

namespace Tests\Feature;

use App\Models\Employe;
use App\Models\Planning;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create([
            'name' => 'planner',
            'password' => 'secret',
            'service_sigle' => 'OPS',
        ]);

        $this->actingAs($user);
    }

    public function test_planning_is_created_for_valid_employees(): void
    {
        Employe::create([
            'matricule' => 'EMP001',
            'nom' => 'Employé test',
            'telephone' => '0102030405',
            'service_sigle' => 'OPS',
            'lieu_ramassage' => 'Centre',
            'pnt_ramassage_dec' => 'Centre',
            'pnt_ramassage_adm' => 'Entrée',
        ]);

        $this->postJson('/plannings', [
            'date' => '2026-10-12',
            'hps' => '07:30',
            'employees' => [['matricule' => 'EMP001']],
        ])->assertCreated();

        $this->assertDatabaseHas('plannings', [
            'mtrl_emp' => 'EMP001',
            'date' => '2026-10-12',
            'HPS' => '07:30',
        ]);
    }

    public function test_invalid_employees_do_not_create_partial_plannings(): void
    {
        Employe::create([
            'matricule' => 'EMP001',
            'nom' => 'Employé test',
            'telephone' => '0102030405',
            'service_sigle' => 'SNR',
            'lieu_ramassage' => 'Centre',
            'pnt_ramassage_dec' => 'Centre',
            'pnt_ramassage_adm' => 'Entrée',
        ]);

        $this->postJson('/plannings', [
            'date' => '2026-10-12',
            'hps' => '07:30',
            'employees' => [
                ['matricule' => 'EMP001'],
                ['matricule' => 'EMP999'],
            ],
        ])->assertUnprocessable();

        $this->assertSame(0, Planning::count());
    }

}
