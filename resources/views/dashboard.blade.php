@extends('layouts.app')

@section('title', 'Attendance Dashboard | Teacher Workspace')
@section('page-title', 'Attendance Dashboard')
@section('page-subtitle', 'Real-time overview of attendance rates, classroom performance, and student tracking')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .quick-guide-card {
        border: 1px solid rgba(56, 189, 248, 0.2);
        background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
        border-radius: 16px;
    }
    .sortable-th {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
    }
    .sortable-th:hover {
        background-color: #e2e8f0 !important;
    }
    .sortable-th.active-sort {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Quick Guide Banner for Non-Coding Teachers & Admins -->
    <div class="quick-guide-card p-4 mb-4 shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white text-uppercase px-2 py-1" style="font-size: 0.7rem;">Quick Start</span>
                    <h5 class="mb-0 fw-bold text-dark">Welcome back, {{ auth()->user()->name }} 👋</h5>
                </div>
                <p class="text-muted mb-0 mt-1 small">
                    Use the <strong>left menu bar</strong> to navigate between classrooms, student records, and attendance logs. What would you like to do right now?
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    <span>Take Today's Attendance</span>
                </a>
                <a href="{{ route('attendance.import') }}" class="btn btn-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                    <span>Import from Excel (Paper Sheet)</span>
                </a>
                <a href="{{ route('students.index') }}" class="btn btn-outline-dark rounded-pill px-3">
                    <i class="bi bi-people me-1"></i> Students
                </a>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-calendar-check me-1"></i> Attendance Log
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Key Metric Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Classes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Classes</span>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ count($classes) }}</div>
                        <small class="text-primary"><i class="bi bi-mortarboard me-1"></i>Active groups</small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Present</span>
                        <div class="fs-2 fw-bold text-success mt-1">{{ $attendanceSummary['present'] ?? 0 }}</div>
                        <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>On-time students</small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Today -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Late</span>
                        <div class="fs-2 fw-bold text-warning mt-1">{{ $attendanceSummary['late'] ?? 0 }}</div>
                        <small class="text-warning"><i class="bi bi-clock-history me-1"></i>Tardy arrivals</small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Absent</span>
                        <div class="fs-2 fw-bold text-danger mt-1">{{ $attendanceSummary['absent'] ?? 0 }}</div>
                        <small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Unexcused / missing</small>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Class Summary -->
    <div class="row g-4 mb-4">
        <!-- Class Comparison Chart -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">Attendance by Class</h5>
                        <small class="text-muted">Comparing tardiness & absence across classes</small>
                    </div>
                    <span class="badge bg-light text-secondary border">Bar Chart</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 240px; position: relative;">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Quick Summary Roster -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Class Overview</h5>
                        <small class="text-muted">Breakdown per assigned classroom</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-success dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="{{ route('export.class.overview') }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export as Excel (.xlsx)</a></li>
                                <li><a class="dropdown-item" href="{{ route('export.class.overview', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Export as CSV (.csv)</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('classes.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="list-group list-group-flush">
                        @forelse ($classOverview as $class)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-dark">{{ $class['name'] }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $class['total_students'] }} students</span>
                                </div>
                                <div class="small mt-2 d-flex gap-3 text-muted">
                                    <span>Late: <strong class="text-warning">{{ $class['late'] }}</strong></span>
                                    <span>Absent: <strong class="text-danger">{{ $class['absent'] }}</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">No classrooms assigned yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Tracking Table (WITH SORTING & EXPORT) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-0 fw-bold">Student Monitoring Directory</h5>
                <small class="text-muted">Click table column headers or use dropdown to sort by indicator</small>
            </div>
            
            <div class="d-flex align-items-center flex-wrap gap-2">
                <!-- Quick Sort Dropdown Selector -->
                <div class="d-flex align-items-center gap-1">
                    <label for="sortSelector" class="small text-muted fw-semibold me-1 d-none d-md-inline"><i class="bi bi-sort-down text-primary"></i> Sort by:</label>
                    <select id="sortSelector" class="form-select form-select-sm rounded-pill" style="min-width: 170px;" onchange="handleDropdownSort(this.value)">
                        <option value="name_asc">Name (A → Z)</option>
                        <option value="name_desc">Name (Z → A)</option>
                        <option value="rate_desc" selected>Highest Attendance Rate</option>
                        <option value="rate_asc">Lowest Attendance Rate</option>
                        <option value="present_desc">Most Present</option>
                        <option value="late_desc">Most Late</option>
                        <option value="absent_desc">Most Absent</option>
                    </select>
                </div>

                <!-- Export Table Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-success dropdown-toggle rounded-pill px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i>
                        <span>Export Table</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="{{ route('export.dashboard.students') }}"><i class="bi bi-file-earmark-excel text-success me-2"></i>Download Excel (.xlsx)</a></li>
                        <li><a class="dropdown-item" href="{{ route('export.dashboard.students', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv text-primary me-2"></i>Download CSV (.csv)</a></li>
                    </ul>
                </div>

                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-people me-1"></i> Full Directory
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="studentMonitoringTable">
                    <thead class="table-light">
                        <tr>
                            <!-- Clickable Sortable Headers -->
                            <th class="ps-4 sortable-th" onclick="sortTable('name')" id="th-name" title="Click to sort by Name">
                                <div class="d-flex align-items-center gap-1">
                                    <span>Student</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                            <th class="sortable-th" onclick="sortTable('class')" id="th-class" title="Click to sort by Classroom">
                                <div class="d-flex align-items-center gap-1">
                                    <span>Classroom</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" onclick="sortTable('present')" id="th-present" title="Click to sort by Present count">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span>Present</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" onclick="sortTable('late')" id="th-late" title="Click to sort by Late count">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span>Late</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" onclick="sortTable('absent')" id="th-absent" title="Click to sort by Absent count">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span>Absent</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                            <th class="text-center pe-4 sortable-th" onclick="sortTable('rate')" id="th-rate" title="Click to sort by Attendance Rate">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span>Attendance Rate</span>
                                    <i class="bi bi-arrow-down-up text-muted sort-icon" style="font-size: 0.75rem;"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        @forelse ($studentRows as $student)
                            <tr class="student-item-row"
                                data-name="{{ strtolower($student['name']) }}"
                                data-class="{{ strtolower($student['class'] ?? '') }}"
                                data-present="{{ (int)$student['present'] }}"
                                data-late="{{ (int)$student['late'] }}"
                                data-absent="{{ (int)$student['absent'] }}"
                                data-rate="{{ (float)$student['attendance_rate'] }}">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $student['name'] }}</div>
                                    <small class="text-muted font-monospace">{{ $student['student_number'] }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $student['class'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success px-2 py-1">{{ $student['present'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1">{{ $student['late'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">{{ $student['absent'] }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                            <div class="progress-bar {{ $student['attendance_rate'] >= 80 ? 'bg-success' : ($student['attendance_rate'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 style="width: {{ $student['attendance_rate'] }}%"></div>
                                        </div>
                                        <span class="fw-bold small">{{ $student['attendance_rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No student records available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- Chart.js Rendering ---
    const labels = @json($classOverview->pluck('name'));
    const lateValues = @json($classOverview->pluck('late'));
    const absentValues = @json($classOverview->pluck('absent'));

    const ctx = document.getElementById('classChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Late',
                        data: lateValues,
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    },
                    {
                        label: 'Absent',
                        data: absentValues,
                        backgroundColor: '#f43f5e',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // --- Interactive Table Sorting System ---
    let currentSortColumn = 'rate';
    let currentSortDirection = 'desc';

    function sortTable(column) {
        if (currentSortColumn === column) {
            // Toggle direction
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = column;
            // Default string columns to asc, numeric indicators to desc
            currentSortDirection = (column === 'name' || column === 'class') ? 'asc' : 'desc';
        }

        applySorting();
        syncSortDropdown();
    }

    function handleDropdownSort(val) {
        const parts = val.split('_');
        currentSortColumn = parts[0];
        currentSortDirection = parts[1];
        applySorting();
    }

    function syncSortDropdown() {
        const selector = document.getElementById('sortSelector');
        if (!selector) return;
        const targetVal = `${currentSortColumn}_${currentSortDirection}`;
        for (let opt of selector.options) {
            if (opt.value === targetVal) {
                selector.value = targetVal;
                break;
            }
        }
    }

    function applySorting() {
        const tbody = document.getElementById('studentTableBody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('.student-item-row'));
        if (rows.length === 0) return;

        rows.sort((a, b) => {
            let valA, valB;

            if (currentSortColumn === 'name' || currentSortColumn === 'class') {
                valA = (a.getAttribute(`data-${currentSortColumn}`) || '').toLowerCase();
                valB = (b.getAttribute(`data-${currentSortColumn}`) || '').toLowerCase();
                return currentSortDirection === 'asc' 
                    ? valA.localeCompare(valB) 
                    : valB.localeCompare(valA);
            } else {
                valA = parseFloat(a.getAttribute(`data-${currentSortColumn}`)) || 0;
                valB = parseFloat(b.getAttribute(`data-${currentSortColumn}`)) || 0;
                return currentSortDirection === 'asc' ? valA - valB : valB - valA;
            }
        });

        // Re-append sorted rows
        rows.forEach(row => tbody.appendChild(row));

        // Update header UI indicators
        document.querySelectorAll('.sortable-th').forEach(th => {
            th.classList.remove('active-sort');
            const icon = th.querySelector('.sort-icon');
            if (icon) {
                icon.className = 'bi bi-arrow-down-up text-muted sort-icon';
            }
        });

        const activeTh = document.getElementById(`th-${currentSortColumn}`);
        if (activeTh) {
            activeTh.classList.add('active-sort');
            const icon = activeTh.querySelector('.sort-icon');
            if (icon) {
                if (currentSortColumn === 'name' || currentSortColumn === 'class') {
                    icon.className = currentSortDirection === 'asc' 
                        ? 'bi bi-sort-alpha-down text-primary sort-icon' 
                        : 'bi bi-sort-alpha-up-alt text-primary sort-icon';
                } else {
                    icon.className = currentSortDirection === 'asc' 
                        ? 'bi bi-sort-numeric-down text-primary sort-icon' 
                        : 'bi bi-sort-numeric-up-alt text-primary sort-icon';
                }
            }
        }
    }

    // Apply default sort on page load
    document.addEventListener('DOMContentLoaded', () => {
        applySorting();
    });
</script>
@endpush
