@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Surat Perintah Kerja</h1>
            <div>
                <a href="{{ route('spk.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
                </a>
                {{-- Tombol Download PDF --}}
                <a href="{{ route('spk.pdf', $spk->id) }}" class="btn btn-primary btn-sm shadow-sm" target="_blank">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Cetak SPK (PDF)
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">No. SPK: {{ $spk->no_spk }}</h6>
                <span
                    class="badge badge-{{ $spk->status == 'Selesai' ? 'success' : ($spk->status == 'Diproses' ? 'warning' : 'secondary') }} px-3 py-2">
                    Status: {{ $spk->status }}
                </span>
            </div>
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th style="width: 30%">Tanggal</th>
                                <td>: {{ \Carbon\Carbon::parse($spk->tanggal)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Nama Pemesan</th>
                                <td>: {{ $spk->client_inisial }}</td>
                            </tr>
                            <tr>
                                <th>Judul Proyek</th>
                                <td>: <strong>{{ $spk->judul_proyek }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th style="width: 30%">Dibuat Oleh</th>
                                <td>: {{ $spk->creator->username ?? 'Admin' }}</td>
                            </tr>
                            <tr>
                                <th>Waktu Input</th>
                                <td>: {{ $spk->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%" class="text-center">No</th>
                                <th style="width: 25%">Nama Barang</th>
                                <th style="width: 35%">Rincian Spesifikasi</th>
                                <th style="width: 10%" class="text-center">Qty</th>
                                <th style="width: 25%" class="text-center">Status Quality Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spk->items as $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold align-middle">{{ $item->nama_barang }}</td>
                                    <td class="align-middle">
                                        @if($item->machines->count() > 0)
                                            @foreach($item->machines as $machine)
                                                <div class="mb-1">• {{ $machine->nama_mesin }}</div>
                                            @endforeach
                                        @elseif($item->rincian)
                                            @php
                                                $rincianLines = array_filter(explode("\n", $item->rincian));
                                            @endphp
                                            @foreach($rincianLines as $rincian)
                                                <div class="mb-1">
                                                    • {{ trim($rincian) }}
                                                </div>
                                            @endforeach
                                        @else
                                            <em class="text-muted">-</em>
                                        @endif
                                    </td>
                                    <td class="text-center font-weight-bold align-middle">{{ $item->quantity }}</td>

                                    {{-- KOLOM STATUS & AKSI --}}
                                    <td class="p-3">

                                        {{-- SKENARIO 1: OPERATOR BELUM SELESAI --}}
                                        @if($item->status_pengerjaan == 'Proses')

                                            {{-- Tampilan untuk Operator: Tombol Selesai --}}
                                            @if(!auth()->user()->isQualityControl())
                                                <div class="text-center">
                                                    <span class="badge badge-warning mb-2">Sedang Dikerjakan</span>
                                                    <form action="{{ route('item.complete', $item->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm btn-block"
                                                            onclick="return confirm('Apakah barang ini sudah benar-benar selesai dan siap dicek QC?')">
                                                            <i class="fas fa-check"></i> Selesai Dikerjakan
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- Tampilan untuk QC: Info Menunggu --}}
                                            @else
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-hard-hat fa-2x mb-2"></i><br>
                                                    <small class="font-weight-bold">Menunggu Operator<br>Menyelesaikan Barang</small>
                                                </div>
                                            @endif

                                            {{-- SKENARIO 2: OPERATOR SUDAH SELESAI (Barang Siap QC) --}}
                                        @else

                                            {{-- Tampilkan Status QC Saat Ini --}}
                                            <div class="text-center mb-2">
                                                @if($item->status_qc == 'OK')
                                                    <span class="badge badge-success px-3 py-2 w-100"><i
                                                            class="fas fa-check-circle"></i> QC: LULUS (OK)</span>
                                                @elseif($item->status_qc == 'Reject')
                                                    <span class="badge badge-danger px-3 py-2 w-100"><i class="fas fa-times-circle"></i>
                                                        QC: REJECT</span>
                                                    @if($item->catatan_qc)
                                                    <div class="small text-danger mt-1">Note: {{ $item->catatan_qc }}</div>@endif
                                                @else
                                                    <span class="badge badge-info px-3 py-2 w-100"><i class="fas fa-hourglass-half"></i>
                                                        Menunggu QC</span>
                                                @endif
                                            </div>

                                            {{-- AREA TOMBOL AKSI (Tergantung Role) --}}

                                            {{-- Jika QC / Admin: Tampilkan Form QC (hanya jika status BUKAN OK) --}}
                                            @if(auth()->user()->isQualityControl() || auth()->user()->isSuperAdmin())

                                                @if($item->status_qc != 'OK')
                                                    {{-- Tombol Update QC (hanya jika belum Lulus) --}}
                                                    <button class="btn btn-sm btn-outline-primary btn-block" type="button"
                                                        data-toggle="collapse" data-target="#qcForm{{ $item->id }}">
                                                        <i class="fas fa-edit"></i> Update QC
                                                    </button>

                                                    <div class="collapse mt-2" id="qcForm{{ $item->id }}">
                                                        <div class="card card-body p-2 bg-light border-0">
                                                            <form action="{{ route('qc.update', $item->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="form-group mb-2">
                                                                    <select name="status_qc" class="form-control form-control-sm">
                                                                        <option value="OK">Lulus (OK)</option>
                                                                        <option value="Reject">Reject (NG)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group mb-2">
                                                                    <input type="text" name="catatan_qc"
                                                                        class="form-control form-control-sm" placeholder="Catatan..."
                                                                        value="{{ $item->catatan_qc }}">
                                                                </div>
                                                                <button type="submit" class="btn btn-primary btn-sm btn-block">Simpan
                                                                    QC</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- Jika sudah OK, tampilkan pesan bahwa status sudah final --}}
                                                    <div class="text-center">
                                                        <small class="text-success">
                                                            <i class="fas fa-lock"></i> Status sudah final (Lulus)
                                                        </small>
                                                    </div>
                                                @endif

                                                {{-- Jika Operator: Tombol Batal (Undo) hanya jika QC belum memeriksa --}}
                                            @elseif($item->status_qc == 'Pending')
                                                <form action="{{ route('item.undo', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-link btn-sm text-secondary text-decoration-none">
                                                        <i class="fas fa-undo"></i> Batal Selesai
                                                    </button>
                                                </form>
                                            @endif

                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada item rincian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection