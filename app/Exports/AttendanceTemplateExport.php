<?php

namespace App\Exports;

use App\Models\ClassRoom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $classroom;
    protected $date;

    public function __construct(ClassRoom $classroom, string $date)
    {
        $this->classroom = $classroom;
        $this->date = $date;
    }

    public function collection()
    {
        $students = $this->classroom->students()->orderBy('name')->get();

        return $students->map(function ($student) {
            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_number' => $student->student_number,
                'date' => $this->date,
                'status' => 'present',
                'notes' => '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student ID (Do not edit)',
            'Student Name',
            'Student Number',
            'Date (YYYY-MM-DD)',
            'Status (present / late / absent)',
            'Notes (Optional remarks)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'],
                ],
            ],
        ];
    }
}

