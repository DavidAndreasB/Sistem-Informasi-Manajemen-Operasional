<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkItem extends Model
{
    use HasFactory;

    protected $table = 'spk_items';

    // Pastikan status_qc dan catatan_qc ada di sini
    protected $fillable = [
        'spk_id', 
        'nama_barang', 
        'rincian', 
        'quantity', 
        'status_pengerjaan',
        'status_qc',   // Kolom Status (Pending, OK, Reject)
        'catatan_qc'   // Kolom Alasan Reject
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'spk_id');
    }
}