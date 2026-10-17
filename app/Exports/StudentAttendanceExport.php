<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentAttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function collection()
    {
        return $this->student->attendances()->orderBy('date', 'desc')->get()->map(function ($attendance) {
            return [
                'date' => $attendance->date,
                'student_name' => $this->student->name,
                'student_number' => $this->student->student_number,
                'classroom' => $this->student->classroom?->name ?? 'Unassigned',
                'status' => ucfirst($attendance->status),
                'notes' => $attendance->notes ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Student Number',
            'Classroom',
            'Status',
            'Notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E293B'],
                ],
            ],
        ];
    }
}

