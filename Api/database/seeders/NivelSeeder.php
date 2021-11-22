<?php

namespace Database\Seeders;

use App\Models\Nivel;
use Illuminate\Database\Seeder;

class NivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(["aluno", "professor"] as $name) {
            Nivel::create([
                "name" => $name
            ]);
        }
    }
}
