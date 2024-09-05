<?php

namespace App\Exports;

use App\Models\Appointment;
use App\Services\AppointmentService;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithBackgroundColor;
use \Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Traits\Helpers;


class FinanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{

    const FORMAT_ACCOUNTING_BRL = '_("R$"* #,##0.00_);_R$ * \(#,##0.00\);_("R$"* "-"??_);_(@_)';

    use Helpers;

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],            
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => FinanceExport::FORMAT_ACCOUNTING_BRL,
            'F' => FinanceExport::FORMAT_ACCOUNTING_BRL,
            'E' => NumberFormat::FORMAT_PERCENTAGE_00
        ];
    }


    public function collection()
    {

        $service = new AppointmentService();
        $appointments = $service->listAll(request());
        $data = [];
        $grossSum = 0;
        $netSum = 0;

        foreach ($appointments['appointments'] as $appointment) {
            $formatted_time_init = Carbon::parse($appointment->time_init)->format('d/m/Y H:i:s');
            $formatted_time_end = Carbon::parse($appointment->time_end)->format('d/m/Y H:i:s');
            $formatted_created_at = Carbon::parse($appointment->created_at)->format('d/m/Y H:i:s');
            $formatted_updated_at = Carbon::parse($appointment->updated_at)->format('d/m/Y H:i:s');

            $row = [];

            $row[] = $appointment->specialist->name;
            $row[] = $formatted_time_init;
            $row[] = 'Cartão de Crédito';
            $row[] = $appointment->value_total;
            $row[] = '4%';
            $row[] = $appointment->value_total * 0.96;
            $data[] = $row;
            $grossSum = $grossSum + $appointment->value_total;
            $netSum = $netSum + ($appointment->value_total * 0.96);
        }


        $row = [];

        $row[] = $this->convertDateToText(request()->dateInit, true) . ' até ' . $this->convertDateToText(request()->dateEnd, true);
        $row[] = '';
        $row[] = '';
        $row[] = $grossSum;
        $row[] = '';
        $row[] = $netSum;


        $data[] = $row;
        return new Collection($data);
    }



    public function headings(): array
    {
        return [
            'Profissional',
            'Data da Reserva',
            'Forma de Pagamento',
            'Valor Pago',
            'Taxa',
            'Valor Líquido',
        ];
    }

    public function backgroundColor(): string
    {

        return 'FFFFFF'; 
    }




}
