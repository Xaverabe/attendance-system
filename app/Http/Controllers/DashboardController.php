<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $classes = ClassRoom::withCount('students')->get();
        } else {
            $classes = ClassRoom::where('teacher_id', $user->id)->withCount('students')->get();
        }

        $attendanceSummary = $this->buildClassAttendanceSummary($user);
        $studentRows = $this->buildStudentMonitoring($user);
        $classOverview = $this->buildClassOverview($user);

        return view('dashboard', compact('classes', 'attendanceSummary', 'studentRows', 'classOverview'));
    }

    private function buildClassAttendanceSummary($user)
    {
        $query = Attendance::query()->select('status', DB::raw('COUNT(*) as total'))
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->where('class_room_id', function ($sub) use ($user) {
                    return $sub->select('id')->from('class_rooms')->where('teacher_id', $user->id);
                });
            });

        return $query->groupBy('status')->get()->keyBy('status')->map(fn ($row) => $row->total)->toArray();
    }

    private function buildClassOverview($user)
    {
        $query = ClassRoom::with(['students.attendances' => function ($query) {
            $query->where('status', '!=', 'present');
        }]);

        if ($user->role !== 'admin') {
            $query->where('teacher_id', $user->id);
        }

        $rows = $query->get()->map(function ($classroom) {
            $late = 0;
            $absent = 0;

            foreach ($classroom->students as $student) {
                foreach ($student->attendances as $attendance) {
                    if ($attendance->status === 'late') {
                        $late++;
                    }

                    if ($attendance->status === 'absent') {
                        $absent++;
                    }
                }
            }

            return [
                'name' => $classroom->name,
                'late' => $late,
                'absent' => $absent,
                'total_students' => $classroom->students->count(),
            ];
        });

        return $rows;
    }

    private function buildStudentMonitoring($user)
    {
        $query = Student::with(['classroom', 'attendances']);

        if ($user->role !== 'admin') {
            $query->whereHas('classroom', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        return $query->get()->map(function ($student) {
            $present = $student->attendances->where('status', 'present')->count();
            $late = $student->attendances->where('status', 'late')->count();
            $absent = $student->attendances->where('status', 'absent')->count();

            return [
                'name' => $student->name,
                'student_number' => $student->student_number,
                'class' => $student->classroom?->name,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'attendance_rate' => $student->attendances->count() ? round(($present / $student->attendances->count()) * 100, 1) : 0,
            ];
        });
    }
}
