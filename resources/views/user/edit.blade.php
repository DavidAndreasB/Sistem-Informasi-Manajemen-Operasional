@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengguna</h1>
        <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">Form Edit Akun: {{ $user->username }}</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Form Update --}}
                    <form method="POST" action="{{ route('user.update', $user->id) }}">
                        @csrf
                        @method('PUT') {{-- Method Spoofing untuk Update --}}

                        {{-- Username --}}
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" 
                                   name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="form-group">
                            <label for="role">Peran (Role)</label>
                            <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Super Admin</option>
                                <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>Quality Control</option>
                                <option value="3" {{ $user->role == 3 ? 'selected' : '' }}>Operator</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <div class="small text-muted mb-2">Kosongkan password jika tidak ingin mengubahnya.</div>

                        {{-- Password Baru (Opsional) --}}
                        <div class="form-group">
                            <label for="password">Password Baru (Opsional)</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" autocomplete="new-password" placeholder="Biarkan kosong jika tetap">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" type="password" class="form-control" 
                                   name="password_confirmation" placeholder="Ulangi password baru">
                        </div>

                        <button type="submit" class="btn btn-warning btn-block text-white font-weight-bold">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection