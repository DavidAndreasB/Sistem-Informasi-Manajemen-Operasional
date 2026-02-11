{{-- JAVASCRIPT UNTUK MULTI-SELECT RINCIAN --}}
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

        // Function: Add rincian to list
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

        // Function: Remove rincian from list
        function removeRincianFromList(tagElement) {
            const listDiv = tagElement.closest('.rincian-list');
            const index = listDiv.dataset.index;
            tagElement.remove();
            updateRincianHiddenInput(index);
        }

        // Function: Update hidden input with all selected rincian
        function updateRincianHiddenInput(index) {
            const listDiv = document.querySelector(`.rincian-list[data-index="${index}"]`);
            const hiddenInput = listDiv.parentElement.querySelector('.rincian-value');

            const values = Array.from(listDiv.querySelectorAll('.rincian-tag'))
                .map(tag => tag.dataset.value);

            // Join with newlines for multi-line display
            hiddenInput.value = values.join('\n');
        }

        // Function: Handle dropdown selection change (show/hide custom textarea)
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

        // Event delegation: Add Rincian button
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
                    customTextarea.value = ''; // Clear after adding
                    customTextarea.style.display = 'none';
                    dropdown.value = ''; // Reset dropdown
                } else {
                    value = dropdown.value;
                    if (!value) {
                        alert('Silakan pilih mesin terlebih dahulu');
                        return;
                    }
                    dropdown.value = ''; // Reset dropdown
                }

                addRincianToList(index, value);
            }

            // Remove rincian tag button
            if (e.target.closest('.remove-rincian-tag')) {
                const tagElement = e.target.closest('.rincian-tag');
                removeRincianFromList(tagElement);
            }

            // Remove row button
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

        // ========================================================
        // ADD ROW FUNCTION
        // ========================================================

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
                            
                            <!-- Custom textarea -->
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