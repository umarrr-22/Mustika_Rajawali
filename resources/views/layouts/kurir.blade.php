<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Mustika Rajawali</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            padding-top: var(--navbar-height);
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            transition: transform 0.3s ease;
            z-index: 1030;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-brand {
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            border-left: 4px solid var(--accent-color);
        }
        
        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - var(--navbar-height));
            transition: all 0.3s;
            position: relative;
        }
        
        .content-container {
            padding: 20px;
        }
        
        /* Navbar Styles */
        .top-navbar {
            height: var(--navbar-height);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 1040;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            display: flex;
            justify-content: space-between;
            padding: 0 15px;
        }
        
        /* Account Section - Right Aligned */
        .account-container {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .account-toggle {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .account-toggle:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }
        
        .account-avatar {
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
        
        .account-name {
            font-weight: 500;
            margin-right: 5px;
        }
        
        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 5px 0;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.9rem;
            color: var(--primary-color);
        }
        
        .dropdown-item:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }
        
        .logout-btn {
            color: #dc3545;
        }
        
        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.05);
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .top-navbar {
                left: 0;
                z-index: 1051;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .account-name {
                max-width: 120px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            /* Mobile dropdown fix */
            .dropdown-menu {
                position: fixed !important;
                top: var(--navbar-height) !important;
                right: 10px !important;
                width: auto !important;
                min-width: 200px !important;
            }
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
                    <a class="nav-link @if(Request::is('kurir/dashboard')) active @endif" href="{{ route('kurir.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Request::is('kurir/jadwal-hari-ini')) active @endif" href="{{ route('kurir.jadwal-hari-ini') }}">
                        <i class="fas fa-calendar-day"></i> Jadwal Hari Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Request::is('kurir/service-masuk')) active @endif" href="{{ route('kurir.service-masuk') }}">
                        <i class="fas fa-tools"></i> Service Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Request::is('kurir/refil-masuk')) active @endif" href="{{ route('kurir.refil-masuk') }}">
                        <i class="fas fa-gas-pump"></i> Refil Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(Request::is('kurir/jadwal-selesai')) active @endif" href="{{ route('kurir.jadwal-selesai') }}">
                        <i class="fas fa-check-circle"></i> Jadwal Selesai
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Top Navbar -->
    <nav class="top-navbar">
        <button class="btn btn-link d-md-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="account-container">
            <div class="dropdown">
                <a class="account-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="account-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="account-name">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-container">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            // Toggle sidebar on mobile
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        });
    </script>
</body>
</html>