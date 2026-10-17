<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Student::with('classroom');

        if ($user->role !== 'admin') {
            $query->whereHas('classroom', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }

        $students = $query->orderBy('name')->get();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $classes = ClassRoom::query()->when(auth()->user()->role !== 'admin', fn ($q) => $q->where('teacher_id', auth()->id()))->get();

        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_number' => ['required', 'string', 'max:255', 'unique:students'],
            'class_room_id' => ['required', 'exists:class_rooms,id'],
        ]);

        if (auth()->user()->role !== 'admin') {
            abort_unless(auth()->user()->id === ClassRoom::findOrFail($data['class_room_id'])->teacher_id, 403);
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $classes = ClassRoom::query()->when(auth()->user()->role !== 'admin', fn ($q) => $q->where('teacher_id', auth()->id()))->get();

        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorizeStudentAccess($student);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_number' => ['required', 'string', 'max:255', Rule::unique('students')->ignore($student->id)],
            'class_room_id' => ['required', 'exists:class_rooms,id'],
        ]);

        if (auth()->user()->role !== 'admin') {
            abort_unless(auth()->user()->id === ClassRoom::findOrFail($data['class_room_id'])->teacher_id, 403);
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', "Student '{$student->name}' updated successfully.");
    }

    public function destroy(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $name = $student->name;
        $student->delete();

        return redirect()->route('students.index')->with('success', "Student '{$name}' removed successfully.");
    }

    private function authorizeStudentAccess(Student $student)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->user()->id === $student->classroom?->teacher_id, 403);
    }
}
