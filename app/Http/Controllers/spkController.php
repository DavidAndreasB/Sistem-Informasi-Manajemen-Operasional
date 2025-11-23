<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use App\Models\SpkItem;
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
        $spk = Spk::with('items')->latest()->get();
        return view('spk.index', compact('spk'));
    }

    public function create()
    {
        return view('spk.create');
    }

    /**
     * Simpan Data SPK (Header + Banyak Item)
     */
    public function store(Request $request)
    {
        $request->validate([
            // Validasi Header
            'no_spk'       => 'required|string|unique:spks,no_spk',
            'tanggal'      => 'required|date',
            'nama_pemesan' => 'required|string',
            'judul_proyek' => 'required|string',
            'status'       => 'required|in:Draft,Diproses', // Validasi Status (Baru)

            // Validasi Items (Array)
            'items'              => 'required|array|min:1',
            'items.*.nama_barang'=> 'required|string',
            'items.*.rincian'    => 'required|string',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            
            // 1. Buat Header SPK
            $spk = Spk::create([
                'no_spk'       => $request->no_spk,
                'tanggal'      => $request->tanggal,
                'nama_pemesan' => $request->nama_pemesan,
                'judul_proyek' => $request->judul_proyek,
                'status'       => $request->status, // <--- Menggunakan input status dari form
                'created_by'   => auth()->id(),
            ]);

            // 2. Buat Item-item
            foreach ($request->items as $item) {
                SpkItem::create([
                    'spk_id'      => $spk->id,
                    'nama_barang' => $item['nama_barang'],
                    'rincian'     => $item['rincian'],
                    'quantity'    => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('spk.index')->with('success', 'SPK berhasil disimpan sebagai ' . $request->status . '.');
    }

    public function show(Spk $spk)
    {
        $spk->load(['items', 'creator']);
        return view('spk.show', compact('spk'));
    }

    public function edit(Spk $spk)
    {
        $spk->load('items');
        return view('spk.edit', compact('spk'));
    }

    public function update(Request $request, Spk $spk)
    {
        $request->validate([
            'no_spk' => 'required|string|unique:spks,no_spk,' . $spk->id,
            'tanggal' => 'required|date',
            'nama_pemesan' => 'required|string',
            'judul_proyek' => 'required|string',
            'status' => 'required|in:Draft,Diproses,Selesai', // Status bisa diupdate juga
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $spk) {
            // Update Header
            $spk->update([
                'no_spk'       => $request->no_spk,
                'tanggal'      => $request->tanggal,
                'nama_pemesan' => $request->nama_pemesan,
                'judul_proyek' => $request->judul_proyek,
                'status'       => $request->status,
            ]);

            // Sinkronisasi Item (Hapus Lama -> Buat Baru)
            $spk->items()->delete();

            foreach ($request->items as $item) {
                SpkItem::create([
                    'spk_id'      => $spk->id,
                    'nama_barang' => $item['nama_barang'],
                    'rincian'     => $item['rincian'],
                    'quantity'    => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui.');
    }

    public function destroy(Spk $spk)
    {
        $spk->delete();
        return redirect()->route('spk.index')->with('success', 'SPK berhasil dihapus.');
    }
}