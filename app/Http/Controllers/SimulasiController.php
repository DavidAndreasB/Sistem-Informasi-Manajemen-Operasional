<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine; // Ambil tarif dari database
use Barryvdh\DomPDF\Facade\Pdf;

class SimulasiController extends Controller
{
    public function index()
    {
        // Ambil daftar tarif dari database
        // Menggunakan pluck untuk membuat array [nama_mesin => tarif]
        $tarifs = Machine::pluck('tarif', 'nama_mesin')->toArray();

        return view('simulasi.index', compact('tarifs'));
    }

    public function exportPdf(Request $request)
    {
        // Validasi bahwa ada data items
        $request->validate([
            'items' => 'required|json',
            'total' => 'required|numeric'
        ]);

        // Decode JSON items
        $items = json_decode($request->items, true);
        $total = $request->total;

        // Generate PDF
        $pdf = Pdf::loadView('simulasi.pdf', [
            'items' => $items,
            'total' => $total,
            'tanggal' => now()
        ]);

        // Set paper size dan orientasi
        $pdf->setPaper('A4', 'portrait');

        // Return PDF untuk ditampilkan di browser
        return $pdf->stream('Simulasi_Harga_' . now()->format('Y-m-d_His') . '.pdf');
    }
}