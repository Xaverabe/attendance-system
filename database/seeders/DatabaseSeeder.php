<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@attendance.test'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@attendance.test'],
            [
                'name' => 'Teacher One',
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]
        );

        $classroomA = ClassRoom::firstOrCreate([
            'name' => 'Grade 10 - Science',
            'teacher_id' => $teacher->id,
        ]);

        $classroomB = ClassRoom::firstOrCreate([
            'name' => 'Grade 11 - Math',
            'teacher_id' => $teacher->id,
        ]);

        $studentsA = [
            ['name' => 'Alice Johnson', 'student_number' => 'S-1001'],
            ['name' => 'Bob Smith', 'student_number' => 'S-1002'],
            ['name' => 'Charlie Lee', 'student_number' => 'S-1003'],
            ['name' => 'Diana Cruz', 'student_number' => 'S-1004'],
        ];

        foreach ($studentsA as $studentData) {
            $student = Student::firstOrCreate(
                ['student_number' => $studentData['student_number']],
                [
                    'name' => $studentData['name'],
                    'class_room_id' => $classroomA->id,
                ]
            );

            foreach (['2026-09-20', '2026-09-21', '2026-09-22'] as $date) {
                Attendance::firstOrCreate([
                    'student_id' => $student->id,
                    'class_room_id' => $classroomA->id,
                    'date' => $date,
                ], [
                    'status' => ['present', 'late', 'absent'][rand(0, 2)],
                ]);
            }
        }

        $studentsB = [
            ['name' => 'Eva Brown', 'student_number' => 'S-2001'],
            ['name' => 'Frank Wilson', 'student_number' => 'S-2002'],
            ['name' => 'Grace Hall', 'student_number' => 'S-2003'],
        ];

        foreach ($studentsB as $studentData) {
            $student = Student::firstOrCreate(
                ['student_number' => $studentData['student_number']],
                [
                    'name' => $studentData['name'],
                    'class_room_id' => $classroomB->id,
                ]
            );

            foreach (['2026-09-20', '2026-09-21', '2026-09-22'] as $date) {
                Attendance::firstOrCreate([
                    'student_id' => $student->id,
                    'class_room_id' => $classroomB->id,
                    'date' => $date,
                ], [
                    'status' => ['present', 'late', 'absent'][rand(0, 2)],
                ]);
            }
        }
    }
}
