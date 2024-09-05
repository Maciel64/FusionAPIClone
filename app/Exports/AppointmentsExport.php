<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Services\AppointmentService;
use \Illuminate\Support\Collection;
use Illuminate\Support\Carbon;


class AppointmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
     
        $service = new AppointmentService();        
        $appointments = $service->listAll(request());   
        $data = [];

        foreach($appointments['appointments'] as $appointment){
            $formatted_time_init = Carbon::parse($appointment->time_init)->format('d/m/Y H:i:s');
            $formatted_time_end = Carbon::parse($appointment->time_end)->format('d/m/Y H:i:s');
            $formatted_created_at = Carbon::parse($appointment->created_at)->format('d/m/Y H:i:s');
            $formatted_updated_at = Carbon::parse($appointment->updated_at)->format('d/m/Y H:i:s');

            $row = [];
            $row[] = $formatted_time_init;
            $row[] = $formatted_time_end;
            $row[] = $appointment->time_total;
            $row[] = $appointment->status;
            $row[] = $appointment->value_total;
            $row[] = $formatted_created_at;
            $row[] = $formatted_updated_at;
            $row[] = $appointment->specialist->name;
            $row[] = $appointment->specialist->email;
            $row[] = $appointment->specialist->document_type;
            $row[] = $appointment->specialist->document;
            $row[] = $appointment->specialist->contacts[0]->area_code;
            $row[] = $appointment->specialist->contacts[0]->number;
            $row[] = $appointment->room->name;
            $row[] = $appointment->room->number;
            $row[] = $appointment->room->coworking_name;
            $row[] = $appointment->room->partner_name;

            $data[] = $row;
        }
        return new Collection($data);

    }

    public function headings(): array
    {
        return [
            'Data de início' , 
            'Data de fim', 
            'Tempo total',
            'Status',
            'Valor',
            'Criado em',
            'Atualizado em',
            'Nome do especialista',
            'Email do especialista',
            'Tipo de documento',
            'Número do documento',
            'DDD',
            'Número do especialista',
            'Nome do quarto',
            'Número do quarto',
            'Nome da clínica',
            'Nome do parceiro',
         ];
    }
}