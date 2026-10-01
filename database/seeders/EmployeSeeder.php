<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employe;

class EmployeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listEmp = new ListEmp();
        $list = $listEmp->employes;

        foreach ($list as $emp) {
            Employe::create($emp);
        }
    }
}
