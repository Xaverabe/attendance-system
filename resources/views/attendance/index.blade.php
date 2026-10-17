@extends('layouts.app')

@section('title', 'Attendance Records & History | Attendance System')
@section('page-title', 'Attendance Management')
@section('page-subtitle', 'Record daily presence, import paper sheets via Excel, and export historical logs')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold">Class Attendance Hub</h4>
            <p class="text-muted small mb-0">Record or bulk-import daily student attendance across classrooms</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Export Full Attendance Log -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i>
                    <span>Export Attendance Logs</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="{{ route('export.attendance') }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Download All Logs (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('export.attendance', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Download All Logs (.csv)</a></li>
                </ul>
            </div>

            <!-- Import from Excel Button -->
            <a href="{{ route('attendance.import') }}" class="btn btn-success rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel-fill"></i>
                <span class="fw-semibold">Import Paper Attendance (Excel)</span>
            </a>
        </div>
    </div>

    @if ($classes->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-calendar-x display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">No Classes Available</h4>
                <p class="text-muted mb-4">You need at least one classroom before recording attendance.</p>
                <a href="{{ route('classes.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Create Class
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach ($classes as $class)
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold text-dark mb-0">{{ $class->name }}</h5>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-people me-1"></i> {{ $class->students->count() }} students
                                    </span>
                                </div>
                                <p class="text-muted small mb-4">
                                    <i class="bi bi-person-badge me-1 text-info"></i> Teacher: 
                                    <strong class="text-dark">{{ $class->teacher->name ?? 'Unassigned' }}</strong>
                                </p>
                            </div>

                            <div class="pt-3 border-top">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('attendance.create', $class) }}" class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Mark Daily Attendance</span>
                                    </a>

                                    <div class="d-flex gap-2">
                                        <!-- Download Excel Template -->
                                        <a href="{{ route('attendance.template', $class) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 d-flex align-items-center justify-content-center gap-1" title="Download Excel template for this class">
                                            <i class="bi bi-file-earmark-arrow-down"></i>
                                            <span>Get Template</span>
                                        </a>

                                        <!-- Export Log for Class -->
                                        <a href="{{ route('export.attendance', ['class_id' => $class->id]) }}" class="btn btn-outline-success btn-sm rounded-pill flex-grow-1 d-flex align-items-center justify-content-center gap-1" title="Export this class's attendance logs">
                                            <i class="bi bi-download"></i>
                                            <span>Export Log</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
