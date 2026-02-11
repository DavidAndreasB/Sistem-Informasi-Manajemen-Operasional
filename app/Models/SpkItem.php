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

    public function jobsheets()
    {
        return $this->hasMany(JobSheet::class, 'spk_item_id');
    }

    /**
     * Many-to-many relationship with Machine
     */
    public function machines()
    {
        return $this->belongsToMany(\App\Models\Machine::class, 'machine_spk_item')
            ->withTimestamps();
    }

    /**
     * Accessor: Get machine names as comma-separated string
     * For backward compatibility and display
     */
    public function getMachineNamesAttribute()
    {
        return $this->machines->pluck('nama_mesin')->implode(', ');
    }
}