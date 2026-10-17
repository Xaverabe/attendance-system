<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $user;
    protected $classId;
    protected $date;

    public function __construct($user, $classId = null, $date = null)
    {
        $this->user = $user;
        $this->classId = $classId;
        $this->date = $date;
    }

    public function collection()
    {
        $query = Attendance::with(['student', 'classroom']);

        if ($this->user->role !== 'admin') {
            $query->whereHas('classroom', function ($q) {
                $q->where('teacher_id', $this->user->id);
            });
        }

        if ($this->classId) {
            $query->where('class_room_id', $this->classId);
        }

        if ($this->date) {
            $query->where('date', $this->date);
        }

        return $query->orderBy('date', 'desc')->get()->map(function ($attendance) {
            return [
                'date' => $attendance->date,
                'student_name' => $attendance->student?->name ?? 'Unknown',
                'student_number' => $attendance->student?->student_number ?? '-',
                'classroom' => $attendance->classroom?->name ?? '-',
                'status' => strtoupper($attendance->status),
                'notes' => $attendance->notes ?? '-',
                'logged_at' => $attendance->created_at ? $attendance->created_at->format('Y-m-d H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Attendance Date',
            'Student Name',
            'Student Number',
            'Classroom',
            'Status',
            'Notes / Remarks',
            'Logged Timestamp',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF10B981'],
                ],
            ],
        ];
    }
}

