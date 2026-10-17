<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Attendance System | Teacher Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-card: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.18);
            --accent-green: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --accent-blue: #38bdf8;
        }

        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            color: #f8fafc;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glowing background circles */
        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            left: -5%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -10%;
            right: -5%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .app-container {
            position: relative;
            z-index: 1;
            padding: 24px 16px 60px 16px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .glass-panel {
            background: var(--glass-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.45);
        }

        .nav-tabs-custom {
            border-bottom: 1px solid var(--glass-border);
            gap: 8px;
        }

        .nav-tabs-custom .nav-link {
            color: #cbd5e1;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-tabs-custom .nav-link.active {
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
            border-bottom: 3px solid #38bdf8;
        }

        /* Status segmented pill toggles */
        .status-toggle-group {
            display: inline-flex;
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            padding: 3px;
            user-select: none;
        }

        .status-toggle-option {
            display: none;
        }

        .status-label {
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 24px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0 1px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-toggle-option:checked + .status-label.label-present {
            background-color: var(--accent-green);
            color: #fff;
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
        }

        .status-toggle-option:checked + .status-label.label-late {
            background-color: var(--accent-amber);
            color: #1e293b;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.4);
        }

        .status-toggle-option:checked + .status-label.label-absent {
            background-color: var(--accent-rose);
            color: #fff;
            box-shadow: 0 2px 10px rgba(244, 63, 94, 0.4);
        }

        .student-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .table-custom {
            --bs-table-bg: transparent;
            --bs-table-color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .table-custom tr {
            transition: background 0.15s ease;
        }

        .table-custom tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.04);
        }

        .clock-badge {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 12px;
            padding: 8px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-variant-numeric: tabular-nums;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Camera Viewport Styling */
        .camera-box {
            position: relative;
            background: #020617;
            border: 2px dashed rgba(56, 189, 248, 0.4);
            border-radius: 16px;
            overflow: hidden;
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #faceVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 14px;
            display: none;
        }

        .face-target-reticle {
            position: absolute;
            width: 220px;
            height: 260px;
            border: 2px solid #38bdf8;
            border-radius: 24px;
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.5), inset 0 0 25px rgba(56, 189, 248, 0.3);
            display: none;
            pointer-events: none;
            animation: scanning-reticle 2.5s ease-in-out infinite alternate;
        }

        @keyframes scanning-reticle {
            0% { border-color: #38bdf8; transform: scale(0.98); }
            100% { border-color: #10b981; transform: scale(1.02); }
        }

        .scanner-beam {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #38bdf8, transparent);
            box-shadow: 0 0 10px #38bdf8;
            display: none;
            animation: scan-vertical 2s linear infinite;
        }

        @keyframes scan-vertical {
            0% { top: 10%; }
            100% { top: 90%; }
        }
    </style>
</head>
<body>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="app-container">
        <!-- Top Navigation Bar -->
        <header class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-light border-opacity-10 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="student-avatar bg-primary bg-opacity-25 text-info" style="width: 48px; height: 48px;">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">Smart Attendance System</h4>
                    <span class="text-light opacity-75 small">Teacher Class Desk & Rapid Daily Check-in</span>
                </div>
            </div>

            <!-- Live Server Time -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="clock-badge">
                    <span class="pulse-dot"></span>
                    <span class="fw-semibold text-info" id="liveDate">{{ $formattedDate }}</span>
                    <span class="text-white-50">|</span>
                    <span class="fw-mono text-light" id="liveTime">--:--:--</span>
                </div>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="badge bg-primary text-uppercase">{{ auth()->user()->role }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('classes.index') }}"><i class="bi bi-mortarboard me-2"></i>My Classes</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check me-2"></i>Attendance Log</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickLoginModal">
                            <i class="bi bi-key-fill me-1"></i> Teacher 1-Click Login
                        </button>
                    </div>
                @endauth
            </div>
        </header>

        <!-- System Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4 bg-success bg-opacity-75 text-white" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4 bg-danger bg-opacity-75 text-white" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4 bg-danger bg-opacity-75 text-white">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Workspace Panel with Glassmorphism -->
        <div class="glass-panel p-4">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="mainTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-2" id="teacher-tab" data-bs-toggle="tab" data-bs-target="#teacher-pane" type="button" role="tab">
                        <i class="bi bi-pencil-square text-info"></i>
                        <span>Teacher Input (Today: {{ $todayDate }})</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="face-tab" data-bs-toggle="tab" data-bs-target="#face-pane" type="button" role="tab">
                        <i class="bi bi-camera-video text-warning"></i>
                        <span>Face Recognition Scanner</span>
                        <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.65rem;">EXPANSION PREVIEW</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                        <i class="bi bi-info-circle"></i>
                        <span>System Info</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="mainTabContent">
                <!-- TAB 1: TEACHER ATTENDANCE INPUT -->
                <div class="tab-pane fade show active" id="teacher-pane" role="tabpanel">
                    @if ($classes->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-folder-x fs-1 text-light opacity-50"></i>
                            <h4 class="mt-3">No Classes Found</h4>
                            <p class="text-light opacity-75">There are no classes assigned to your account or registered yet.</p>
                            @auth
                                <a href="{{ route('classes.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-lg me-1"></i> Create a Class
                                </a>
                            @else
                                <button class="btn btn-outline-light mt-2" data-bs-toggle="modal" data-bs-target="#quickLoginModal">
                                    Sign In as Teacher
                                </button>
                            @endauth
                        </div>
                    @else
                        <!-- Form for Submitting Attendance -->
                        <form id="attendanceForm" method="POST" action="{{ route('attendance.quickStore') }}">
                            @csrf
                            
                            <!-- Control Bar: Class, Date, Batch Actions -->
                            <div class="row g-3 align-items-center mb-4 p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10">
                                <!-- Classroom Selection -->
                                <div class="col-lg-4 col-md-6">
                                    <label for="classSelect" class="form-label text-light small fw-bold text-uppercase opacity-75 mb-1">
                                        <i class="bi bi-mortarboard me-1 text-info"></i> Select Classroom
                                    </label>
                                    <select class="form-select bg-dark text-white border-secondary" id="classSelect" name="class_room_id" required>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->students->count() }} students)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Attendance Date Input (Pre-filled with now date) -->
                                <div class="col-lg-3 col-md-6">
                                    <label for="attendanceDate" class="form-label text-light small fw-bold text-uppercase opacity-75 mb-1">
                                        <i class="bi bi-calendar-event me-1 text-info"></i> Attendance Date
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control bg-dark text-white border-secondary" id="attendanceDate" name="date" value="{{ $todayDate }}" required>
                                        <button type="button" class="btn btn-outline-secondary text-info border-secondary" onclick="document.getElementById('attendanceDate').value = '{{ $todayDate }}';" title="Reset to today">
                                            Today
                                        </button>
                                    </div>
                                </div>

                                <!-- Batch Actions & Search -->
                                <div class="col-lg-5 col-12 d-flex flex-column justify-content-end">
                                    <span class="text-light small fw-bold text-uppercase opacity-75 mb-1">Quick Batch Fill</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-success text-white flex-grow-1" onclick="batchMarkAll('present')">
                                            <i class="bi bi-check-all me-1 text-success"></i> All Present
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-white flex-grow-1" onclick="batchMarkAll('late')">
                                            <i class="bi bi-clock-history me-1 text-warning"></i> All Late
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger text-white flex-grow-1" onclick="batchMarkAll('absent')">
                                            <i class="bi bi-x-circle me-1 text-danger"></i> All Absent
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Filter Input -->
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div class="input-group" style="max-width: 320px;">
                                    <span class="input-group-text bg-dark border-secondary text-white-50"><i class="bi bi-search"></i></span>
                                    <input type="text" id="studentFilter" class="form-control bg-dark text-white border-secondary" placeholder="Filter student by name or #...">
                                </div>

                                <!-- Real-time Status Counter -->
                                <div class="d-flex align-items-center gap-3 small">
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> <span id="countPresent">0</span> Present
                                    </span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 px-3 py-2 rounded-pill">
                                        <i class="bi bi-clock me-1"></i> <span id="countLate">0</span> Late
                                    </span>
                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> <span id="countAbsent">0</span> Absent
                                    </span>
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary px-3 py-2 rounded-pill">
                                        Total: <span id="countTotal">0</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Student Tables (One per Classroom for instantaneous client switching) -->
                            @foreach ($classes as $class)
                                <div class="class-group-wrapper" id="class-wrapper-{{ $class->id }}" style="{{ $selectedClassId == $class->id ? '' : 'display: none;' }}">
                                    @if ($class->students->isEmpty())
                                        <div class="text-center py-4 bg-black bg-opacity-20 rounded-4">
                                            <p class="text-light opacity-75 mb-0">No students are currently enrolled in {{ $class->name }}.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive rounded-4 border border-white border-opacity-10 bg-black bg-opacity-20">
                                            <table class="table table-custom align-middle mb-0">
                                                <thead class="table-dark bg-dark bg-opacity-50 text-uppercase small text-light opacity-75">
                                                    <tr>
                                                        <th style="width: 60px;">#</th>
                                                        <th>Student</th>
                                                        <th>Student ID</th>
                                                        <th class="text-center" style="min-width: 250px;">Attendance Status</th>
                                                        <th style="min-width: 200px;">Remarks / Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($class->students as $index => $student)
                                                        @php
                                                            // Check if student has attendance recorded for today
                                                            $todayAttendance = $student->attendances->first();
                                                            $currentStatus = $todayAttendance ? $todayAttendance->status : 'present';
                                                            $currentNote = $todayAttendance ? $todayAttendance->notes : '';
                                                            $initials = collect(explode(' ', $student->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                                                        @endphp
                                                        <tr class="student-row" data-name="{{ strtolower($student->name) }}" data-number="{{ strtolower($student->student_number) }}">
                                                            <td class="text-light opacity-50">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <div class="student-avatar">{{ $initials }}</div>
                                                                    <div>
                                                                        <div class="fw-bold text-white">{{ $student->name }}</div>
                                                                        <small class="text-light opacity-50">Enrolled in {{ $class->name }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-dark border border-secondary text-info font-monospace">{{ $student->student_number }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <!-- 3-Way Segmented Radio Controls -->
                                                                <div class="status-toggle-group">
                                                                    <input type="radio" 
                                                                           class="status-toggle-option" 
                                                                           id="status_{{ $class->id }}_{{ $student->id }}_present" 
                                                                           name="attendance[{{ $student->id }}]" 
                                                                           value="present" 
                                                                           {{ $currentStatus === 'present' ? 'checked' : '' }}
                                                                           {{ $selectedClassId == $class->id ? '' : 'disabled' }}
                                                                           onchange="updateCounts()">
                                                                    <label class="status-label label-present" for="status_{{ $class->id }}_{{ $student->id }}_present">
                                                                        <i class="bi bi-check-lg"></i> Present
                                                                    </label>

                                                                    <input type="radio" 
                                                                           class="status-toggle-option" 
                                                                           id="status_{{ $class->id }}_{{ $student->id }}_late" 
                                                                           name="attendance[{{ $student->id }}]" 
                                                                           value="late" 
                                                                           {{ $currentStatus === 'late' ? 'checked' : '' }}
                                                                           {{ $selectedClassId == $class->id ? '' : 'disabled' }}
                                                                           onchange="updateCounts()">
                                                                    <label class="status-label label-late" for="status_{{ $class->id }}_{{ $student->id }}_late">
                                                                        <i class="bi bi-clock"></i> Late
                                                                    </label>

                                                                    <input type="radio" 
                                                                           class="status-toggle-option" 
                                                                           id="status_{{ $class->id }}_{{ $student->id }}_absent" 
                                                                           name="attendance[{{ $student->id }}]" 
                                                                           value="absent" 
                                                                           {{ $currentStatus === 'absent' ? 'checked' : '' }}
                                                                           {{ $selectedClassId == $class->id ? '' : 'disabled' }}
                                                                           onchange="updateCounts()">
                                                                    <label class="status-label label-absent" for="status_{{ $class->id }}_{{ $student->id }}_absent">
                                                                        <i class="bi bi-x-lg"></i> Absent
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" 
                                                                       name="notes[{{ $student->id }}]" 
                                                                       class="form-control form-control-sm bg-dark text-white border-secondary" 
                                                                       placeholder="Optional remarks..." 
                                                                       value="{{ $currentNote }}"
                                                                       {{ $selectedClassId == $class->id ? '' : 'disabled' }}>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Submission Footer -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-light border-opacity-10 flex-wrap gap-3">
                                <div class="text-light opacity-75 small">
                                    <i class="bi bi-shield-lock me-1 text-info"></i>
                                    @auth
                                        Recording as <strong>{{ auth()->user()->name }}</strong> (Verified Teacher)
                                    @else
                                        <span class="text-warning">Guest Mode: Please log in to commit changes to the official database.</span>
                                    @endauth
                                </div>

                                <div class="d-flex gap-2">
                                    @auth
                                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg d-flex align-items-center gap-2">
                                            <i class="bi bi-cloud-arrow-up-fill"></i>
                                            <span>Save Today's Attendance</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-warning btn-lg rounded-pill px-4 shadow-lg d-flex align-items-center gap-2 text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#quickLoginModal">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            <span>Log In as Teacher to Save</span>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- TAB 2: FUTURE FACE RECOGNITION SCANNER PREVIEW -->
                <div class="tab-pane fade" id="face-pane" role="tabpanel">
                    <div class="row g-4 align-items-stretch">
                        <!-- Camera Viewport Box -->
                        <div class="col-lg-7">
                            <div class="camera-box p-3">
                                <!-- Reticle & Scanner animation -->
                                <div class="face-target-reticle" id="faceReticle">
                                    <div class="scanner-beam" id="scannerBeam"></div>
                                </div>

                                <!-- Live Video Feed -->
                                <video id="faceVideo" autoplay playsinline muted></video>

                                <!-- Idle Screen (Before camera permission) -->
                                <div id="cameraIdleState" class="text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-camera-fill display-3 text-info opacity-75"></i>
                                    </div>
                                    <h4 class="fw-bold">Ready for Face Recognition</h4>
                                    <p class="text-light opacity-75 small max-w-sm mx-auto" style="max-width: 380px;">
                                        In the next phase, students can submit attendance themselves using their phone camera, personal laptop, or this teacher kiosk device.
                                    </p>
                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-info rounded-pill px-4" id="btnStartCamera">
                                            <i class="bi bi-play-circle me-1"></i> Start Camera Stream Test
                                        </button>
                                    </div>
                                </div>

                                <!-- Camera Active Controls -->
                                <div id="cameraActiveControls" class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-75 d-none justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="pulse-dot"></span>
                                        <small class="text-success fw-bold">Live Stream Active (WebRTC)</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" id="btnStopCamera">
                                        <i class="bi bi-stop-circle me-1"></i> Stop Camera
                                    </button>
                                </div>
                            </div>

                            <!-- Live Feedback Simulator -->
                            <div class="alert alert-dark border-secondary mt-3 rounded-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cpu text-info fs-5"></i>
                                    <div>
                                        <div class="fw-bold small">Recognition Pipeline Model</div>
                                        <div class="text-light opacity-50 small">Target: <code>face-api.js (SSD Mobilenet v1 / TinyFace)</code></div>
                                    </div>
                                </div>
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">Browser-Native (Zero GPU Cost)</span>
                            </div>
                        </div>

                        <!-- Face Recognition Configuration & Architecture -->
                        <div class="col-lg-5">
                            <div class="p-3 bg-black bg-opacity-25 rounded-4 border border-white border-opacity-10 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-gear-wide-connected text-info"></i>
                                        <span>Attendance Modes</span>
                                    </h5>

                                    <!-- Mode 1: Kiosk Station -->
                                    <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-3 border border-white border-opacity-10">
                                        <div class="d-flex align-items-center gap-2 text-info fw-bold mb-1">
                                            <i class="bi bi-laptop"></i>
                                            <span>Mode A: Teacher / Classroom Kiosk</span>
                                        </div>
                                        <p class="small text-light opacity-75 mb-0">
                                            Mount your laptop or tablet at the classroom entrance. As students step in front of the camera, their face vector is matched in real time and marked <strong>Present</strong> with an audible confirmation chime.
                                        </p>
                                    </div>

                                    <!-- Mode B: Student BYOD -->
                                    <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-3 border border-white border-opacity-10">
                                        <div class="d-flex align-items-center gap-2 text-warning fw-bold mb-1">
                                            <i class="bi bi-phone"></i>
                                            <span>Mode B: Student Self-Checkin (BYOD)</span>
                                        </div>
                                        <p class="small text-light opacity-75 mb-0">
                                            Students open the attendance page on their smartphones (iOS / Android), allow selfie camera access, perform a brief blink/smile liveness check, and register their attendance.
                                        </p>
                                    </div>

                                    <!-- Security & Anti-Spoofing Checklist -->
                                    <h6 class="fw-bold text-white mt-4 mb-2 small text-uppercase opacity-75">
                                        <i class="bi bi-shield-check me-1 text-success"></i> Security Architecture
                                    </h6>
                                    <ul class="list-unstyled small text-light opacity-75 ps-2 mb-0">
                                        <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>Liveness Detection</strong>: Detects blinking/movement to reject printed photos or screen recordings.</li>
                                        <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>Geofencing / Campus Wi-Fi</strong>: Ensures students are inside the classroom perimeter.</li>
                                        <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>Encrypted Descriptors</strong>: Stores 128-float mathematical vectors, keeping student biometric data secure.</li>
                                    </ul>
                                </div>

                                <div class="mt-4 pt-3 border-top border-light border-opacity-10 text-center">
                                    <button class="btn btn-outline-info w-100 rounded-pill" onclick="alert('Face Recognition integration is planned for Phase 2! Current teacher input on Tab 1 is ready for today\'s date.');">
                                        <i class="bi bi-code-slash me-1"></i> View Phase 2 API Specifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: SYSTEM OVERVIEW -->
                <div class="tab-pane fade" id="info-pane" role="tabpanel">
                    <div class="row g-4 py-2">
                        <div class="col-md-4">
                            <div class="p-4 bg-black bg-opacity-25 rounded-4 border border-white border-opacity-10 h-100">
                                <div class="student-avatar bg-info bg-opacity-25 text-info mb-3">
                                    <i class="bi bi-person-workspace fs-4"></i>
                                </div>
                                <h5>Teacher Role</h5>
                                <p class="text-light opacity-75 small">
                                    Input and modify daily attendance for assigned classrooms, track tardiness, write notes, and review historical performance.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-black bg-opacity-25 rounded-4 border border-white border-opacity-10 h-100">
                                <div class="student-avatar bg-primary bg-opacity-25 text-primary mb-3">
                                    <i class="bi bi-shield-lock-fill fs-4"></i>
                                </div>
                                <h5>Admin Dashboard</h5>
                                <p class="text-light opacity-75 small">
                                    Full oversight across all classes, teacher assignments, student registrations, and aggregated presence/absence statistics.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-black bg-opacity-25 rounded-4 border border-white border-opacity-10 h-100">
                                <div class="student-avatar bg-success bg-opacity-25 text-success mb-3">
                                    <i class="bi bi-upc-scan fs-4"></i>
                                </div>
                                <h5>Facial Recognition</h5>
                                <p class="text-light opacity-75 small">
                                    Next-generation automated attendance via camera feed on mobile phones or entrance kiosks powered by browser machine learning.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Teacher Sign-in Modal (For guest convenience) -->
    <div class="modal fade" id="quickLoginModal" tabindex="-1" aria-labelledby="quickLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border border-secondary shadow-lg rounded-4">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold" id="quickLoginModalLabel">
                        <i class="bi bi-box-arrow-in-right text-info me-2"></i>Teacher Sign In
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-light opacity-75 small mb-3">
                            Sign in to save and manage student attendance records.
                        </p>

                        <div class="mb-3">
                            <label for="modalEmail" class="form-label small text-light opacity-75">Email address</label>
                            <input type="email" class="form-control bg-secondary bg-opacity-25 text-white border-secondary" id="modalEmail" name="email" value="teacher@attendance.test" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalPassword" class="form-label small text-light opacity-75">Password</label>
                            <input type="password" class="form-control bg-secondary bg-opacity-25 text-white border-secondary" id="modalPassword" name="password" value="password" required>
                        </div>

                        <!-- 1-Click Fill Helper -->
                        <div class="p-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 mb-3 small d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-magic text-warning me-1"></i> Demo Teacher credentials</span>
                            <button type="button" class="btn btn-sm btn-outline-info py-0 px-2" onclick="fillTeacherCredentials()">Pre-fill</button>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Real-Time Live Clock
        function updateLiveClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: true });
            const clockEl = document.getElementById('liveTime');
            if (clockEl) {
                clockEl.textContent = timeStr;
            }
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Classroom Dropdown Switcher
        const classSelect = document.getElementById('classSelect');
        if (classSelect) {
            classSelect.addEventListener('change', function() {
                const selectedId = this.value;
                document.querySelectorAll('.class-group-wrapper').forEach(wrapper => {
                    const isTarget = wrapper.id === 'class-wrapper-' + selectedId;
                    wrapper.style.display = isTarget ? '' : 'none';

                    // Enable inputs only for active class to prevent submitting hidden classes
                    wrapper.querySelectorAll('input').forEach(input => {
                        input.disabled = !isTarget;
                    });
                });

                // Reset filter & recount
                const filterInput = document.getElementById('studentFilter');
                if (filterInput) filterInput.value = '';
                filterStudents('');
                updateCounts();
            });
        }

        // Live Student Search Filter
        const studentFilter = document.getElementById('studentFilter');
        if (studentFilter) {
            studentFilter.addEventListener('input', function() {
                filterStudents(this.value.toLowerCase().trim());
            });
        }

        function filterStudents(query) {
            const activeWrapper = document.querySelector('.class-group-wrapper:not([style*="display: none"])');
            if (!activeWrapper) return;

            const rows = activeWrapper.querySelectorAll('.student-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const number = row.getAttribute('data-number') || '';
                if (!query || name.includes(query) || number.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Batch Mark Status for all students in active classroom
        function batchMarkAll(status) {
            const activeWrapper = document.querySelector('.class-group-wrapper:not([style*="display: none"])');
            if (!activeWrapper) return;

            const radioButtons = activeWrapper.querySelectorAll(`input[type="radio"][value="${status}"]`);
            radioButtons.forEach(radio => {
                radio.checked = true;
            });
            updateCounts();
        }

        // Recount Attendance Summary (Present, Late, Absent)
        function updateCounts() {
            const activeWrapper = document.querySelector('.class-group-wrapper:not([style*="display: none"])');
            if (!activeWrapper) return;

            const presentCount = activeWrapper.querySelectorAll('input[type="radio"][value="present"]:checked').length;
            const lateCount = activeWrapper.querySelectorAll('input[type="radio"][value="late"]:checked').length;
            const absentCount = activeWrapper.querySelectorAll('input[type="radio"][value="absent"]:checked').length;
            const total = presentCount + lateCount + absentCount;

            const countPresent = document.getElementById('countPresent');
            const countLate = document.getElementById('countLate');
            const countAbsent = document.getElementById('countAbsent');
            const countTotal = document.getElementById('countTotal');

            if (countPresent) countPresent.textContent = presentCount;
            if (countLate) countLate.textContent = lateCount;
            if (countAbsent) countAbsent.textContent = absentCount;
            if (countTotal) countTotal.textContent = total;
        }

        // Initial count calculation
        document.addEventListener('DOMContentLoaded', () => {
            updateCounts();
        });

        // Quick login credentials helper
        function fillTeacherCredentials() {
            document.getElementById('modalEmail').value = 'teacher@attendance.test';
            document.getElementById('modalPassword').value = 'password';
        }

        // --- FACE RECOGNITION CAMERA TEST LOGIC ---
        let videoStream = null;
        const btnStartCamera = document.getElementById('btnStartCamera');
        const btnStopCamera = document.getElementById('btnStopCamera');
        const faceVideo = document.getElementById('faceVideo');
        const cameraIdleState = document.getElementById('cameraIdleState');
        const cameraActiveControls = document.getElementById('cameraActiveControls');
        const faceReticle = document.getElementById('faceReticle');
        const scannerBeam = document.getElementById('scannerBeam');

        if (btnStartCamera) {
            btnStartCamera.addEventListener('click', async () => {
                try {
                    videoStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        },
                        audio: false
                    });

                    faceVideo.srcObject = videoStream;
                    faceVideo.style.display = 'block';
                    cameraIdleState.style.display = 'none';
                    cameraActiveControls.classList.remove('d-none');
                    cameraActiveControls.classList.add('d-flex');
                    faceReticle.style.display = 'block';
                    scannerBeam.style.display = 'block';
                } catch (err) {
                    alert('Camera access error or permission denied: ' + err.message);
                }
            });
        }

        if (btnStopCamera) {
            btnStopCamera.addEventListener('click', () => {
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                    videoStream = null;
                }
                faceVideo.style.display = 'none';
                faceVideo.srcObject = null;
                cameraIdleState.style.display = 'block';
                cameraActiveControls.classList.add('d-none');
                cameraActiveControls.classList.remove('d-flex');
                faceReticle.style.display = 'none';
                scannerBeam.style.display = 'none';
            });
        }
    </script>
</body>
</html>
