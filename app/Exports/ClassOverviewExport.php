<?php

namespace App\Exports;

use App\Models\ClassRoom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassOverviewExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = ClassRoom::with(['teacher', 'students.attendances']);

        if ($this->user->role !== 'admin') {
            $query->where('teacher_id', $this->user->id);
        }

        return $query->get()->map(function ($classroom) {
            $late = 0;
            $absent = 0;
            $present = 0;

            foreach ($classroom->students as $student) {
                foreach ($student->attendances as $attendance) {
                    if ($attendance->status === 'late') $late++;
                    if ($attendance->status === 'absent') $absent++;
                    if ($attendance->status === 'present') $present++;
                }
            }

            return [
                'name' => $classroom->name,
                'teacher' => $classroom->teacher?->name ?? 'Unassigned',
                'total_students' => $classroom->students->count(),
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Class Name',
            'Teacher',
            'Enrolled Students',
            'Total Present Records',
            'Total Late Records',
            'Total Absent Records',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'],
                ],
            ],
        ];
    }
}

