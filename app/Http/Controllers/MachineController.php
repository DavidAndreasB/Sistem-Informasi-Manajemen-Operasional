<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of machines
     */
    public function index()
    {
        $machines = Machine::orderBy('nama_mesin')->get();
        return view('machines.index', compact('machines'));
    }

    /**
     * Show the form for creating a new machine
     */
    public function create()
    {
        return view('machines.create');
    }

    /**
     * Store a newly created machine
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mesin' => 'required|string|max:255|unique:machines,nama_mesin',
            'tarif' => 'required|numeric|min:0',
        ], [
            'nama_mesin.required' => 'Nama mesin harus diisi.',
            'nama_mesin.unique' => 'Nama mesin sudah ada.',
            'tarif.required' => 'Tarif harus diisi.',
            'tarif.numeric' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ]);

        Machine::create($validated);

        return redirect()->route('machines.index')
            ->with('success', 'Mesin berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified machine
     */
    public function edit(Machine $machine)
    {
        return view('machines.edit', compact('machine'));
    }

    /**
     * Update the specified machine
     */
    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'nama_mesin' => 'required|string|max:255|unique:machines,nama_mesin,' . $machine->id,
            'tarif' => 'required|numeric|min:0',
        ], [
            'nama_mesin.required' => 'Nama mesin harus diisi.',
            'nama_mesin.unique' => 'Nama mesin sudah ada.',
            'tarif.required' => 'Tarif harus diisi.',
            'tarif.numeric' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')
            ->with('success', 'Mesin berhasil diperbarui.');
    }

    /**
     * Remove the specified machine
     */
    public function destroy(Machine $machine)
    {
        // Check if machine is used in any JobSheet
        $usageCount = \App\Models\JobSheet::where('jenis_pekerjaan', $machine->nama_mesin)->count();

        if ($usageCount > 0) {
            return back()->with(
                'error',
                "Mesin '{$machine->nama_mesin}' tidak dapat dihapus karena masih digunakan di {$usageCount} jobsheet."
            );
        }

        $machine->delete();

        return redirect()->route('machines.index')
            ->with('success', 'Mesin berhasil dihapus.');
    }
}
