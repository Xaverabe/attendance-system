@extends('layouts.app')

@section('title', 'Students Directory | Attendance System')
@section('page-title', 'Students Directory')
@section('page-subtitle', 'Manage student enrollment, ID numbers, and individual attendance history')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold">Enrolled Students</h4>
            <p class="text-muted small mb-0">Total of {{ $students->count() }} registered students</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Export Students Directory -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i>
                    <span>Export Students</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="{{ route('export.students') }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Download Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('export.students', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Download CSV (.csv)</a></li>
                </ul>
            </div>

            <!-- Prominent Add Student Button -->
            <a href="{{ route('students.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill fs-5"></i>
                <span class="fw-semibold">Add New Student</span>
            </a>
        </div>
    </div>

    @if ($students->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-people display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">No Students Registered Yet</h4>
                <p class="text-muted mb-4">Enroll students into classrooms to start recording daily attendance.</p>
                <a href="{{ route('students.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Add First Student
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Student Number</th>
                            <th>Assigned Class</th>
                            <th class="text-end pe-4" style="min-width: 240px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php
                                $initials = collect(explode(' ', $student->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $student->name }}</div>
                                            <small class="text-muted">Registered in database</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border font-monospace px-2 py-1">{{ $student->student_number }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                        <i class="bi bi-mortarboard me-1"></i> {{ $student->classroom?->name ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- EDIT STUDENT BUTTON -->
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1" title="Edit student details">
                                            <i class="bi bi-pencil"></i>
                                            <span>Edit</span>
                                        </a>

                                        <!-- VIEW ATTENDANCE BUTTON -->
                                        <a href="{{ route('attendance.student', $student) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1" title="View individual attendance record">
                                            <i class="bi bi-calendar3-check"></i>
                                            <span>Attendance</span>
                                        </a>

                                        <!-- DELETE STUDENT BUTTON -->
                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to remove student \'{{ $student->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2" title="Remove student">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
