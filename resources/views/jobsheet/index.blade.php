@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Jobsheet</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Daftar Proyek Sedang Diproses</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>No SPK</th>
                            <th>Customer</th>
                            <th>Judul Proyek</th>
                            <th>Tanggal Masuk</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spks as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold text-primary">{{ $item->no_spk }}</td>
                                <td>{{ $item->nama_pemesan }}</td>
                                <td>{{ $item->judul_proyek }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('jobsheet.show', $item->id) }}" class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-tools"></i> Kerjakan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Tidak ada SPK yang sedang diproses.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection