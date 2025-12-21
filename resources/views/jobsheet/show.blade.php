@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">

        {{-- Header Halaman --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Jobsheet: {{ $spk->no_spk }}
                @if($spk->status == 'Selesai')
                    <span class="badge badge-success ml-2" style="font-size: 0.6em;">SELESAI (ARSIP)</span>
                @endif
            </h1>
            <a href="{{ route('jobsheet.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Pesan Sukses/Error --}}
        @if (session('success'))
            <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
        @endif

        {{-- Info Proyek --}}
        <div class="card shadow mb-4 border-left-{{ $spk->status == 'Selesai' ? 'success' : 'primary' }}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div
                            class="text-xs font-weight-bold text-uppercase mb-1 {{ $spk->status == 'Selesai' ? 'text-success' : 'text-primary' }}">
                            Informasi Proyek
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $spk->judul_proyek }}</div>
                        <div class="text-gray-600">{{ $spk->nama_pemesan }}</div>
                    </div>
                    <div class="col-md-6 text-md-right mt-3 mt-md-0">
                        @if(auth()->user()->isSuperAdmin())
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Biaya Produksi</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($spk->jobsheets->sum('biaya'), 0, ',', '.') }}
                            </div>
                            <small>Total Jam Kerja: {{ $spk->jobsheets->sum('total_jam') }} Jam</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- LOGIKA TAMPILAN: Jika Selesai, Form Hilang --}}
            @if($spk->status == 'Diproses')

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-gradient-primary">
                            <h6 class="m-0 font-weight-bold text-white">Input Aktivitas Harian</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('jobsheet.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="spk_id" value="{{ $spk->id }}">

                                <div class="form-group">
                                    <label class="small font-weight-bold">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold">Mesin / Pekerjaan</label>
                                    <select name="jenis_pekerjaan" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Mesin --</option>
                                        @foreach(\App\Models\JobSheet::TARIF_MESIN as $mesin => $tarif)
                                            <option value="{{ $mesin }}">{{ $mesin }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label class="small font-weight-bold">Mulai</label>
                                        <input type="text" name="jam_mulai" class="form-control" placeholder="HH:MM" required
                                            readonly>
                                    </div>
                                    <div class="form-group col-6">
                                        <label class="small font-weight-bold">Selesai</label>
                                        <input type="text" name="jam_selesai" class="form-control" placeholder="HH:MM" required
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold">Keterangan</label>
                                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save fa-sm"></i> Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">

            @else

                    <div class="col-lg-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-lock"></i> Proyek ini telah selesai. Data hanya dapat <strong>Read-Only</strong>.
                        </div>

                @endif

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengerjaan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Aktivitas</th>
                                            <th>Durasi</th>
                                            <th>Operator</th>
                                            {{-- Aksi hanya muncul jika Admin DAN status Diproses --}}
                                            @if(auth()->user()->isSuperAdmin() && $spk->status == 'Diproses')
                                                <th width="10%">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($spk->jobsheets->sortByDesc('created_at') as $log)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/y') }}</td>
                                                <td>
                                                    <span
                                                        class="font-weight-bold text-dark">{{ $log->jenis_pekerjaan }}</span><br>
                                                    <span class="small text-muted">{{ $log->keterangan }}</span>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                                                    </div>
                                                    <span class="badge badge-info">{{ number_format($log->total_jam, 1) }}
                                                        Jam</span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user-circle text-gray-400"></i>
                                                    {{ $log->operator->username ?? 'User' }}
                                                </td>

                                                @if(auth()->user()->isSuperAdmin() && $spk->status == 'Diproses')
                                                    <td class="text-center">
                                                        <form action="{{ route('jobsheet.destroy', $log->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Hapus riwayat ini?')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    Belum ada aktivitas pengerjaan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
@endsection

    @push('scripts')
        <!-- Tambahkan Flatpickr CSS dan JS untuk time picker format 24 jam -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            // Inisialisasi Flatpickr untuk input waktu dengan format 24 jam
            document.addEventListener('DOMContentLoaded', function () {
                // Konfigurasi untuk jam mulai
                flatpickr("input[name='jam_mulai']", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",  // Format 24 jam (HH:MM)
                    time_24hr: true,    // Memaksa format 24 jam
                    minuteIncrement: 1,
                    defaultHour: 8,     // Default jam 8 pagi
                    defaultMinute: 0
                });

                // Konfigurasi untuk jam selesai
                flatpickr("input[name='jam_selesai']", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",  // Format 24 jam (HH:MM)
                    time_24hr: true,    // Memaksa format 24 jam
                    minuteIncrement: 1,
                    defaultHour: 17,    // Default jam 5 sore
                    defaultMinute: 0
                });
            });
        </script>
    @endpush