<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\User;
use App\Models\JobSheet;
use App\Models\SpkItem;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. RINGKASAN DATA (STATISTIK ATAS)
        $totalSpk       = Spk::count();
        $spkDiproses    = Spk::where('status', 'Diproses')->count();
        $spkSelesai     = Spk::where('status', 'Selesai')->count();
        
        // Hitung item yang menunggu QC (status_qc = 'Pending')
        // (Asumsi: Anda sudah menjalankan migrasi QC sebelumnya)
        // Jika belum, Anda bisa hapus baris ini atau ganti jadi 0
        $pendingQc      = SpkItem::where('status_qc', 'Pending')->count(); 

        // 2. DATA UNTUK TABEL/LIST (BAGIAN BAWAH)
        // Ambil 5 SPK terbaru
        $recentSpk      = Spk::latest()->take(5)->get();
        
        // Ambil 5 Aktivitas Jobsheet terbaru
        $recentActivity = JobSheet::with(['operator', 'spk'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalSpk', 
            'spkDiproses', 
            'spkSelesai', 
            'pendingQc',
            'recentSpk', 
            'recentActivity'
        ));
    }
}