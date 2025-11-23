<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Venus Tekindo') }}</title>

    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <link href="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Venus Tekindo</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Menu Utama
            </div>
            
                @if(auth()->user() && auth()->user()->isSuperAdmin())
            {{-- MENU MANAJEMEN USER --}}
            <li class="nav-item {{ request()->routeIs('user.*') || request()->routeIs('register') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('user.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Manajemen User</span>
                </a>
            </li>

            {{-- MENU BARU: SIMULASI --}}
            <li class="nav-item {{ request()->routeIs('simulasi.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('simulasi.index') }}">
                    <i class="fas fa-fw fa-calculator"></i>
                    <span>Simulasi Harga</span>
                </a>
            </li>

            @endif
            <li class="nav-item {{ request()->routeIs('spk.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('spk.index') }}">
                    <i class="fas fa-fw fa-file-contract"></i>
                    <span>SPK</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('jobsheet.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jobsheet.index') }}">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Jobsheet</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->username ?? 'Pengguna' }}
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('sbadmin2/img/undraw_profile.svg') }}"
                                    onerror="this.src='https://source.unsplash.com/QAB-WJcbgJk/60x60'">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="#" 
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                @yield('content')
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Venus Tekindo {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables jika ada elemen dengan id 'dataTable'
            if ($('#dataTable').length) {
                $('#dataTable').DataTable();
            }
        });
    </script>

</body>

</html>