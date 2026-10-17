@extends('layouts.app')

@section('title', 'Add Student | Attendance System')
@section('page-title', 'Enroll New Student')
@section('page-subtitle', 'Register a student and assign them to an active classroom')

@section('content')
<div class="container py-2" style="max-width: 640px;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom border-light p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                    <i class="bi bi-person-plus-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Enroll Student</h5>
                    <small class="text-muted">Enter the student's personal and academic details</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('students.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Student Full Name</label>
                    <input id="name" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Emily Watson">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="student_number" class="form-label fw-semibold">Student Identification Number</label>
                    <input id="student_number" name="student_number" class="form-control form-control-lg font-monospace @error('student_number') is-invalid @enderror" value="{{ old('student_number') }}" required placeholder="e.g. S-1005">
                    @error('student_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="class_room_id" class="form-label fw-semibold">Assigned Classroom</label>
                    <select id="class_room_id" name="class_room_id" class="form-select form-select-lg @error('class_room_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('class_room_id') ? '' : 'selected' }}>-- Choose a Classroom --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
