<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttendanceSystemTest extends TestCase
{
    protected $admin;
    protected $teacher;
    protected $classroom;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first();
        $this->teacher = User::where('role', 'teacher')->first();
        
        $this->classroom = ClassRoom::where('teacher_id', $this->teacher->id)->first() 
            ?? ClassRoom::first();
        
        $this->student = $this->classroom->students()->first() 
            ?? Student::where('class_room_id', $this->classroom->id)->first();
    }

    /** 1. Test Welcome / Landing Page */
    public function test_welcome_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Teacher Input');
        $response->assertSee('Face Recognition Scanner');
    }

    /** 2. Test Quick Attendance Store from Welcome Page */
    public function test_quick_attendance_store_as_teacher()
    {
        $today = now()->toDateString();
        $response = $this->actingAs($this->teacher)->post('/attendance/quick-store', [
            'class_room_id' => $this->classroom->id,
            'date' => $today,
            'attendance' => [
                $this->student->id => 'present',
            ],
            'notes' => [
                $this->student->id => 'Tested via automated test',
            ],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('attendances', [
            'class_room_id' => $this->classroom->id,
            'student_id' => $this->student->id,
            'date' => $today,
            'status' => 'present',
        ]);
    }

    /** 3. Test Dashboard and Sorting components */
    public function test_dashboard_loads_with_metrics_and_sortable_table()
    {
        $response = $this->actingAs($this->teacher)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Student Monitoring Directory');
        $response->assertSee('Sort by:');
        $response->assertSee('sortTable(');
        $response->assertSee('Export Table');
    }

    /** 4. Test Class Management CRUD routes */
    public function test_classes_index_page()
    {
        $response = $this->actingAs($this->teacher)->get('/classes');
        $response->assertStatus(200);
        $response->assertSee($this->classroom->name);
        $response->assertSee('Add New Class');
        $response->assertSee('Export Classes');
    }

    public function test_classes_create_page()
    {
        $response = $this->actingAs($this->teacher)->get('/classes/create');
        $response->assertStatus(200);
        $response->assertSee('Create Class');
    }

    public function test_classes_edit_page()
    {
        $response = $this->actingAs($this->teacher)->get("/classes/{$this->classroom->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Class');
    }

    /** 5. Test Student Management CRUD routes */
    public function test_students_index_page()
    {
        $response = $this->actingAs($this->teacher)->get('/students');
        $response->assertStatus(200);
        $response->assertSee('Enrolled Students');
        $response->assertSee('Export Students');
    }

    public function test_students_create_page()
    {
        $response = $this->actingAs($this->teacher)->get('/students/create');
        $response->assertStatus(200);
        $response->assertSee('Enroll Student');
    }

    public function test_students_edit_page()
    {
        $response = $this->actingAs($this->teacher)->get("/students/{$this->student->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Student Details');
    }

    /** 6. Test Attendance History Hub */
    public function test_attendance_index_hub()
    {
        $response = $this->actingAs($this->teacher)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('Class Attendance Hub');
        $response->assertSee('Import Paper Attendance (Excel)');
        $response->assertSee('Export Attendance Logs');
    }

    public function test_individual_student_attendance_view()
    {
        $response = $this->actingAs($this->teacher)->get("/attendance/student/{$this->student->id}");
        $response->assertStatus(200);
        $response->assertSee($this->student->name);
        $response->assertSee('Export Record');
    }

    /** 7. Test Excel Template Download */
    public function test_excel_template_download()
    {
        $date = '2026-10-17';
        $response = $this->actingAs($this->teacher)->get("/attendance/template/{$this->classroom->id}?date={$date}");
        
        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    /** 8. Test Excel Bulk Import Form View */
    public function test_excel_import_form_loads()
    {
        $response = $this->actingAs($this->teacher)->get('/attendance/import');
        $response->assertStatus(200);
        $response->assertSee('Excel Attendance Import');
        $response->assertSee('Download Template');
        $response->assertSee('Upload Filled Excel');
    }

    /** 9. Test CSV/Excel File Attendance Import Execution */
    public function test_attendance_file_import_process()
    {
        $testDate = '2026-10-18';
        $csvContent = "Student ID (Do not edit),Student Name,Student Number,Date (YYYY-MM-DD),Status (present / late / absent),Notes (Optional remarks)\n";
        $csvContent .= "{$this->student->id},\"{$this->student->name}\",{$this->student->student_number},{$testDate},late,\"Arrived 10 min late\"\n";

        $file = UploadedFile::fake()->createWithContent('attendance_import_test.csv', $csvContent);

        $response = $this->actingAs($this->teacher)->post('/attendance/import', [
            'class_room_id' => $this->classroom->id,
            'date' => $testDate,
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'class_room_id' => $this->classroom->id,
            'student_id' => $this->student->id,
            'date' => $testDate,
            'status' => 'late',
            'notes' => 'Arrived 10 min late',
        ]);
    }

    /** 10. Test All Export Routes (Excel & CSV) */
    public function test_export_dashboard_students_xlsx()
    {
        $response = $this->actingAs($this->teacher)->get('/export/dashboard-students');
        $response->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_export_dashboard_students_csv()
    {
        $response = $this->actingAs($this->teacher)->get('/export/dashboard-students?format=csv');
        $response->assertStatus(200);
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
    }

    public function test_export_class_overview_xlsx()
    {
        $response = $this->actingAs($this->teacher)->get('/export/class-overview');
        $response->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_export_students_xlsx()
    {
        $response = $this->actingAs($this->teacher)->get('/export/students');
        $response->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_export_attendance_history_xlsx()
    {
        $response = $this->actingAs($this->teacher)->get('/export/attendance');
        $response->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_export_single_student_attendance_xlsx()
    {
        $response = $this->actingAs($this->teacher)->get("/export/attendance/student/{$this->student->id}");
        $response->assertStatus(200);
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }
}

