<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $query = ClassRoom::with(['teacher', 'students']);

        if (auth()->user()->role !== 'admin') {
            $query->where('teacher_id', auth()->id());
        }

        $classes = $query->get();

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = auth()->user()->role === 'admin'
            ? User::where('role', 'teacher')->orderBy('name')->get()
            : collect();

        return view('classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        if (auth()->user()->role === 'admin') {
            $rules['teacher_id'] = ['required', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        ClassRoom::create([
            'name' => $data['name'],
            'teacher_id' => auth()->user()->role === 'admin' ? $data['teacher_id'] : auth()->id(),
        ]);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function edit(ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $teachers = auth()->user()->role === 'admin'
            ? User::where('role', 'teacher')->orderBy('name')->get()
            : collect();

        return view('classes.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        if (auth()->user()->role === 'admin') {
            $rules['teacher_id'] = ['required', 'exists:users,id'];
        }

        $data = $request->validate($rules);

        $classroom->name = $data['name'];
        if (auth()->user()->role === 'admin' && isset($data['teacher_id'])) {
            $classroom->teacher_id = $data['teacher_id'];
        }
        $classroom->save();

        return redirect()->route('classes.index')->with('success', "Class '{$classroom->name}' updated successfully.");
    }

    public function destroy(ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $className = $classroom->name;
        $classroom->delete();

        return redirect()->route('classes.index')->with('success', "Class '{$className}' deleted successfully.");
    }

    private function authorizeClassAccess(ClassRoom $classroom)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->id() === $classroom->teacher_id, 403);
    }
}
