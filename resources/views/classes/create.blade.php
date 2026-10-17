@extends('layouts.app')

@section('title', 'Create Class | Attendance System')
@section('page-title', 'Create New Class')
@section('page-subtitle', 'Add a new classroom group to the academic registry')

@section('content')
<div class="container py-2" style="max-width: 640px;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom border-light p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                    <i class="bi bi-plus-circle-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Create Class Cohort</h5>
                    <small class="text-muted">Enter class title and assign an instructor</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('classes.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Class Name</label>
                    <input id="name" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Grade 10 - Science">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if (auth()->user()->role === 'admin' && isset($teachers) && $teachers->isNotEmpty())
                    <div class="mb-4">
                        <label for="teacher_id" class="form-label fw-semibold">Assign Teacher</label>
                        <select id="teacher_id" name="teacher_id" class="form-select form-select-lg @error('teacher_id') is-invalid @enderror" required>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} ({{ $teacher->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <small class="text-muted d-block">Supervising Teacher</small>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
