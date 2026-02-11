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

        @if ($errors->any())
            <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

        {{-- TWO COLUMN LAYOUT --}}
        <div class="row">
            {{-- LEFT COLUMN: INLINE INPUT FORMS (Hidden for QC) --}}
            @if(!Auth::user()->isQualityControl())
                <div class="col-lg-5">
                    <h5 class="mb-3 text-gray-800">
                        <i class="fas fa-edit"></i> Input Aktivitas
                    </h5>

                    @foreach($spk->items as $item)
                        <div class="card shadow mb-4 border-left-primary">
                            <div class="card-header py-2 bg-gradient-primary">
                                <h6 class="m-0 font-weight-bold text-white">
                                    <i class="fas fa-box"></i> {{ $item->nama_barang }}
                                    <span class="badge badge-light text-primary ml-2">{{ $item->quantity }} pcs</span>
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                @php
                                    // Load machines from pivot table relationship (not rincian column)
                                    $machines = $item->machines;
                                @endphp

                                @foreach($machines as $machine)
                                    <div class="border rounded p-2 mb-2" style="background-color: #f8f9fc;">
                                        <div class="font-weight-bold text-primary mb-2 small">
                                            <i class="fas fa-cog"></i> {{ $machine->nama_mesin }}
                                        </div>

                                        {{-- Check if item can be worked on --}}
                                        @php
                                            // Item bisa dikerjakan jika:
                                            // 1. SPK status Diproses DAN
                                            // 2. (Item belum selesai ATAU status QC = Reject)
                                            $canWork = $spk->status == 'Diproses' &&
                                                ($item->status_pengerjaan != 'Selesai' || $item->status_qc == 'Reject');
                                        @endphp

                                        @if($canWork)
                                            {{-- INLINE INPUT FORM --}}
                                            <form action="{{ route('jobsheet.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="spk_id" value="{{ $spk->id }}">
                                                <input type="hidden" name="spk_item_id" value="{{ $item->id }}">
                                                <input type="hidden" name="jenis_pekerjaan" value="{{ $machine->nama_mesin }}">

                                                <div class="row mb-1">
                                                    <div class="col-6">
                                                        <label class="small mb-0">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control form-control-sm"
                                                            value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small mb-0">Keterangan</label>
                                                        <input type="text" name="keterangan" class="form-control form-control-sm"
                                                            placeholder="Catatan...">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label class="small mb-0">Jam Mulai</label>
                                                        <input type="text" name="jam_mulai" class="form-control form-control-sm time-picker"
                                                            placeholder="HH:MM" readonly required>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="small mb-0">Jam Selesai</label>
                                                        <input type="text" name="jam_selesai"
                                                            class="form-control form-control-sm time-picker" placeholder="HH:MM" readonly
                                                            required>
                                                    </div>
                                                    <div class="col-4 d-flex align-items-end">
                                                        <button type="submit" class="btn btn-success btn-sm btn-block">
                                                            <i class="fas fa-save"></i> Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Show status message instead of form --}}
                                            @if($spk->status == 'Selesai')
                                                <div class="text-center text-muted small">
                                                    <i class="fas fa-check-circle text-success"></i> SPK Selesai
                                                </div>
                                            @elseif($item->status_pengerjaan == 'Selesai' && $item->status_qc != 'Reject')
                                                <div class="text-center small">
                                                    @if($item->status_qc == 'OK')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-double"></i> QC: LULUS
                                                        </span>
                                                    @elseif($item->status_qc == 'Pending')
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-hourglass-half"></i> Menunggu QC
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- RIGHT COLUMN: RIWAYAT PENGERJAAN (GROUPED BY ITEM) --}}
            <div class="{{ Auth::user()->isQualityControl() ? 'col-lg-12' : 'col-lg-7' }}">
                <h5 class="mb-3 text-gray-800">
                    <i class="fas fa-history"></i> Riwayat Pengerjaan
                </h5>

                @php
                    // Group jobsheets by spk_item_id
                    $groupedJobsheets = $spk->jobsheets->sortByDesc('created_at')->groupBy('spk_item_id');
                @endphp

                @forelse($groupedJobsheets as $itemId => $jobsheets)
                    {{-- Item Header --}}
                    <div class="mb-3">
                        @if($itemId)
                            @php
                                $item = $jobsheets->first()->spkItem;
                            @endphp
                            <div class="alert alert-light border-left-primary mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-box"></i> Item: {{ $item->nama_barang ?? 'Unknown' }}
                                        <span class="badge badge-primary ml-2">{{ $jobsheets->count() }} aktivitas</span>
                                    </h6>

                                    {{-- Selesai Kerjakan Button (Hidden for QC) --}}
                                    @if(!Auth::user()->isQualityControl() && $spk->status == 'Diproses' && ($item->status_pengerjaan != 'Selesai' || $item->status_qc == 'Reject'))
                                        <div class="d-flex align-items-center">
                                            {{-- Show reject badge if status is reject --}}
                                            @if($item->status_qc == 'Reject')
                                                <span class="badge badge-danger mr-2">
                                                    <i class="fas fa-times-circle"></i> QC: REJECT
                                                </span>
                                            @endif

                                            {{-- Show button for incomplete or rejected items --}}
                                            <form action="{{ route('item.complete', $item->id) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Apakah item {{ $item->nama_barang }} sudah benar-benar selesai dan siap dicek QC?')">
                                                    <i class="fas fa-check"></i> Selesai Kerjakan
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($item->status_pengerjaan == 'Selesai')
                                        @if($item->status_qc == 'OK')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-double"></i> QC: LULUS
                                            </span>
                                        @elseif($item->status_qc == 'Reject')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> QC: REJECT
                                            </span>
                                        @else
                                            <span class="badge badge-info">
                                                <i class="fas fa-hourglass-half"></i> Menunggu QC
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border-left-secondary mb-2">
                                <h6 class="font-weight-bold text-secondary mb-0">
                                    <i class="fas fa-tasks"></i> Aktivitas Umum (Tidak terhubung ke item spesifik)
                                    <span class="badge badge-secondary ml-2">{{ $jobsheets->count() }} aktivitas</span>
                                </h6>
                            </div>
                        @endif

                        {{-- Jobsheet Table for this Item --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
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
                                    @foreach($jobsheets as $log)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/y') }}</td>
                                            <td>
                                                <span class="font-weight-bold text-dark">{{ $log->jenis_pekerjaan }}</span><br>
                                                <span class="small text-muted">{{ $log->keterangan }}</span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                                                </div>
                                                <span class="badge badge-info">{{ number_format($log->total_jam, 2) }} Jam</span>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-info-circle"></i> Belum ada aktivitas pengerjaan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Flatpickr untuk Time Picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize time pickers for all time inputs
            flatpickr('.time-picker', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1
            });
        });
    </script>
@endpush