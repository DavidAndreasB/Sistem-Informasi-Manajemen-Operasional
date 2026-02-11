@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah SPK Baru</h1>
            <a href="{{ route('spk.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

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
                                <label>No SPK <span class="badge badge-info">Auto</span></label>
                                <input type="text" name="no_spk" class="form-control bg-light" value="{{ $nextSpkNumber }}"
                                    readonly required>
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Nomor SPK dibuat otomatis dan
                                    akan bertambah secara berurutan</small>
                            </div>
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        {{-- KOLOM KANAN --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Pemesan / Client</label>
                                <select name="client_id" class="form-control" required>
                                    <option value="">-- Pilih Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            {{ $client->nama_lengkap }} ({{ $client->inisial }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Pilih dari daftar client yang terdaftar
                                </small>
                            </div>
                            <div class="form-group">
                                <label>Judul Proyek</label>
                                <input type="text" name="judul_proyek" class="form-control" placeholder="Judul Pekerjaan"
                                    required>
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
                                <th width="50%">Rincian</th>
                                <th width="15%">Quantity</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="items-table">
                            {{-- Baris Pertama (Default) --}}
                            <tr>
                                <td class="align-top">
                                    <input type="text" name="items[0][nama_barang]" class="form-control"
                                        placeholder="Nama Item" required>
                                </td>
                                <td>
                                    <!-- Dropdown + Add Button -->
                                    <div class="d-flex mb-2">
                                        <select class="form-control form-control-sm rincian-dropdown" data-index="0"
                                            style="flex:1;">
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($machines as $machine)
                                                <option value="{{ $machine->nama_mesin }}">
                                                    {{ $machine->nama_mesin }}
                                                </option>
                                            @endforeach
                                            <option value="Lainnya">Lainnya (Custom)</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-success ml-1 add-rincian-btn"
                                            data-index="0">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <!-- Custom textarea (hidden by default) -->
                                    <textarea class="form-control form-control-sm mb-2 rincian-custom-input" data-index="0"
                                        style="display:none;" rows="2" placeholder="Ketik rincian custom..."></textarea>

                                    <!-- Selected rincian list -->
                                    <div class="rincian-list" data-index="0" style="min-height:20px;">
                                        <!-- Tags will be inserted here -->
                                    </div>

                                    <!-- Hidden input stores machine names (comma-separated) -->
                                    <input type="hidden" name="items[0][rincian]" class="rincian-value">
                                </td>
                                <td class="align-top">
                                    <input type="number" name="items[0][quantity]" class="form-control text-center"
                                        placeholder="1" min="1" required>
                                </td>
                                <td class="align-top text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i
                                            class="fas fa-trash"></i></button>
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

    {{-- JAVASCRIPT UNTUK MENAMBAH BARIS DINAMIS DAN HANDLE DROPDOWN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let rowIdx = 1;

            // Machine options untuk dropdown dinamis
            const machineOptions = `
                                                            <option value="">-- Pilih Mesin --</option>
                                                            @foreach($machines as $machine)
                                                                <option value="{{ $machine->nama_mesin }}">{{ $machine->nama_mesin }}</option>
                                                            @endforeach
                                                            <option value="Lainnya">Lainnya (Custom)</option>
                                                        `;

            // ========================================================
            // MULTI-SELECT RINCIAN FUNCTIONS  
            // ========================================================

            // Add rincian to list
            function addRincianToList(index, value) {
                if (!value || value.trim() === '') return;

                const listDiv = document.querySelector(`.rincian-list[data-index="${index}"]`);

                // Check if already exists
                const existing = Array.from(listDiv.querySelectorAll('.rincian-tag')).find(
                    tag => tag.dataset.value === value
                );
                if (existing) {
                    alert('Rincian ini sudah ditambahkan');
                    return;
                }

                // Create tag element
                const tag = document.createElement('span');
                tag.className = 'badge badge-primary mr-1 mb-1 rincian-tag';
                tag.dataset.value = value;
                tag.style.fontSize = '0.85rem';
                tag.innerHTML = `
                                                    ${value}
                                                    <button type="button" class="btn btn-link btn-sm p-0 ml-1 text-white remove-rincian-tag" 
                                                        style="font-size:1.1rem; line-height:1; vertical-align:middle;">
                                                        ×
                                                    </button>
                                                `;

                listDiv.appendChild(tag);
                updateRincianHiddenInput(index);
            }

            // Remove rincian from list
            function removeRincianFromList(tagElement) {
                const listDiv = tagElement.closest('.rincian-list');
                const index = listDiv.dataset.index;
                tagElement.remove();
                updateRincianHiddenInput(index);
            }

            // Update hidden input with all selected rincian
            function updateRincianHiddenInput(index) {
                const listDiv = document.querySelector(`.rincian-list[data-index="${index}"]`);
                const hiddenInput = listDiv.parentElement.querySelector('.rincian-value');

                const values = Array.from(listDiv.querySelectorAll('.rincian-tag'))
                    .map(tag => tag.dataset.value);

                hiddenInput.value = values.join('\n');
            }

            // Handle dropdown change (show/hide custom textarea)
            function handleDropdownChange(selectElement) {
                const index = selectElement.dataset.index;
                const customTextarea = document.querySelector(`.rincian-custom-input[data-index="${index}"]`);

                if (selectElement.value === 'Lainnya') {
                    customTextarea.style.display = 'block';
                    customTextarea.focus();
                } else {
                    customTextarea.style.display = 'none';
                    customTextarea.value = '';
                }
            }

            // ========================================================
            // EVENT HANDLERS
            // ========================================================

            // Event delegation: Click events
            document.getElementById('items-table').addEventListener('click', function (e) {
                // Add rincian button
                if (e.target.closest('.add-rincian-btn')) {
                    const button = e.target.closest('.add-rincian-btn');
                    const index = button.dataset.index;
                    const dropdown = document.querySelector(`.rincian-dropdown[data-index="${index}"]`);
                    const customTextarea = document.querySelector(`.rincian-custom-input[data-index="${index}"]`);

                    let value = '';
                    if (dropdown.value === 'Lainnya') {
                        value = customTextarea.value.trim();
                        if (!value) {
                            alert('Silakan ketik rincian custom terlebih dahulu');
                            return;
                        }
                        customTextarea.value = '';
                        customTextarea.style.display = 'none';
                        dropdown.value = '';
                    } else {
                        value = dropdown.value; // Machine name
                        if (!value) {
                            alert('Silakan pilih mesin terlebih dahulu');
                            return;
                        }
                        dropdown.value = '';
                    }

                    addRincianToList(index, value);
                }

                // Remove rincian tag
                if (e.target.closest('.remove-rincian-tag')) {
                    const tagElement = e.target.closest('.rincian-tag');
                    removeRincianFromList(tagElement);
                }

                // Remove row
                if (e.target.closest('.remove-row')) {
                    e.target.closest('tr').remove();
                }
            });

            // Event delegation: Dropdown change
            document.getElementById('items-table').addEventListener('change', function (e) {
                if (e.target.classList.contains('rincian-dropdown')) {
                    handleDropdownChange(e.target);
                }
            });


            // Add Row Button
            document.getElementById('add-row').addEventListener('click', function () {
                let html = `
                                                <tr>
                                                    <td class="align-top">
                                                        <input type="text" name="items[${rowIdx}][nama_barang]" class="form-control" placeholder="Nama Item" required>
                                                    </td>
                                                    <td>
                                                        <!-- Dropdown + Add Button -->
                                                        <div class="d-flex mb-2">
                                                            <select class="form-control form-control-sm rincian-dropdown" data-index="${rowIdx}" style="flex:1;">
                                                                ${machineOptions}
                                                            </select>
                                                            <button type="button" class="btn btn-sm btn-success ml-1 add-rincian-btn" data-index="${rowIdx}">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>

                                                        <!-- Custom textarea (hidden by default) -->
                                                        <textarea class="form-control form-control-sm mb-2 rincian-custom-input" data-index="${rowIdx}"
                                                            style="display:none;" rows="2" placeholder="Ketik rincian custom..."></textarea>

                                                        <!-- Selected rincian list -->
                                                        <div class="rincian-list" data-index="${rowIdx}" style="min-height:20px;"></div>

                                                        <!-- Hidden input stores final value -->
                                                        <input type="hidden" name="items[${rowIdx}][rincian]" class="rincian-value">
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

            // Remove Row Button
            document.getElementById('items-table').addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endsection