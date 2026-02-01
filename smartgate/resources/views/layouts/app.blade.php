<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartGate Guard Dashboard</title>
    
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo-container">
                <img src="{{ asset('images/evsu-logo.png') }}" alt="EVSU Logo">
            </div>
            <div class="brand-text">Smart<span style="color: white;">Gate</span></div>
        </div>

        <ul class="nav-links">
            @php $role = session('role'); @endphp
            
            @if($role === 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="ph ph-users"></i>
                        User Roles
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.rfid') }}" class="{{ request()->routeIs('admin.rfid') ? 'active' : '' }}">
                        <i class="ph ph-identification-card"></i>
                        RFID Monitoring
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <i class="ph ph-files"></i>
                        Reports & Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="ph ph-gear"></i>
                        System Settings
                    </a>
                </li>
            @elseif($role === 'office')
                <li class="nav-item">
                    <a href="{{ route('office.dashboard') }}" class="{{ request()->routeIs('office.dashboard') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('office.registration') }}" class="{{ request()->routeIs('office.registration') ? 'active' : '' }}">
                        <i class="ph ph-user-plus"></i>
                        Owner Registration
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('office.users') }}" class="{{ request()->routeIs('office.users') ? 'active' : '' }}">
                        <i class="ph ph-users"></i>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('office.stats') }}" class="{{ request()->routeIs('office.stats') ? 'active' : '' }}">
                        <i class="ph ph-chart-pie"></i>
                        Statistics
                    </a>
                </li>
            @elseif($role === 'guard')
                <li class="nav-item">
                    <a href="{{ route('guard.dashboard') }}" class="{{ request()->routeIs('guard.dashboard') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('guard.entry') }}" class="{{ request()->routeIs('guard.entry') ? 'active' : '' }}">
                        <i class="ph ph-sign-in"></i>
                        Visitor Entry
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('guard.exit') }}" class="{{ request()->routeIs('guard.exit') ? 'active' : '' }}">
                        <i class="ph ph-sign-out"></i>
                        Visitor Exit
                    </a>
                </li>
            @else
                <!-- Fallback or Guest links -->
            @endif
            
            <div style="flex-grow:1"></div>
            <li class="nav-item">
                <a href="{{ route('logout') }}">
                    <i class="ph ph-sign-out"></i>
                    Logout
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="page-title">
                <h1>@yield('title', 'Dashboard')</h1>
                <p>@yield('subtitle', 'Welcome back, ' . (session('role') ? ucfirst(session('role')) : 'User'))</p>
            </div>
            
            <div class="user-profile">
                <div class="avatar">{{ session('role') ? strtoupper(substr(session('role'), 0, 1)) : 'U' }}</div>
                <span>{{ session('role') ? ucfirst(session('role')) : 'User' }}</span>
                <i class="ph ph-caret-down"></i>
            </div>
        </header>

        @yield('content')

        <footer class="app-footer" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 2rem 0;">
            <p style="margin: 0; font-size: 1.1rem;">&copy; 2026 SmartGate System Developed by</p>
            <img src="{{ asset('images/chocobol-logo.png') }}" alt="Chocobol Logo" style="height: 120px; width: auto;">
        </footer>
    </main>
    @yield('scripts')
</body>

</html>
