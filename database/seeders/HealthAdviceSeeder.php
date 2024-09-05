<?php

namespace Database\Seeders;

use App\Models\HealthAdvice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthAdviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('health_advice')->delete();

      $data = [
        ['initials' => 'CFBM', 'name'  => "Conselhor Federal de Biomedicina"],
        ['initials' => 'CRBM', 'name'  => "Conselho Regional de Biomedicina"],
        ['initials' => 'COFEN', 'name'  => "Conselho Federal de Enfermagem"],
        ['initials' => 'COREN', 'name'  => "Conselho Regional de Enfermagem"],
        ['initials' => 'CFF', 'name'  => "Conselho Federal de Farmácia"],
        ['initials' => 'COFFITO', 'name'  => "Conselho Federal de Fisioterapia e Terapia Ocupacional"],
        ['initials' => 'CREFITO', 'name'  => "Conselho Regional de Fisioterapia e Terapia Ocupacional"],
        ['initials' => 'CREFONO', 'name'  => "Conselho Regional de Fonoaudiologia"],
        ['initials' => 'CFFa', 'name'  => "Conselho Federal de Fonoaudiologia"],
        ['initials' => 'CFM', 'name'  => "Conselho Federal de Medicina"],
        ['initials' => 'CRM', 'name'  => "Conselho Regional de Medicina"],
        ['initials' => 'CFMV', 'name'  => "Conselho Federal de Medicina Veterinária"],
        ['initials' => 'CRMV', 'name'  => "Conselho Regional de Medicina Veterinária"],
        ['initials' => 'CFO', 'name'  => "Conselho Federal de Odontologia"],
        ['initials' => 'CRO', 'name'  => "Conselho Regional de Odontologia"],
        ['initials' => 'CFP', 'name'  => "Conselho Federal de Psicologia"],
        ['initials' => 'CRP', 'name'  => "Conselho Regional de Psicologia"],
        ['initials' => 'CFQ', 'name'  => "Conselho Federal de Química"],
        ['initials' => 'CRQ', 'name'  => "Conselho Regional de Química"],
        ['initials' => 'CONTER', 'name'  => "Conselho Nacional de Técnicos em Radiologia"],
        ['initials' => 'CRTR', 'name'  => "Conselho Regional de Técnicos em Radiologia"],      
      ];

      foreach ($data as $item) {
        HealthAdvice::create($item);
      }
    }
}
