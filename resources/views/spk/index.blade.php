@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Daftar SPK</h1>
    <p class="mb-4">Manajemen data Surat Perintah Kerja (SPK) Proyek.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data SPK</h6>
            
            {{-- Tombol Tambah (Hanya Admin) --}}
            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('spk.create') }}" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Buat SPK Baru
            </a>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>No SPK</th>
                            <th>Nama Pemesan</th>
                            <th>Judul Proyek</th>
                            {{-- Kolom 'Jml Item' DIHAPUS di sini --}}
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spk as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $item->no_spk }}</td>
                                <td>{{ $item->nama_pemesan }}</td>
                                <td>{{ $item->judul_proyek }}</td>
                                {{-- Kolom data 'Jml Item' DIHAPUS di sini --}}
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                <td>
                                    @if($item->status == 'Diproses')
                                        <span class="badge badge-warning">Diproses</span>
                                    @elseif($item->status == 'Selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('spk.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat Detail Surat">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                    {{-- Tombol Edit/Hapus (Hanya Admin) --}}
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('spk.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('spk.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus SPK ini? Seluruh item di dalamnya juga akan terhapus.')" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Colspan disesuaikan menjadi 7 karena 1 kolom dihapus --}}
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                    Belum ada data SPK. 
                                </td>
                            </tr>
                        @endforelse
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
        $('#dataTable').DataTable();
    });
</script>
@endpush