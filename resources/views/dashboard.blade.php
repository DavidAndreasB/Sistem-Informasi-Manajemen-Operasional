@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Operasional</h1>
        </div>

        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total SPK Masuk</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSpk }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sedang Diproses</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $spkDiproses }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cogs fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Proyek Selesai</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $spkSelesai }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Item Pending QC</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingQc }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-6 mb-4">

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Halo, {{ Auth::user()->username }}!</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 15rem;"
                                src="{{ asset('images/logoVenus.png') }}" alt="Logo Venus Tekindo">
                        </div>
                        <p>Selamat datang di Sistem Informasi Manajemen Operasional <strong>PT. Venus Tekindo</strong>.</p>
                        <a rel="nofollow" href="{{ route('spk.create') }}">Buat SPK Baru &rarr;</a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">5 Proyek Terakhir Masuk</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No SPK</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSpk as $spk)
                                        <tr>
                                            <td><a href="{{ route('spk.show', $spk->id) }}">{{ $spk->no_spk }}</a></td>
                                            <td>{{ $spk->judul_proyek }}</td>
                                            <td>
                                                @if($spk->status == 'Diproses') <span
                                                    class="badge badge-warning">Diproses</span>
                                                @elseif($spk->status == 'Selesai') <span
                                                    class="badge badge-success">Selesai</span>
                                                @else <span class="badge badge-secondary">Draft</span> @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6 mb-4">

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aktivitas Pengerjaan Terkini (Log)</h6>
                    </div>
                    <div class="card-body">
                        @forelse($recentActivity as $job)
                            <div class="mb-3 small">
                                <div class="font-weight-bold">
                                    {{ $job->operator->username ?? 'User' }}
                                    <span class="float-right text-gray-500">{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                                <div>
                                    Mengerjakan <strong>{{ $job->jenis_pekerjaan }}</strong>
                                    pada proyek <a
                                        href="{{ route('jobsheet.show', $job->spk_id) }}">{{ $job->spk->no_spk ?? '-' }}</a>.
                                </div>
                                <hr class="my-2">
                            </div>
                        @empty
                            <p class="text-center text-muted">Belum ada aktivitas pengerjaan.</p>
                        @endforelse
                        <a href="{{ route('jobsheet.index') }}" class="btn btn-light btn-block btn-sm">Lihat Semua Aktivitas
                            &rarr;</a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Panduan Singkat</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Admin:</strong> Gunakan menu "SPK" untuk membuat perintah kerja baru.</p>
                        <p><strong>Operator:</strong> Gunakan menu "Jobsheet", pilih proyek, lalu catat jam kerja Anda.</p>
                        <p><strong>QC:</strong> Masuk ke detail SPK untuk memvalidasi (OK/Reject) item barang.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection