<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceTemplateExport;
use App\Imports\AttendanceImport;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceImportController extends Controller
{
    public function showImportForm(Request $request)
    {
        $user = auth()->user();
        $query = ClassRoom::query()->with('students');

        if ($user->role !== 'admin') {
            $query->where('teacher_id', $user->id);
        }

        $classes = $query->get();
        $selectedClassId = $request->query('class_id', $classes->first()?->id);
        $selectedDate = $request->query('date', now()->toDateString());

        return view('attendance.import', compact('classes', 'selectedClassId', 'selectedDate'));
    }

    public function downloadTemplate(Request $request, ClassRoom $classroom)
    {
        $this->authorizeClassAccess($classroom);

        $date = $request->query('date', now()->toDateString());
        $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $classroom->name);
        $fileName = "Attendance_Template_{$cleanName}_{$date}.xlsx";

        return Excel::download(new AttendanceTemplateExport($classroom, $date), $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'class_room_id' => ['required', 'exists:class_rooms,id'],
            'date' => ['required', 'date'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $classroom = ClassRoom::findOrFail($request->class_room_id);
        $this->authorizeClassAccess($classroom);

        $import = new AttendanceImport($classroom, $request->date);

        try {
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();
            $errors = $import->getErrors();

            if (count($errors) > 0) {
                return redirect()->back()
                    ->with('warning', "Import completed with warnings: {$count} student record(s) imported/updated.")
                    ->with('import_errors', $errors);
            }

            return redirect()->route('attendance.index')
                ->with('success', "Successfully imported {$count} attendance record(s) for {$classroom->name} on {$request->date}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process Excel file: ' . $e->getMessage());
        }
    }

    private function authorizeClassAccess(ClassRoom $classroom)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->id() === $classroom->teacher_id, 403);
    }
}

