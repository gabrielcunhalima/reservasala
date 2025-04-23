<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurnosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('turnos')->insert([
            ['Descricao' => 'Manhã', 'Horario' => '08:00 às 12:00', 'Ativo' => 1],
            ['Descricao' => 'Tarde', 'Horario' => '13:00 às 17:00', 'Ativo' => 1],
            ['Descricao' => 'Dia Todo', 'Horario' => '08:00 às 17:00', 'Ativo' => 1],
        ]);
    }
}
