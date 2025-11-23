@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengguna</h1>
        <a href="{{ route('register') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah User Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun Sistem</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Username</th>
                            <th>Peran (Role)</th>
                            <th>Dibuat Pada</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $user->username }}</td>
                            <td>
                                @if ($user->isSuperAdmin())
                                    <span class="badge badge-danger px-2 py-1">Super Admin</span>
                                @elseif ($user->isQualityControl())
                                    <span class="badge badge-warning px-2 py-1">Quality Control</span>
                                @else
                                    <span class="badge badge-info px-2 py-1">Operator</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- Tombol Hapus (Proteksi: Tidak bisa hapus diri sendiri) --}}
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        {{-- KONFIRMASI MENGGUNAKAN ONCLICK JAVASCRIPT --}}
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus user {{ $user->username }}? Tindakan ini tidak dapat dibatalkan.');" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Tidak bisa hapus akun sendiri">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#dataTable').DataTable();
    });
</script>
@endpush