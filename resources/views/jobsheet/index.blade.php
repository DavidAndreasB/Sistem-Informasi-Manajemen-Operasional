@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Pilih Pekerjaan (Jobsheet)</h1>

        <div class="card shadow mb-5 border-bottom-primary">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-hammer mr-2"></i> Daftar Proyek Sedang Diproses</h6>
                <span class="badge badge-light text-primary">{{ $activeSpks->count() }} Proyek</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {{-- ID Tabel ini: dataTableAktif --}}
                    <table class="table table-bordered table-hover" id="dataTableAktif" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No SPK</th>
                                <th>Customer</th>
                                <th>Judul Proyek</th>
                                <th>Tanggal Masuk</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeSpks as $item)
                                <tr>
                                    <td class="font-weight-bold text-primary">{{ $item->no_spk }}</td>
                                    <td>{{ $item->nama_pemesan }}</td>
                                    <td>{{ $item->judul_proyek }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('jobsheet.show', $item->id) }}"
                                            class="btn btn-success btn-sm shadow-sm font-weight-bold">
                                            <i class="fas fa-tools fa-sm"></i> Kerjakan
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4 border-bottom-secondary">
            <div class="card-header py-3 bg-secondary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-history mr-2"></i> Riwayat Proyek Selesai</h6>
            </div>
            <div class="card-body">
                <p class="mb-3 small text-muted">
                    Gunakan fitur <strong>Search</strong> di sebelah kanan untuk mencari proyek lama,
                    atau klik <strong>Judul Kolom</strong> untuk mengurutkan data (Sorting).
                </p>
                <div class="table-responsive">
                    {{-- ID Tabel ini: dataTableSelesai --}}
                    <table class="table table-bordered table-striped table-sm" id="dataTableSelesai" width="100%"
                        cellspacing="0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th>No SPK</th>
                                <th>Customer</th>
                                <th>Judul Proyek</th>
                                <th>Tanggal Selesai</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finishedSpks as $item)
                                <tr class="text-muted">
                                    <td>{{ $item->no_spk }}</td>
                                    <td>{{ $item->nama_pemesan }}</td>
                                    <td>{{ $item->judul_proyek }}</td>
                                    {{-- data-sort digunakan agar sorting tanggal akurat secara sistem --}}
                                    <td data-sort="{{ $item->updated_at }}">
                                        {{ $item->updated_at->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('jobsheet.show', $item->id) }}"
                                            class="btn btn-secondary btn-sm shadow-sm">
                                            <i class="fas fa-eye fa-sm"></i> Lihat Detail
                                        </a>
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
        $(document).ready(function () {
            // Inisialisasi DataTables untuk Tabel Aktif
            $('#dataTableAktif').DataTable({
                "pageLength": 5, // Menampilkan 5 baris per halaman
                "language": {
                    "search": "Cari Proyek:",
                    "lengthMenu": "Tampilkan _MENU_ proyek",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ proyek",
                    "paginate": { "next": "Maju", "previous": "Mundur" }
                }
            });

            // Inisialisasi DataTables untuk Tabel Selesai
            $('#dataTableSelesai').DataTable({
                "order": [[3, "desc"]], // Default urutkan berdasarkan Tanggal Selesai (Kolom ke-4) secara menurun (Terbaru)
                "pageLength": 10, // Menampilkan 10 baris per halaman
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Tampilkan _MENU_ arsip",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ arsip",
                    "paginate": { "next": "Maju", "previous": "Mundur" }
                }
            });
        });
    </script>
@endpush