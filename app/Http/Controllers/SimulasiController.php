<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSheet; // Kita ambil tarif dari sini

class SimulasiController extends Controller
{
    public function index()
    {
        // Ambil daftar tarif yang sudah didefinisikan di Model JobSheet
        // Agar jika harga naik, simulasi juga ikut naik otomatis.
        $tarifs = JobSheet::TARIF_MESIN;
        
        return view('simulasi.index', compact('tarifs'));
    }
}