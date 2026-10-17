<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardStudentsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Student::with(['classroom', 'attendances']);

        if ($this->user->role !== 'admin') {
            $query->whereHas('classroom', function ($q) {
                $q->where('teacher_id', $this->user->id);
            });
        }

        return $query->orderBy('name')->get()->map(function ($student) {
            $present = $student->attendances->where('status', 'present')->count();
            $late = $student->attendances->where('status', 'late')->count();
            $absent = $student->attendances->where('status', 'absent')->count();
            $total = $present + $late + $absent;
            $rate = $total > 0 ? round(($present / $total) * 100) : 0;

            return [
                'name' => $student->name,
                'student_number' => $student->student_number,
                'class' => $student->classroom?->name ?? 'Unassigned',
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $rate . '%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Student Number',
            'Classroom',
            'Total Present',
            'Total Late',
            'Total Absent',
            'Attendance Rate',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F172A'],
                ],
            ],
        ];
    }
}
