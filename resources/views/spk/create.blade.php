@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah SPK Baru</h1>

    <form action="{{ route('spk.store') }}" method="POST">
        @csrf
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">1. Header SPK (Informasi Proyek)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- KOLOM KIRI --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No SPK</label>
                            <input type="text" name="no_spk" class="form-control" placeholder="Contoh: 001/VT/2025" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Pemesan</label>
                            <input type="text" name="nama_pemesan" class="form-control" placeholder="Nama Perusahaan / Klien" required>
                        </div>
                        <div class="form-group">
                            <label>Judul Proyek</label>
                            <input type="text" name="judul_proyek" class="form-control" placeholder="Judul Pekerjaan" required>
                        </div>
                        
                        {{-- INPUT STATUS (BARU) --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Status Awal</label>
                            <select name="status" class="form-control" required>
                                <option value="Diproses" selected>Diproses (Langsung Masuk Produksi)</option>
                                <option value="Draft">Draft (Simpan Sementara)</option>
                            </select>
                            <small class="text-muted">*Pilih 'Draft' jika data belum lengkap/belum siap dikerjakan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">2. Rincian Item Pekerjaan</h6>
                <button type="button" class="btn btn-sm btn-success" id="add-row">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="25%">Nama Barang</th>
                            <th width="50%">Rincian (Bisa Banyak Baris)</th>
                            <th width="15%">Quantity</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items-table">
                        {{-- Baris Pertama (Default) --}}
                        <tr>
                            <td class="align-top">
                                <input type="text" name="items[0][nama_barang]" class="form-control" placeholder="Nama Item" required>
                            </td>
                            <td>
                                <textarea name="items[0][rincian]" class="form-control" rows="3" placeholder="- Detail 1&#10;- Detail 2" required></textarea>
                            </td>
                            <td class="align-top">
                                <input type="number" name="items[0][quantity]" class="form-control text-center" placeholder="1" min="1" required>
                            </td>
                            <td class="align-top text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm">
                <i class="fas fa-save fa-sm text-white-50"></i> Simpan Data SPK
            </button>
        </div>
    </form>
</div>

{{-- JAVASCRIPT UNTUK MENAMBAH BARIS DINAMIS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIdx = 1;
        
        document.getElementById('add-row').addEventListener('click', function () {
            let html = `
                <tr>
                    <td class="align-top">
                        <input type="text" name="items[${rowIdx}][nama_barang]" class="form-control" placeholder="Nama Item" required>
                    </td>
                    <td>
                        <textarea name="items[${rowIdx}][rincian]" class="form-control" rows="3" placeholder="Rincian..." required></textarea>
                    </td>
                    <td class="align-top">
                        <input type="number" name="items[${rowIdx}][quantity]" class="form-control text-center" placeholder="1" min="1" required>
                    </td>
                    <td class="align-top text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            document.getElementById('items-table').insertAdjacentHTML('beforeend', html);
            rowIdx++;
        });

        document.getElementById('items-table').addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection