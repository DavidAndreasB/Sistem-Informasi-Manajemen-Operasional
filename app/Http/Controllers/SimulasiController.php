<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSheet; // Kita ambil tarif dari sini
use Barryvdh\DomPDF\Facade\Pdf;

class SimulasiController extends Controller
{
    public function index()
    {
        // Ambil daftar tarif yang sudah didefinisikan di Model JobSheet
        // Agar jika harga naik, simulasi juga ikut naik otomatis.
        $tarifs = JobSheet::TARIF_MESIN;

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