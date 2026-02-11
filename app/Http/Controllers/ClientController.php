<?php


namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index()
    {
        $clients = Client::orderBy('nama_lengkap')->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255|unique:clients,nama_lengkap',
            'inisial' => 'required|string|max:50',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi.',
            'nama_lengkap.unique' => 'Nama perusahaan sudah terdaftar.',
            'inisial.required' => 'Inisial harus diisi.',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255|unique:clients,nama_lengkap,' . $client->id,
            'inisial' => 'required|string|max:50',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi.',
            'nama_lengkap.unique' => 'Nama perusahaan sudah terdaftar.',
            'inisial.required' => 'Inisial harus diisi.',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil diperbarui.');
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client)
    {
        // Check if client is used in any SPK using relationship
        $usageCount = $client->spks()->count();

        if ($usageCount > 0) {
            return back()->with(
                'error',
                "Client '{$client->nama_lengkap}' tidak dapat dihapus karena masih digunakan di {$usageCount} SPK."
            );
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }
}

