@extends('layouts.sbadmin')

@section('title', 'Tambah Mesin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle"></i> Tambah Mesin Baru
            </h1>
            <a href="{{ route('machines.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Mesin</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('machines.store') }}" method="POST">
                            @csrf

                            {{-- Nama Mesin --}}
                            <div class="form-group">
                                <label for="nama_mesin" class="font-weight-bold">
                                    Nama Mesin <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nama_mesin') is-invalid @enderror"
                                    id="nama_mesin" name="nama_mesin" value="{{ old('nama_mesin') }}"
                                    placeholder="Contoh: Bubut Kecil, Milling, Gerinda" required>
                                @error('nama_mesin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Nama mesin harus unik
                                </small>
                            </div>

                            {{-- Tarif --}}
                            <div class="form-group">
                                <label for="tarif" class="font-weight-bold">
                                    Tarif per Jam (Rp) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('tarif') is-invalid @enderror" id="tarif"
                                    name="tarif" value="{{ old('tarif') }}" min="0" step="1" placeholder="Contoh: 50000"
                                    required>
                                @error('tarif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Masukkan tarif dalam Rupiah (tanpa titik atau koma)
                                </small>
                            </div>

                            {{-- Buttons --}}
                            <div class="form-group mb-0 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection