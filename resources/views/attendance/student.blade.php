@extends('layouts.app')

@section('title', 'Student Attendance Record | ' . $student->name)
@section('page-title', 'Student Attendance Record')
@section('page-subtitle', 'Detailed attendance log for ' . $student->name . ' (' . $student->student_number . ')')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            @php
                $initials = collect(explode(' ', $student->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
            @endphp
            <div class="user-avatar bg-primary bg-opacity-10 text-primary fs-5" style="width: 52px; height: 52px;">
                {{ $initials }}
            </div>
            <div>
                <h4 class="mb-0 fw-bold">{{ $student->name }}</h4>
                <div class="text-muted small">
                    <span class="badge bg-light text-secondary border font-monospace">{{ $student->student_number }}</span>
                    &nbsp;·&nbsp;
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-mortarboard me-1"></i>{{ $student->classroom?->name ?? 'Unassigned' }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Export Single Student Record -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i>
                    <span>Export Record</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="{{ route('export.attendance.student', $student) }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Download Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('export.attendance.student', [$student, 'format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Download CSV (.csv)</a></li>
                </ul>
            </div>

            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Historical Attendance Entries</h5>
            <span class="badge bg-light text-secondary border">{{ $student->attendances->count() }} total logged days</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th class="text-center">Status</th>
                            <th>Notes / Remarks</th>
                            <th class="text-end pe-4">Logged At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($student->attendances as $attendance)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="bi bi-calendar-event me-2 text-info"></i>
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('D, M d, Y') }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeStyles = [
                                            'present' => 'bg-success-subtle text-success border-success-subtle',
                                            'late' => 'bg-warning-subtle text-warning border-warning-subtle',
                                            'absent' => 'bg-danger-subtle text-danger border-danger-subtle'
                                        ];
                                        $style = $badgeStyles[$attendance->status] ?? 'bg-secondary-subtle text-secondary';
                                    @endphp
                                    <span class="badge {{ $style }} border px-3 py-1 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $attendance->notes ? 'text-dark' : 'text-muted fst-italic' }}">
                                        {{ $attendance->notes ?: 'No notes added' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-muted small font-monospace">
                                    {{ $attendance->created_at ? $attendance->created_at->format('Y-m-d H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No attendance history has been recorded for this student yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
