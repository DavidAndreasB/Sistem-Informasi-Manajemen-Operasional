<?php

namespace App\Http\Controllers;

use App\Models\SpkItem;
use Illuminate\Http\Request;

class QcController extends Controller
{
    /**
     * Update Status QC untuk item tertentu
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi Otoritas (Hanya QC & Admin)
        if (!auth()->user()->isQualityControl() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Hanya QC yang berwenang.');
        }

        // Cari Item Barang
        $item = SpkItem::findOrFail($id);

        // 2. Validasi Input Form
        $request->validate([
            'status_qc' => 'required|in:Pending,OK,Reject',
            'catatan_qc' => 'nullable|string',
        ]);

        // 3a. Validasi: Cegah rollback dari OK ke Reject
        if ($item->status_qc == 'OK' && $request->status_qc == 'Reject') {
            return back()->with('error', 'Status yang sudah LULUS tidak dapat diubah kembali ke REJECT.');
        }

        // 4. Update Status Item Barang
        $item->update([
            'status_qc' => $request->status_qc,
            'catatan_qc' => $request->catatan_qc,
        ]);

        // ========================================================================
        // LOGIKA OTOMATIS UPDATE STATUS SPK (AUTO-COMPLETE)
        // ========================================================================

        // Ambil SPK Induk
        $spk = $item->spk;

        // Cek: Apakah masih ada barang di SPK ini yang status QC-nya BUKAN 'OK'?
        // (Artinya masih ada yang Pending atau Reject)
        $unfinishedItems = $spk->items()->where('status_qc', '!=', 'OK')->count();

        if ($unfinishedItems == 0) {
            // JIKA 0 (Semua Barang sudah OK) -> Ubah SPK jadi 'Selesai'
            $spk->update(['status' => 'Selesai']);
            $message = 'Item di-update. Karena semua barang sudah OK, Status SPK otomatis menjadi SELESAI.';
        } else {
            // JIKA MASIH ADA SISA -> Pastikan SPK statusnya 'Diproses'
            // (Ini berguna jika sebelumnya sudah Selesai, tapi ada barang yang di-Reject lagi)
            if ($spk->status == 'Selesai') {
                $spk->update(['status' => 'Diproses']);
                $message = 'Status SPK kembali menjadi "Diproses" karena ada item yang direvisi/reject.';
            } else {
                $message = 'Status Quality Control berhasil diperbarui.';
            }
        }
        // ========================================================================

        return back()->with('success', $message);
    }
}