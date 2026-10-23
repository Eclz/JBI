<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'admission_number',
            'admission_date',
            'department',
            'program',
            'status',
            'current_semester',
            'year_of_study',
            'phone',
            'date_of_birth',
            'gender',
            'address',
        ];
    }
}
