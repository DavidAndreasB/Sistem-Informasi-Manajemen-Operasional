@extends('layouts.sbadmin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Simulasi Perhitungan Biaya (Penawaran)</h1>

    <div class="row">
        
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Input Komponen Biaya</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Jenis Mesin / Pekerjaan</label>
                        <select id="inputMesin" class="form-control">
                            <option value="" disabled selected>-- Pilih Mesin --</option>
                            {{-- Loop Tarif dari Controller --}}
                            @foreach($tarifs as $mesin => $harga)
                                <option value="{{ $harga }}">{{ $mesin }} (Rp {{ number_format($harga, 0, ',', '.') }}/jam)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estimasi Durasi (Jam)</label>
                        <input type="number" id="inputJam" class="form-control" placeholder="Contoh: 2.5" step="0.1">
                    </div>

                    <button type="button" class="btn btn-info btn-block" onclick="tambahBaris()">
                        <i class="fas fa-plus-circle"></i> Tambahkan ke Tabel
                    </button>
                    
                    <hr>
                    <small class="text-muted">
                        *Fitur ini hanya simulasi dan tidak menyimpan data ke database sistem.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Rincian Estimasi Penawaran</h6>
                    <button class="btn btn-sm btn-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak / PDF
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Uraian Pekerjaan</th>
                                    <th class="text-center">Tarif / Jam</th>
                                    <th class="text-center">Durasi (Jam)</th>
                                    <th class="text-right">Subtotal (Rp)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelSimulasi">
                                {{-- Data akan masuk di sini via JS --}}
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right text-uppercase">Total Estimasi Biaya:</td>
                                    <td class="text-right text-success" id="totalBiaya">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <button class="btn btn-danger btn-sm mt-3" onclick="resetTabel()">
                        <i class="fas fa-trash"></i> Reset Tabel
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- JAVASCRIPT KALKULATOR --}}
<script>
    let total = 0;

    function tambahBaris() {
        // Ambil nilai dari input
        let selectMesin = document.getElementById("inputMesin");
        let jam = parseFloat(document.getElementById("inputJam").value);

        // Validasi sederhana
        if (selectMesin.selectedIndex === 0 || isNaN(jam) || jam <= 0) {
            alert("Mohon pilih mesin dan masukkan durasi jam yang valid.");
            return;
        }

        let namaMesin = selectMesin.options[selectMesin.selectedIndex].text.split(' (')[0];
        let hargaPerJam = parseFloat(selectMesin.value);
        let subtotal = hargaPerJam * jam;

        // Tambah baris ke tabel HTML
        let table = document.getElementById("tabelSimulasi");
        let row = table.insertRow();
        
        row.innerHTML = `
            <td>${namaMesin}</td>
            <td class="text-center">Rp ${new Intl.NumberFormat('id-ID').format(hargaPerJam)}</td>
            <td class="text-center">${jam}</td>
            <td class="text-right font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm btn-circle" onclick="hapusBaris(this, ${subtotal})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

        // Update Total
        updateTotal(subtotal);

        // Reset Input
        document.getElementById("inputJam").value = "";
        selectMesin.selectedIndex = 0;
    }

    function hapusBaris(btn, subtotal) {
        let row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateTotal(-subtotal); // Kurangi total
    }

    function updateTotal(nilai) {
        total += nilai;
        document.getElementById("totalBiaya").innerText = "Rp " + new Intl.NumberFormat('id-ID').format(total);
    }

    function resetTabel() {
        document.getElementById("tabelSimulasi").innerHTML = "";
        total = 0;
        updateTotal(0);
    }
</script>
@endsection