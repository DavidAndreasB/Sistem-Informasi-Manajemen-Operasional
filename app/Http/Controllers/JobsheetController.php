<?php

namespace App\Http\Controllers;

use App\Models\JobSheet;
use App\Models\Spk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SpkItem;

class JobsheetController extends Controller
{
    /**
     * Halaman 1: Daftar SPK Aktif
     */
    public function index()
    {
        $spks = Spk::where('status', 'Diproses')->latest()->get();
        return view('jobsheet.index', compact('spks'));
    }

    /**
     * Halaman 2: Input & History
     */
    public function show($spk_id)
    {
        $spk = Spk::with(['jobsheets.operator'])->findOrFail($spk_id);
        return view('jobsheet.show', compact('spk'));
    }

    /**
     * Simpan Aktivitas (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'spk_id' => 'required|exists:spks,id',
            'jenis_pekerjaan' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // Hitung durasi (abs = nilai mutlak agar tidak minus)
        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);
        $totalJam = abs($selesai->diffInMinutes($mulai)) / 60; 

        JobSheet::create([
            'spk_id' => $request->spk_id,
            'operator_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'total_jam' => $totalJam,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Aktivitas berhasil dicatat.');
    }

    /**
     * Hapus Aktivitas (DESTROY)
     */
    public function destroy($id)
    {
        $job = JobSheet::findOrFail($id);
        
        // Hanya Admin atau Pembuat data yang boleh hapus
        if(auth()->user()->isSuperAdmin() || auth()->id() == $job->operator_id){
             $job->delete();
             return back()->with('success', 'Data dihapus.');
        }
        
        return back()->with('error', 'Akses ditolak.');
    }

    /**
     * Operator menandai item selesai dikerjakan
     */
    public function completeItem($id)
    {
        $item = SpkItem::findOrFail($id);
        
        // Validasi: Hanya bisa jika status masih 'Proses'
        if($item->status_pengerjaan == 'Selesai') {
             return back()->with('error', 'Item ini sudah ditandai selesai sebelumnya.');
        }

        // Update status jadi Selesai (Siap QC)
        $item->update(['status_pengerjaan' => 'Selesai']);

        return back()->with('success', 'Item berhasil ditandai selesai. Menunggu pemeriksaan QC.');
    }
    
    /**
     * (Opsional) Operator membatalkan selesai (jika kepencet)
     * Hanya bisa jika QC belum memeriksa (Status QC masih Pending)
     */
    public function undoCompleteItem($id)
    {
        $item = SpkItem::findOrFail($id);

        if($item->status_qc != 'Pending') {
            return back()->with('error', 'Tidak bisa dibatalkan karena QC sudah memeriksa.');
        }

        $item->update(['status_pengerjaan' => 'Proses']);
        return back()->with('success', 'Status item dikembalikan ke proses.');
    }
}