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
            <li class="nav-item {{ request()->routeIs('user.*') || request()->routeIs('register') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('user.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Manajemen User</span>
                </a>
            </li>

            {{-- MENU SIMULASI --}}
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

            <hr class="sidebar-divider">

            {{-- ================= LOGOUT DI SIDEBAR ================= --}}
            <div class="sidebar-heading">
                Akun
            </div>

            <li class="nav-item">
                {{-- PERUBAHAN DI SINI: Menghapus class 'text-danger' agar warnanya putih standar --}}
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout / Keluar</span>
                </a>
            </li>
            {{-- ===================================================== --}}

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

                        <li class="nav-item no-arrow">
                            <span class="nav-link">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->username ?? 'Pengguna' }}
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="{{ asset('sbadmin2/img/undraw_profile.svg') }}"
                                    onerror="this.src='https://source.unsplash.com/QAB-WJcbgJk/60x60'">
                            </span>
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

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda ingin mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    
                    {{-- Form Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            if ($('#dataTable').length) {
                $('#dataTable').DataTable();
            }
        });
    </script>

    @stack('scripts')

</body>

</html>