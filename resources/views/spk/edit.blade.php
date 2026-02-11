@extends('layouts.sbadmin')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Edit SPK</h1>
            <a href="{{ route('spk.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        <form action="{{ route('spk.update', $spk->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">1. Edit Informasi Proyek (Header)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No SPK</label>
                                <input type="text" name="no_spk" class="form-control"
                                    value="{{ old('no_spk', $spk->no_spk) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal', $spk->tanggal) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Pemesan / Client</label>
                                <select name="client_id" class="form-control" required>
                                    <option value="">-- Pilih Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $spk->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->nama_lengkap }} ({{ $client->inisial }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Judul Proyek</label>
                                <input type="text" name="judul_proyek" class="form-control"
                                    value="{{ old('judul_proyek', $spk->judul_proyek) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">2. Edit Rincian Item</h6>
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
                            @foreach ($spk->items as $index => $item)
                                <tr class="item-row">
                                    <td class="align-top">
                                        <input type="text" name="items[{{ $index }}][nama_barang]" class="form-control"
                                            value="{{ $item->nama_barang }}" required>
                                    </td>
                                    <td>
                                        {{-- Dropdown + Add Button --}}
                                        <div class="d-flex mb-2">
                                            <select class="form-control form-control-sm rincian-dropdown"
                                                data-index="{{ $index }}" style="flex:1;">
                                                <option value="">-- Pilih Mesin --</option>
                                                @foreach($machines as $machine)
                                                    <option value="{{ $machine->nama_mesin }}">{{ $machine->nama_mesin }}</option>
                                                @endforeach
                                                <option value="Lainnya">Lainnya (Custom)</option>
                                            </select>
                                            <button type="button" class="btn btn-sm btn-success ml-1 add-rincian-btn"
                                                data-index="{{ $index }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>

                                        {{-- Custom textarea (hidden by default) --}}
                                        <textarea class="form-control form-control-sm mb-2 rincian-custom-input"
                                            data-index="{{ $index }}" style="display:none;" rows="2"
                                            placeholder="Ketik rincian custom..."></textarea>

                                        {{-- Selected rincian list --}}
                                        <div class="rincian-list" data-index="{{ $index }}" style="min-height:20px;">
                                            {{-- Pre-populate existing rincian as tags --}}
                                            @php
                                                $existingRincian = array_filter(explode("\n", $item->rincian));
                                            @endphp
                                            @foreach($existingRincian as $rin)
                                                <span class="badge badge-primary rincian-tag mr-1 mb-1" style="font-size:0.85rem;">
                                                    {{ trim($rin) }}
                                                    <button type="button" class="btn btn-sm p-0 ml-1 remove-rincian-tag"
                                                        style="color:white; font-size:1rem; line-height:1;">×</button>
                                                </span>
                                            @endforeach
                                        </div>

                                        {{-- Hidden input stores final value --}}
                                        <input type="hidden" name="items[{{ $index }}][rincian]" class="rincian-value"
                                            value="{{ $item->rincian }}">
                                    </td>
                                    <td class="align-top">
                                        <input type="number" name="items[{{ $index }}][quantity]"
                                            class="form-control text-center" value="{{ $item->quantity }}" min="1" required>
                                    </td>
                                    <td class="align-top text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-5">
                <button type="submit" class="btn btn-warning btn-lg btn-block shadow-sm text-white">
                    <i class="fas fa-save fa-sm text-white-50"></i> Update Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- JAVASCRIPT UNTUK MENAMBAH BARIS DINAMIS DAN MULTI-SELECT RINCIAN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hitung jumlah baris awal agar index array tidak bentrok
            let rowIdx = {{ count($spk->items) }};

            // Machine options for dropdown
            const machineOptions = `
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->nama_mesin }}">{{ $machine->nama_mesin }}</option>
                        @endforeach
                        <option value="Lainnya">Lainnya (Custom)</option>
                    `;

            // === HELPER FUNCTIONS ===

            // Add rincian to list
            function addRincianToList(index, value) {
                if (!value || value.trim() === '') return;

                const listContainer = document.querySelector(`.rincian-list[data-index="${index}"]`);
                const tag = document.createElement('span');
                tag.className = 'badge badge-primary rincian-tag mr-1 mb-1';
                tag.style.fontSize = '0.85rem';
                tag.innerHTML = `${value.trim()}  <button type="button" class="btn btn-sm p-0 ml-1 remove-rincian-tag" style="color:white; font-size:1rem; line-height:1;">×</button>`;

                listContainer.appendChild(tag);
                updateHiddenInput(index);
            }

            // Remove rincian from list
            function removeRincianFromList(tagElement) {
                const index = tagElement.closest('td').querySelector('.rincian-list').dataset.index;
                tagElement.remove();
                updateHiddenInput(index);
            }

            // Update hidden input value
            function updateHiddenInput(index) {
                const listContainer = document.querySelector(`.rincian-list[data-index="${index}"]`);
                const tags = listContainer.querySelectorAll('.rincian-tag');
                const values = Array.from(tags).map(tag => {
                    return tag.childNodes[0].textContent.trim();
                });
                const hiddenInput = listContainer.closest('td').querySelector('.rincian-value');
                hiddenInput.value = values.join('\n');
            }

            // === EVENT DELEGATION ===

            // Click events
            document.getElementById('items-table').addEventListener('click', function (e) {
                // Add rincian button
                if (e.target.closest('.add-rincian-btn')) {
                    const btn = e.target.closest('.add-rincian-btn');
                    const index = btn.dataset.index;
                    const dropdown = document.querySelector(`.rincian-dropdown[data-index="${index}"]`);
                    const customTextarea = document.querySelector(`.rincian-custom-input[data-index="${index}"]`);

                    const selectedValue = dropdown.value;

                    if (selectedValue === 'Lainnya') {
                        // Show custom textarea
                        customTextarea.style.display = 'block';
                        customTextarea.focus();
                    } else if (selectedValue) {
                        addRincianToList(index, selectedValue);
                        dropdown.value = '';
                    }
                }

                // Remove rincian tag
                if (e.target.closest('.remove-rincian-tag')) {
                    const tagElement = e.target.closest('.rincian-tag');
                    removeRincianFromList(tagElement);
                }

                // Remove row
                if (e.target.closest('.remove-row')) {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        alert("Minimal harus menyisakan satu baris item.");
                    }
                }
            });

            // Change event for dropdown
            document.getElementById('items-table').addEventListener('change', function (e) {
                if (e.target.classList.contains('rincian-dropdown')) {
                    const index = e.target.dataset.index;
                    const customTextarea = document.querySelector(`.rincian-custom-input[data-index="${index}"]`);

                    if (e.target.value !== 'Lainnya') {
                        customTextarea.style.display = 'none';
                        customTextarea.value = '';
                    }
                }
            });

            // Blur event for custom textarea
            document.getElementById('items-table').addEventListener('blur', function (e) {
                if (e.target.classList.contains('rincian-custom-input')) {
                    const index = e.target.dataset.index;
                    const dropdown = document.querySelector(`.rincian-dropdown[data-index="${index}"]`);
                    const customValue = e.target.value.trim();

                    if (customValue) {
                        addRincianToList(index, customValue);
                        e.target.value = '';
                    }

                    e.target.style.display = 'none';
                    dropdown.value = '';
                }
            }, true);

            // === ADD ROW BUTTON ===

            document.getElementById('add-row').addEventListener('click', function () {
                let html = `
                            <tr class="item-row">
                                <td class="align-top">
                                    <input type="text" name="items[${rowIdx}][nama_barang]" class="form-control" placeholder="Nama Item Baru" required>
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
        });
    </script>
@endsection