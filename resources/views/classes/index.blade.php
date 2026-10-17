@extends('layouts.app')

@section('title', 'Classes & Rooms | Attendance System')
@section('page-title', 'Classes & Rooms')
@section('page-subtitle', 'Manage classrooms, student cohorts, and teacher assignments')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0 fw-bold">Active Classes</h4>
            <p class="text-muted small mb-0">Total of {{ $classes->count() }} registered classroom cohorts</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <!-- Export Classes Summary -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i>
                    <span>Export Classes</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="{{ route('export.class.overview') }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Download Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="{{ route('export.class.overview', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Download CSV (.csv)</a></li>
                </ul>
            </div>

            <!-- PROMINENT ADD CLASS BUTTON (Visible to both teachers and admins) -->
            <a href="{{ route('classes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill fs-5"></i>
                <span class="fw-semibold">Add New Class</span>
            </a>
        </div>
    </div>

    @if ($classes->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-mortarboard display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">No Classes Found</h4>
                <p class="text-muted mb-4">Get started by creating your first class cohort.</p>
                <a href="{{ route('classes.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Create First Class
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

                            <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a href="{{ route('attendance.create', $class) }}" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Take attendance</span>
                                </a>

                                <div class="d-flex gap-1">
                                    <!-- EDIT CLASS BUTTON -->
                                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-1" title="Edit class details">
                                        <i class="bi bi-pencil"></i>
                                        <span>Edit</span>
                                    </a>

                                    <!-- DELETE CLASS BUTTON -->
                                    <form method="POST" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Are you sure you want to delete class \'{{ $class->name }}\'? This will also delete related student and attendance records.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2" title="Delete class">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
