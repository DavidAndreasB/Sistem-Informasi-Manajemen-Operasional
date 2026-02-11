<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\SpkItem;
use App\Models\Machine;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SpkController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $spk = Spk::with(['items', 'client'])->latest()->get(); // Eager load client
        return view('spk.index', compact('spk'));
    }

    public function create()
    {
        $nextSpkNumber = Spk::generateNextSpkNumber();
        $machines = \App\Models\Machine::all();
        $clients = Client::orderBy('nama_lengkap')->get();
        return view('spk.create', compact('nextSpkNumber', 'machines', 'clients'));
    }

    /**
     * Simpan Data SPK (Header + Banyak Item)
     */
    public function store(Request $request)
    {
        $request->validate([
            // Validasi Header (no_spk removed - akan di-generate otomatis)
            'tanggal' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'judul_proyek' => 'required|string',
            // status removed - otomatis 'Diproses'

            // Validasi Items (Array)
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string',
            'items.*.rincian' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            // Custom error messages
            'tanggal.required' => 'Tanggal harus diisi.',
            'client_id.required' => 'Client harus dipilih.',
            'client_id.exists' => 'Client tidak valid.',
            'judul_proyek.required' => 'Judul proyek harus diisi.',
            'items.required' => 'Minimal harus ada 1 item barang.',
            'items.min' => 'Minimal harus ada 1 item barang.',
            'items.*.nama_barang.required' => 'Nama barang harus diisi untuk semua item.',
            'items.*.rincian.required' => 'Rincian belum ditambahkan! Pilih mesin dari dropdown, lalu klik tombol HIJAU (+) untuk menambahkan rincian.',
            'items.*.quantity.required' => 'Quantity harus diisi untuk semua item.',
            'items.*.quantity.min' => 'Quantity minimal 1.',
        ]);

        DB::transaction(function () use ($request) {
            // Generate SPK number otomatis
            $noSpk = Spk::generateNextSpkNumber();

            // 1. Buat Header SPK
            $spk = Spk::create([
                'no_spk' => $noSpk,
                'tanggal' => $request->tanggal,
                'client_id' => $request->client_id,
                'judul_proyek' => $request->judul_proyek,
                'status' => 'Diproses', // <--- Status otomatis 'Diproses'
                'created_by' => auth()->id(),
            ]);

            // 2. Buat Item-item dan attach machines
            foreach ($request->items as $itemData) {
                $item = SpkItem::create([
                    'spk_id' => $spk->id,
                    'nama_barang' => $itemData['nama_barang'],
                    'quantity' => $itemData['quantity'],
                    // rincian is now nullable, we use pivot table instead
                ]);

                // Handle BOTH old format (rincian string) and new format (machine_ids array)
                if (isset($itemData['machine_ids']) && is_array($itemData['machine_ids'])) {
                    // New format: attach machine IDs directly
                    $item->machines()->attach($itemData['machine_ids']);
                } elseif (isset($itemData['rincian']) && !empty($itemData['rincian'])) {
                    // Old format: parse "Milling\nLas\nGrinding" to machine IDs
                    $machineNames = explode("\n", $itemData['rincian']);
                    $machineNames = array_map('trim', $machineNames);
                    $machineNames = array_filter($machineNames); // Remove empty values

                    foreach ($machineNames as $machineName) {
                        $machine = \App\Models\Machine::where('nama_mesin', $machineName)->first();
                        if ($machine) {
                            $item->machines()->attach($machine->id);
                        }
                    }
                }
            }
        });

        return redirect()->route('spk.index')->with('success', 'SPK berhasil disimpan dengan status Diproses.');
    }

    public function show(Spk $spk)
    {
        $spk->load(['items', 'creator']);
        return view('spk.show', compact('spk'));
    }

    public function edit(Spk $spk)
    {
        $spk->load('items');
        $machines = Machine::all();
        $clients = Client::orderBy('nama_lengkap')->get();
        return view('spk.edit', compact('spk', 'machines', 'clients'));
    }

    public function update(Request $request, Spk $spk)
    {
        $request->validate([
            'no_spk' => 'required|string|unique:spks,no_spk,' . $spk->id,
            'tanggal' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'judul_proyek' => 'required|string',
            // status removed - tidak bisa diedit
            'items' => 'required|array|min:1',
        ], [
            // Custom error messages
            'no_spk.required' => 'No SPK harus diisi.',
            'no_spk.unique' => 'No SPK sudah digunakan.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'client_id.required' => 'Client harus dipilih.',
            'client_id.exists' => 'Client tidak valid.',
            'judul_proyek.required' => 'Judul proyek harus diisi.',
            'items.required' => 'Minimal harus ada 1 item barang.',
            'items.min' => 'Minimal harus ada 1 item barang.',
        ]);

        DB::transaction(function () use ($request, $spk) {
            // Update Header (tanpa status)
            $spk->update([
                'no_spk' => $request->no_spk,
                'tanggal' => $request->tanggal,
                'client_id' => $request->client_id,
                'judul_proyek' => $request->judul_proyek,
                // status tidak diupdate
            ]);

            // Sinkronisasi Item (Hapus Lama -> Buat Baru)
            $spk->items()->delete();

            foreach ($request->items as $itemData) {
                $item = SpkItem::create([
                    'spk_id' => $spk->id,
                    'nama_barang' => $itemData['nama_barang'],
                    'quantity' => $itemData['quantity'],
                    // rincian is now nullable, stored in pivot table
                ]);

                // Handle BOTH old format (rincian string) and new format (machine_ids array)
                if (isset($itemData['machine_ids']) && is_array($itemData['machine_ids'])) {
                    // New format: attach machine IDs directly
                    $item->machines()->attach($itemData['machine_ids']);
                } elseif (isset($itemData['rincian']) && !empty($itemData['rincian'])) {
                    // Old format: parse "Milling\nLas\nGrinding" to machine IDs
                    $machineNames = explode("\n", $itemData['rincian']);
                    $machineNames = array_map('trim', $machineNames);
                    $machineNames = array_filter($machineNames); // Remove empty values

                    foreach ($machineNames as $machineName) {
                        $machine = \App\Models\Machine::where('nama_mesin', $machineName)->first();
                        if ($machine) {
                            $item->machines()->attach($machine->id);
                        }
                    }
                }
            }
        });

        return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui.');
    }

    public function destroy(Spk $spk)
    {
        $spk->delete();
        return redirect()->route('spk.index')->with('success', 'SPK berhasil dihapus.');
    }

    /**
     * Generate PDF untuk SPK
     */
    public function printPdf(Spk $spk)
    {
        // Load relasi yang diperlukan
        $spk->load(['items', 'creator']);

        // Load view PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('spk.pdf', compact('spk'));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        // Sanitize filename - hapus karakter / dan \ yang tidak valid
        $safeNoSpk = str_replace(['/', '\\'], '_', $spk->no_spk);
        $filename = 'SPK_' . $safeNoSpk . '_' . date('YmdHis') . '.pdf';

        return $pdf->stream($filename);
    }
}