<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    protected $classroom;
    protected $targetDate;
    protected $importedCount = 0;
    protected $errors = [];

    public function __construct(ClassRoom $classroom, ?string $targetDate = null)
    {
        $this->classroom = $classroom;
        $this->targetDate = $targetDate;
    }

    public function collection(Collection $rows)
    {
        $validStatuses = ['present', 'late', 'absent'];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $studentId = $row['student_id_do_not_edit'] ?? $row['student_id'] ?? null;
            $studentNumber = $row['student_number'] ?? null;
            $studentName = $row['student_name'] ?? null;

            if (empty($studentId) && empty($studentNumber) && empty($studentName)) {
                continue;
            }

            $student = null;
            if ($studentId) {
                $student = Student::where('id', $studentId)
                    ->where('class_room_id', $this->classroom->id)
                    ->first();
            }

            if (!$student && $studentNumber) {
                $student = Student::where('student_number', trim($studentNumber))
                    ->where('class_room_id', $this->classroom->id)
                    ->first();
            }

            if (!$student) {
                $this->errors[] = "Row {$rowNumber}: Student '{$studentName}' (Number: '{$studentNumber}') not found in {$this->classroom->name}.";
                continue;
            }

            $rawDate = $row['date_yyyy_mm_dd'] ?? $row['date'] ?? $this->targetDate;
            $attendanceDate = null;

            if (is_numeric($rawDate)) {
                $attendanceDate = Carbon::instance(ExcelDate::excelToDateTimeObject($rawDate))->toDateString();
            } elseif (!empty($rawDate)) {
                try {
                    $attendanceDate = Carbon::parse($rawDate)->toDateString();
                } catch (\Exception $e) {
                    $attendanceDate = $this->targetDate ?? now()->toDateString();
                }
            } else {
                $attendanceDate = $this->targetDate ?? now()->toDateString();
            }

            $rawStatus = strtolower(trim((string)($row['status_present_late_absent'] ?? $row['status'] ?? 'present')));
            if (!in_array($rawStatus, $validStatuses)) {
                $this->errors[] = "Row {$rowNumber} ({$student->name}): Invalid status '{$rawStatus}'. Must be present, late, or absent.";
                continue;
            }

            $notes = $row['notes_optional_remarks'] ?? $row['notes'] ?? null;

            Attendance::updateOrCreate(
                [
                    'class_room_id' => $this->classroom->id,
                    'student_id' => $student->id,
                    'date' => $attendanceDate,
                ],
                [
                    'status' => $rawStatus,
                    'notes' => !empty($notes) ? trim($notes) : null,
                ]
            );

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
