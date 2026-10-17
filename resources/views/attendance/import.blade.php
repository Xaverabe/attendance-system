@extends('layouts.app')

@section('title', 'Import Attendance from Excel | Attendance System')
@section('page-title', 'Excel Attendance Import')
@section('page-subtitle', 'Import paper-recorded student attendance by downloading and filling our official Excel template')

@section('content')
<div class="container-fluid px-0" style="max-width: 960px;">
    <!-- Flash Warning with Error Details if any -->
    @if (session('warning'))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
                <h5 class="mb-0 fw-bold">{{ session('warning') }}</h5>
            </div>
            @if (session('import_errors'))
                <ul class="mb-0 mt-2 small ps-3">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="row g-4">
        <!-- Step 1: Download Template -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                            <i class="bi bi-file-earmark-arrow-down-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary text-uppercase px-2 py-1 mb-1" style="font-size: 0.65rem;">Step 1</span>
                            <h5 class="mb-0 fw-bold">Download Template</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small">
                            Generate a customized Excel spreadsheet pre-filled with all enrolled students for your selected classroom and date.
                        </p>

                        <div class="mb-3">
                            <label for="templateClassSelect" class="form-label fw-semibold small text-muted text-uppercase">Classroom</label>
                            <select id="templateClassSelect" class="form-select form-select-lg">
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->students->count() }} students)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="templateDateInput" class="form-label fw-semibold small text-muted text-uppercase">Target Attendance Date</label>
                            <input type="date" id="templateDateInput" class="form-control form-control-lg" value="{{ $selectedDate }}">
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-4">
                            <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle text-info me-1"></i> Paper Sheet Instructions:</h6>
                            <ol class="small text-muted mb-0 ps-3">
                                <li>Download the spreadsheet for your class.</li>
                                <li>Transfer records from your paper attendance sheet.</li>
                                <li>Fill column <strong>Status</strong> with <code>present</code>, <code>late</code>, or <code>absent</code>.</li>
                                <li>Save the file as <strong>.xlsx</strong> or <strong>.csv</strong>.</li>
                            </ol>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-lg rounded-pill w-100 d-flex align-items-center justify-content-center gap-2" onclick="triggerTemplateDownload()">
                        <i class="bi bi-download"></i>
                        <span>Download Class Template (.xlsx)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Upload Completed File -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                            <i class="bi bi-cloud-arrow-up-fill fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-success text-uppercase px-2 py-1 mb-1" style="font-size: 0.65rem;">Step 2</span>
                            <h5 class="mb-0 fw-bold">Upload Filled Excel</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('attendance.import.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <p class="text-muted small">
                            Upload your completed Excel or CSV spreadsheet. The system will automatically match students and save attendance records.
                        </p>

                        <div class="mb-3">
                            <label for="importClassSelect" class="form-label fw-semibold small text-muted text-uppercase">Target Classroom</label>
                            <select id="importClassSelect" name="class_room_id" class="form-select form-select-lg" required>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="importDateInput" class="form-label fw-semibold small text-muted text-uppercase">Attendance Date</label>
                            <input type="date" id="importDateInput" name="date" class="form-control form-control-lg" value="{{ $selectedDate }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="importFile" class="form-label fw-semibold small text-muted text-uppercase">Excel / CSV File</label>
                            <input type="file" id="importFile" name="file" class="form-control form-control-lg" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Supported formats: <strong>.xlsx, .xls, .csv</strong> (Max 10MB)</small>
                        </div>

                        <div class="pt-3 border-top">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-cloud-check-fill"></i>
                                <span>Upload & Process Attendance</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function triggerTemplateDownload() {
        const classId = document.getElementById('templateClassSelect').value;
        const date = document.getElementById('templateDateInput').value || '{{ now()->toDateString() }}';
        
        if (!classId) {
            alert('Please select a classroom first.');
            return;
        }

        const url = `/attendance/template/${classId}?date=${encodeURIComponent(date)}`;
        window.location.href = url;
    }
</script>
@endpush

