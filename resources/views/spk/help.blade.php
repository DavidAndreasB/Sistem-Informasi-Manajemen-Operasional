@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-question-circle"></i> Cara Mengisi Form SPK
            </h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">Petunjuk Pengisian Rincian Item</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning border-left-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> PENTING!</h5>
                    <p class="mb-0">Untuk field <strong>"Rincian"</strong>, Anda HARUS:</p>
                </div>

                <ol class="h5">
                    <li class="mb-3">
                        <strong>Pilih mesin</strong> dari dropdown
                        <br><img
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='40'%3E%3Crect fill='%23fff' stroke='%23ddd' width='250' height='38' x='0' y='0'/%3E%3Ctext x='10' y='25' font-family='Arial' font-size='14' fill='%23666'%3E-- Pilih Mesin --%3C/text%3E%3C/svg%3E"
                            class="mt-2" />
                    </li>
                    <li class="mb-3">
                        Klik tombol <span class="badge badge-success badge-lg"><i class="fas fa-plus"></i></span> (hijau)
                        untuk menambahkan rincian
                    </li>
                    <li class="mb-3">
                        Rincian yang dipilih akan muncul sebagai tag biru
                        <br><span class="badge badge-primary mt-2">Milling</span>
                        <span class="badge badge-primary">Las</span>
                    </li>
                    <li class="mb-3">
                        Ulangi langkah 1-2 untuk menambahkan rincian lain
                    </li>
                </ol>

                <div class="alert alert-danger border-left-danger mt-4">
                    <strong><i class="fas fa-times-circle"></i> Error yang Sering Terjadi:</strong>
                    <p class="mb-0">
                        Jika Anda mendapat error <code>"The items.0.rincian field is required"</code>,
                        berarti Anda belum menambahkan rincian. Pastikan sudah klik tombol
                        <span class="badge badge-success"><i class="fas fa-plus"></i></span> setelah memilih mesin!
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection