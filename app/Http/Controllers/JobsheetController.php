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
     * Halaman 1: Daftar SPK
     */
    public function index()
    {
        // 1. Ambil SPK yang sedang berjalan
        $activeSpks = Spk::where('status', 'Diproses')->latest()->get();

        // 2. Ambil SEMUA SPK yang sudah selesai (Hapus take(20))
        // DataTables akan menangani pagination-nya di tampilan (frontend)
        $finishedSpks = Spk::where('status', 'Selesai')->latest()->get();

        return view('jobsheet.index', compact('activeSpks', 'finishedSpks'));
    }
    /**
     * Halaman 2: Input & History
     */
    public function show($spk_id)
    {
        $spk = Spk::with(['jobsheets.operator', 'items.machines'])->findOrFail($spk_id);
        return view('jobsheet.show', compact('spk'));
    }

    /**
     * Simpan Aktivitas (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'spk_id' => 'required|exists:spks,id',
            'spk_item_id' => 'nullable|exists:spk_items,id',
            'jenis_pekerjaan' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        // Hitung durasi dengan menambahkan tanggal yang sama untuk parsing yang akurat
        $tanggal = $request->tanggal;
        $mulai = Carbon::parse($tanggal . ' ' . $request->jam_mulai);
        $selesai = Carbon::parse($tanggal . ' ' . $request->jam_selesai);

        // Hitung dari waktu mulai ke waktu selesai (bukan sebaliknya!)
        // diffInMinutes() tanpa parameter kedua = absolute value (selalu positif)
        $totalMinutes = $mulai->diffInMinutes($selesai);
        $totalJam = $totalMinutes / 60;

        // Debug logging
        \Log::info('JobSheet Store Debug', [
            'tanggal' => $tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'mulai_parsed' => $mulai->toDateTimeString(),
            'selesai_parsed' => $selesai->toDateTimeString(),
            'total_minutes' => $totalMinutes,
            'total_jam' => $totalJam,
            'spk_item_id' => $request->spk_item_id
        ]);

        JobSheet::create([
            'spk_id' => $request->spk_id,
            'spk_item_id' => $request->spk_item_id,
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
        if (auth()->user()->isSuperAdmin() || auth()->id() == $job->operator_id) {
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
        if ($item->status_pengerjaan == 'Selesai' && $item->status_qc != 'Reject') {
            return back()->with('error', 'Item ini sudah ditandai selesai sebelumnya.');
        }

        // Update status jadi Selesai (Siap QC)
        $item->update([
            'status_pengerjaan' => 'Selesai',
            'status_qc' => 'Pending'  // Reset QC status ke Pending (Menunggu QC)
        ]);

        return back()->with('success', 'Item berhasil ditandai selesai. Menunggu pemeriksaan QC.');
    }

    /**
     * (Opsional) Operator membatalkan selesai (jika kepencet)
     * Hanya bisa jika QC belum memeriksa (Status QC masih Pending)
     */
    public function undoCompleteItem($id)
    {
        $item = SpkItem::findOrFail($id);

        if ($item->status_qc != 'Pending') {
            return back()->with('error', 'Tidak bisa dibatalkan karena QC sudah memeriksa.');
        }

        $item->update(['status_pengerjaan' => 'Proses']);
        return back()->with('success', 'Status item dikembalikan ke proses.');
    }
}