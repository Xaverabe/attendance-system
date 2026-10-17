<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Attendance System')</title>
    
    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active-bg: rgba(56, 189, 248, 0.12);
            --sidebar-active-text: #38bdf8;
            --sidebar-text: #94a3b8;
            --sidebar-heading: #64748b;
            --topbar-height: 64px;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Desktop Sidebar */
        .app-sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: #fff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        /* Brand & Logo */
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #fff;
        }

        .sidebar-brand:hover {
            color: #fff;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            margin-right: 0.75rem;
            box-shadow: 0 2px 10px rgba(56, 189, 248, 0.3);
        }

        /* Profile Block in Sidebar */
        .sidebar-user {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        /* Navigation Links */
        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--sidebar-heading);
            font-weight: 700;
            padding: 0.5rem 0.75rem 0.25rem 0.75rem;
            margin-top: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 0.85rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 24px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
        }

        .sidebar-link:hover i {
            color: #38bdf8;
        }

        .sidebar-link.active {
            color: var(--sidebar-active-text);
            background-color: var(--sidebar-active-bg);
            font-weight: 600;
            border-left: 3px solid #38bdf8;
        }

        .sidebar-link.active i {
            color: var(--sidebar-active-text);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.95);
        }

        /* Main Content Layout */
        .app-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Top Bar */
        .app-topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .main-content {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* Responsive handling for mobile/tablet */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            .app-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- DESKTOP LEFT SIDEBAR -->
    <aside class="app-sidebar d-none d-lg-flex">
        <!-- Brand -->
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 lh-1">Attendance</div>
                <small class="text-white-50" style="font-size: 0.72rem;">Teacher & Admin Desk</small>
            </div>
        </a>

        <!-- User Profile Card -->
        @auth
            <div class="sidebar-user d-flex align-items-center gap-3">
                @php
                    $initials = collect(explode(' ', auth()->user()->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                @endphp
                <div class="user-avatar">{{ $initials }}</div>
                <div class="overflow-hidden">
                    <div class="text-white fw-semibold text-truncate small">{{ auth()->user()->name }}</div>
                    <span class="badge bg-primary bg-opacity-25 text-info border border-primary border-opacity-50 text-uppercase" style="font-size: 0.65rem;">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
        @endauth

        <!-- Navigation Menu -->
        <div class="sidebar-nav">
            <div class="nav-section-title">Core Menu</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('welcome') }}" class="sidebar-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                <i class="bi bi-pencil-square text-info"></i>
                <span>Take Attendance</span>
                <span class="badge bg-success ms-auto" style="font-size: 0.65rem;">TODAY</span>
            </a>

            <div class="nav-section-title">Academic Management</div>
            <div class="d-flex align-items-center justify-content-between pe-2 mb-1">
                <a href="{{ route('classes.index') }}" class="sidebar-link flex-grow-1 mb-0 {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Classes & Rooms</span>
                </a>
                <a href="{{ route('classes.create') }}" class="btn btn-sm btn-outline-info rounded-circle p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 24px; height: 24px; font-size: 0.85rem;" title="Add new class">
                    <i class="bi bi-plus"></i>
                </a>
            </div>

            <div class="d-flex align-items-center justify-content-between pe-2 mb-1">
                <a href="{{ route('students.index') }}" class="sidebar-link flex-grow-1 mb-0 {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Students Directory</span>
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-sm btn-outline-info rounded-circle p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 24px; height: 24px; font-size: 0.85rem;" title="Add new student">
                    <i class="bi bi-plus"></i>
                </a>
            </div>

            <a href="{{ route('attendance.index') }}" class="sidebar-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                <i class="bi bi-calendar3-check"></i>
                <span>Attendance History</span>
            </a>

            <a href="{{ route('attendance.import') }}" class="sidebar-link {{ request()->routeIs('attendance.import') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-excel text-success"></i>
                <span>Excel Bulk Import</span>
            </a>

            <div class="nav-section-title">Smart Features</div>
            <a href="{{ route('welcome') }}#face-pane" class="sidebar-link">
                <i class="bi bi-camera-video text-warning"></i>
                <span>Face Scanner Kiosk</span>
                <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.65rem;">AI PREVIEW</span>
            </a>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <small class="text-white-50" style="font-size: 0.75rem;">
                    <i class="bi bi-clock me-1 text-info"></i> {{ now()->format('M d, Y') }}
                </small>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-pill" title="Logout">
                        <i class="bi bi-box-arrow-right me-1"></i> Exit
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MOBILE OFFCANVAS SIDEBAR (For screens < 992px) -->
    <div class="offcanvas offcanvas-start bg-dark text-white border-0" tabindex="-1" id="mobileSidebar" style="width: 280px; background-color: var(--sidebar-bg) !important;">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center">
                <div class="brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white">Smart Attendance</h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">Menu Navigation</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            @auth
                <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-3 bg-black bg-opacity-25">
                    @php
                        $initials = collect(explode(' ', auth()->user()->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                    @endphp
                    <div class="user-avatar">{{ $initials }}</div>
                    <div>
                        <div class="text-white fw-bold">{{ auth()->user()->name }}</div>
                        <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">{{ auth()->user()->role }}</span>
                    </div>
                </div>
            @endauth

            <div class="sidebar-nav p-3 flex-grow-1">
                <div class="nav-section-title">Core Menu</div>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('welcome') }}" class="sidebar-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square text-info"></i> Take Attendance
                </a>
                <div class="nav-section-title">Academic Management</div>
                <div class="d-flex align-items-center justify-content-between pe-2 mb-1">
                    <a href="{{ route('classes.index') }}" class="sidebar-link flex-grow-1 mb-0 {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i> Classes & Rooms
                    </a>
                    <a href="{{ route('classes.create') }}" class="btn btn-sm btn-outline-info rounded-circle p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 24px; height: 24px; font-size: 0.85rem;" title="Add new class">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>
                <div class="d-flex align-items-center justify-content-between pe-2 mb-1">
                    <a href="{{ route('students.index') }}" class="sidebar-link flex-grow-1 mb-0 {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Students Directory
                    </a>
                    <a href="{{ route('students.create') }}" class="btn btn-sm btn-outline-info rounded-circle p-0 d-flex align-items-center justify-content-center text-decoration-none" style="width: 24px; height: 24px; font-size: 0.85rem;" title="Add new student">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>
                <a href="{{ route('attendance.index') }}" class="sidebar-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    <i class="bi bi-calendar3-check"></i> Attendance History
                </a>
                <a href="{{ route('attendance.import') }}" class="sidebar-link {{ request()->routeIs('attendance.import') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel Bulk Import
                </a>
                <div class="nav-section-title">Smart Features</div>
                <a href="{{ route('welcome') }}#face-pane" class="sidebar-link">
                    <i class="bi bi-camera-video text-warning"></i> Face Scanner Kiosk
                </a>
            </div>

            <div class="p-3 border-top border-secondary border-opacity-25">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                        <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN APP WRAPPER -->
    <div class="app-wrapper">
        <!-- TOPBAR -->
        <header class="app-topbar shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <!-- Hamburger for Mobile -->
                <button class="btn btn-outline-secondary d-lg-none py-1 px-2 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                
                <div>
                    <h5 class="mb-0 fw-bold text-dark">@yield('page-title', 'Overview')</h5>
                    <small class="text-muted d-none d-md-inline">@yield('page-subtitle', 'Smart Attendance Management Portal')</small>
                </div>
            </div>

            <!-- Right Controls -->
            <div class="d-flex align-items-center gap-3">
                <!-- Today roll-call quick button -->
                <a href="{{ route('welcome') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle"></i>
                    <span class="d-none d-sm-inline">Mark Today's Attendance</span>
                </a>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span class="d-none d-md-inline fw-semibold small">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li class="dropdown-header">Signed in as <strong>{{ auth()->user()->role }}</strong></li>
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('welcome') }}"><i class="bi bi-pencil-square me-2"></i>Quick Attendance</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        @if (session('success'))
            <div class="container-fluid px-4 pt-3">
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container-fluid px-4 pt-3">
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="container-fluid px-4 pt-3">
                <div class="alert alert-danger border-0 shadow-sm rounded-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- MAIN VIEWPORT CONTENT -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
