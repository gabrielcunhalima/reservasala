<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('salas')->insert([
            [
                'nomeSala' => 'Sala 1',
                'capacidade' => 40,
                'descricao' => '- 40 Cadeiras de Braço Orelha escamoteável.<br>- 1 Quadro Branco 120X150 cm.<br>- 1 Mesa com 2 gavetas.<br>- 1 Cavalete para Flip Chart/Banner.<br>- 1 Tela de Transparência.<br>- 1 Projetor + Controle.<br>- 1 Condicionador de Ar Split + controle.',
                'localizacao' => 'FAPEU - SEDE',
                'imagem' => 'sala01_p',
                'ativo' => 1,
                'valorMeioPeriodo' => 300.00,
                'valorIntegral' => 480.00,
                'taxaLimpeza' => 70.00
            ],
            [
                'nomeSala' => 'Sala 2',
                'capacidade' => 40,
                'descricao' => '- 40 Cadeiras de Braço Orelha escamoteável.<br>- 2 Quadros de vidro.<br>- 1 Mesas com 2 gavetas.<br>- 1 Balcão de Apoio, 02 portas.<br>- 1 Cavalete para Flip Chart/Banner.<br>- 1 Tela de Transparência.<br>- 1 Projetor + Controle.<br>- 2 Caixas de som<br>- 2 Condicionadores de Ar Split + controle.',
                'localizacao' => 'FAPEU - SEDE',
                'imagem' => 'sala02_p',
                'ativo' => 1,
                'valorMeioPeriodo' => 300.00,
                'valorIntegral' => 480.00,
                'taxaLimpeza' => 70.00
            ],
            [
                'nomeSala' => 'Auditório',
                'capacidade' => 80,
                'descricao' => '- 80 Cadeiras de Braço Orelha escamoteável.<br>- 2 Quadros Branco.<br>- 2 Mesas com 2 gavetas.<br>- 1 Balcão de apoio para mesa de som, 2 portas.<br>- 2 Cavaletes para Flip Chart/Banner.<br>- 1 Painel de Transparência.<br>- 1 Projetor + controle.<br>- 2 Caixas de som.<br>- 1 Mesa de som<br>- 1 Microfone.<br>- 3 Condicionadores de Ar Split + controle.',
                'localizacao' => 'FAPEU - SEDE',
                'imagem' => 'auditorio_p',
                'ativo' => 1,
                'valorMeioPeriodo' => 500.00,
                'valorIntegral' => 800.00,
                'taxaLimpeza' => 70.00
            ],
        ]);
    }
}
