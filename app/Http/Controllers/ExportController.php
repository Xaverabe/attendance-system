<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\ClassOverviewExport;
use App\Exports\DashboardStudentsExport;
use App\Exports\StudentAttendanceExport;
use App\Exports\StudentsExport;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function dashboardStudents(Request $request)
    {
        $user = auth()->user();
        $format = $request->query('format', 'xlsx');
        $date = now()->format('Y-m-d');
        $filename = "Student_Monitoring_Report_{$date}." . ($format === 'csv' ? 'csv' : 'xlsx');
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new DashboardStudentsExport($user), $filename, $writerType);
    }

    public function classOverview(Request $request)
    {
        $user = auth()->user();
        $format = $request->query('format', 'xlsx');
        $date = now()->format('Y-m-d');
        $filename = "Class_Overview_Summary_{$date}." . ($format === 'csv' ? 'csv' : 'xlsx');
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new ClassOverviewExport($user), $filename, $writerType);
    }

    public function students(Request $request)
    {
        $user = auth()->user();
        $format = $request->query('format', 'xlsx');
        $date = now()->format('Y-m-d');
        $filename = "Enrolled_Students_Directory_{$date}." . ($format === 'csv' ? 'csv' : 'xlsx');
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new StudentsExport($user), $filename, $writerType);
    }

    public function attendance(Request $request)
    {
        $user = auth()->user();
        $format = $request->query('format', 'xlsx');
        $classId = $request->query('class_id');
        $dateFilter = $request->query('date');
        $date = now()->format('Y-m-d');
        $filename = "Attendance_History_Log_{$date}." . ($format === 'csv' ? 'csv' : 'xlsx');
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new AttendanceExport($user, $classId, $dateFilter), $filename, $writerType);
    }

    public function studentAttendance(Request $request, Student $student)
    {
        $this->authorizeStudentAccess($student);

        $format = $request->query('format', 'xlsx');
        $cleanName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);
        $date = now()->format('Y-m-d');
        $filename = "Attendance_Record_{$cleanName}_{$date}." . ($format === 'csv' ? 'csv' : 'xlsx');
        $writerType = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new StudentAttendanceExport($student), $filename, $writerType);
    }

    private function authorizeStudentAccess(Student $student)
    {
        if (auth()->user()->role === 'admin') {
            return;
        }

        abort_unless(auth()->user()->id === $student->classroom?->teacher_id, 403);
    }
}

