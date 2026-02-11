<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [
            ['nama_mesin' => 'Milling', 'tarif' => 120000],
            ['nama_mesin' => 'Bubut Kecil', 'tarif' => 120000],
            ['nama_mesin' => 'Bubut Besar', 'tarif' => 250000],
            ['nama_mesin' => 'Grinding', 'tarif' => 250000],
            ['nama_mesin' => 'Las', 'tarif' => 200000],
            ['nama_mesin' => 'Metal Spray', 'tarif' => 150000],
            ['nama_mesin' => 'Sand Blasting / Pengecatan', 'tarif' => 200000],
        ];

        foreach ($machines as $machine) {
            \App\Models\Machine::create($machine);
        }
    }
}
