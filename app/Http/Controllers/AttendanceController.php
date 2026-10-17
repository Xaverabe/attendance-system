<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function welcome(Request $request)
    {
        $user = auth()->user();
        $todayDate = now()->toDateString();
        $formattedDate = now()->format('l, d F Y');

        $query = ClassRoom::query()->with([
            'students' => function ($q) {
                $q->orderBy('name');
            },
            'students.attendances' => function ($q) use ($todayDate) {
                $q->where('date', $todayDate);
            }
        ]);

        if ($user && $user->role !== 'admin') {
            $query->where('teacher_id', $user->id);
        }

        $classes = $query->get();
        $selectedClassId = $request->query('saved_class', $classes->first()?->id);

        return view('welcome', compact('classes', 'todayDate', 'formattedDate', 'user', 'selectedClassId'));
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,late,absent'],
            'notes' => ['nullable', 'array'],
        ]);

        $classroom = ClassRoom::findOrFail($request->class_room_id);

        if (auth()->check()) {
            $this->authorizeClassAccess($classroom);
        } else {
            return redirect()->route('login')->with('error', 'Please log in as a teacher to save attendance.');
        }

        foreach ($request->input('attendance', []) as $studentId => $status) {
            $student = Student::findOrFail($studentId);

            Attendance::updateOrCreate(
                [
                    'class_room_id' => $classroom->id,
                    'student_id' => $student->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $status,
                    'notes' => $request->input('notes.' . $studentId),
                ]
            );
        }

        return redirect('/?saved_class=' . $classroom->id)->with('success', "Attendance for {$classroom->name} on {$request->date} has been saved successfully.");
    }

    public function index()
    {
        $user = auth()->user();

        $query = ClassRoom::query()->with('students');

        if ($user->role !== 'admin') {
            $query->where('teacher_id', $user->id);
        }

        $classes = $query->get();

        return view('attendance.index', compact('classes'));
    }

    public function createForClass(ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $students = $classroom->students()->orderBy('name')->get();
        $date = now()->toDateString();

        return view('attendance.create', compact('classroom', 'students', 'date'));
    }

    public function store(Request $request, ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,late,absent'],
        ]);

        foreach ($request->input('attendance', []) as $studentId => $status) {
            $student = Student::findOrFail($studentId);

            Attendance::updateOrCreate(
                [
                    'class_room_id' => $classroom->id,
                    'student_id' => $student->id,
                    'date' => $request->date,
                ],
                [
                    'status' => $status,
                    'notes' => $request->input('notes.' . $studentId),
                ]
            );
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
    }

    public function show(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $student->load(['classroom', 'attendances' => function ($query) {
            $query->orderBy('date', 'desc');
        }]);

        return view('attendance.student', compact('student'));
    }

    private function authorizeClassAccess(ClassRoom $classroom)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->user()->id === $classroom->teacher_id, 403);
    }

    private function authorizeStudentAccess(Student $student)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->user()->id === $student->classroom->teacher_id, 403);
    }
}
