<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Student::with(['classroom.teacher']);

        if ($this->user->role !== 'admin') {
            $query->whereHas('classroom', function ($q) {
                $q->where('teacher_id', $this->user->id);
            });
        }

        return $query->orderBy('name')->get()->map(function ($student) {
            return [
                'name' => $student->name,
                'student_number' => $student->student_number,
                'classroom' => $student->classroom?->name ?? 'Unassigned',
                'teacher' => $student->classroom?->teacher?->name ?? 'Unassigned',
                'enrolled_at' => $student->created_at ? $student->created_at->format('Y-m-d H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Full Name',
            'Student Identification Number',
            'Classroom',
            'Supervising Teacher',
            'Registration Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0369A1'],
                ],
            ],
        ];
    }
}

