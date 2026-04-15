@extends('layouts.sbadmin')

@section('title', 'Edit Client')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit"></i> Edit Client
            </h1>
            <a href="{{ route('clients.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Edit Client</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('clients.update', $client->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Nama Lengkap --}}
                            <div class="form-group">
                                <label for="nama_lengkap" class="font-weight-bold">
                                    Nama Lengkap Perusahaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    id="nama_lengkap" name="nama_lengkap"
                                    value="{{ old('nama_lengkap', $client->nama_lengkap) }}"
                                    placeholder="Contoh: PT. Maju Bersama" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Nama lengkap perusahaan harus unik
                                </small>
                            </div>

                            {{-- Inisial --}}
                            <div class="form-group">
                                <label for="inisial" class="font-weight-bold">
                                    Inisial <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('inisial') is-invalid @enderror" id="inisial"
                                    name="inisial" value="{{ old('inisial', $client->inisial) }}"
                                    placeholder="Contoh: PT. PM" required>
                                @error('inisial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Singkatan dari nama perusahaan
                                </small>
                            </div>

                            {{-- Buttons --}}
                            <div class="form-group mb-0 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
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