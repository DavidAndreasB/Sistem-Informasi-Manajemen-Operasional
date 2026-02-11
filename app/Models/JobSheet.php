<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSheet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // KONFIGURASI TARIF MESIN sekarang disimpan di database (tabel machines)

    // Relasi ke SPK
    public function spk()
    {
        return $this->belongsTo(Spk::class, 'spk_id');
    }

    // Relasi ke Operator
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Relasi ke Machine
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'jenis_pekerjaan', 'nama_mesin');
    }

    // Relasi ke SPK Item
    public function spkItem()
    {
        return $this->belongsTo(SpkItem::class, 'spk_item_id');
    }

    // Attribute Virtual: Menghitung Biaya (Total Jam * Tarif)
    public function getBiayaAttribute()
    {
        // Ambil tarif dari database
        $machine = Machine::where('nama_mesin', $this->jenis_pekerjaan)->first();
        $tarif = $machine ? $machine->tarif : 0;
        return $tarif * $this->total_jam;
    }
}