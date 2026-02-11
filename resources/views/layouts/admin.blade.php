<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Mustika Rajawali')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
            --navbar-height: 56px;
            --primary-color: #2c3e50;
            --secondary-color: #1a2b3c;
            --accent-color: #4e73df;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-brand {
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 1rem 0;
            overflow-y: auto;
            height: calc(100vh - var(--navbar-height));
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }

        .nav-link.active {
            border-left: 4px solid var(--accent-color);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }

        .content-container {
            padding: 20px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        .top-navbar {
            height: var(--navbar-height);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
        }

        .dashboard-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background-color: #f8f9fa;
            font-weight: 600;
        }

        /* Account Dropdown Styles */
        .account-dropdown .dropdown-toggle {
            color: var(--primary-color);
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .account-dropdown .dropdown-toggle:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }

        .account-dropdown .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(44, 62, 80, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            color: var(--primary-color);
        }

        .account-dropdown .user-avatar i {
            margin: 0; /* Remove any margin from the icon */
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .account-dropdown .user-name {
            font-weight: 500;
            margin-right: 5px;
        }

        .account-dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 0;
            min-width: 200px;
        }

        .account-dropdown .dropdown-item {
            padding: 8px 16px;
            font-size: 0.9rem;
            color: var(--primary-color);
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .account-dropdown .dropdown-item:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }

        .account-dropdown .logout-btn {
            color: #dc3545;
        }

        .account-dropdown .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.05);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content, .top-navbar {
                margin-left: 0;
                left: 0;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 5px 10px;
        }

        /* Modal customization */
        .modal-content {
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h5 class="mb-0 text-white">Mustika Rajawali</h5>
    </div>
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/tambah-jadwal')) active @endif" href="{{ route('admin.tambah-jadwal') }}">
                    <i class="fas fa-fw fa-calendar-plus"></i> Tambah Jadwal
                </a>
            </li>
            <!-- Service Menu -->
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/service-masuk')) active @endif" href="{{ route('admin.service-masuk') }}">
                    <i class="fas fa-fw fa-truck-arrow-right"></i> Service Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/service-proses')) active @endif" href="{{ route('admin.service-proses') }}">
                    <i class="fas fa-fw fa-tools"></i> Service Proses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/service-selesai')) active @endif" href="{{ route('admin.service-selesai') }}">
                    <i class="fas fa-fw fa-check-circle"></i> Service Selesai
                </a>
            </li>
            
            <!-- Refil Menu -->
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/refil-masuk')) active @endif" href="{{ route('admin.refil-masuk') }}">
                    <i class="fas fa-fw fa-truck-arrow-right"></i> Refil Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/refil-proses')) active @endif" href="{{ route('admin.refil-proses') }}">
                    <i class="fas fa-fw fa-hourglass-half"></i> Refil Proses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/refil-selesai')) active @endif" href="{{ route('admin.refil-selesai') }}">
                    <i class="fas fa-fw fa-gas-pump"></i> Refil Selesai
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/jadwal-selesai')) active @endif" href="{{ route('admin.jadwal-selesai') }}">
                    <i class="fas fa-fw fa-clipboard-check"></i> Jadwal Selesai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::is('admin/pengaturan-akun')) active @endif" href="{{ route('admin.pengaturan-akun') }}">
                    <i class="fas fa-fw fa-cog"></i> Pengaturan Akun
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Top Navbar -->
<nav class="top-navbar navbar navbar-expand navbar-light bg-white">
    <div class="container-fluid">
        <button class="btn btn-link d-md-none me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item dropdown account-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="main-content">
    <div class="content-container fade-in">
        @yield('content')
    </div>
</main>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Core JavaScript -->
<script>
    // Sidebar Toggle
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Auto close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (window.innerWidth <= 768 && 
            !sidebar.contains(event.target) && 
            event.target !== toggleBtn && 
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });

    // Initialize DataTables if table exists
    document.addEventListener('DOMContentLoaded', function() {
        if ($.fn.DataTable && $('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                }
            });
        }
    });

    // Initialize tooltips
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>

@yield('scripts') <!-- Page-specific scripts will be injected here -->
</body>
</html>