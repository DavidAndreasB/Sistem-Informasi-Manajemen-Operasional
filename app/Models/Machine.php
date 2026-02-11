<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = ['nama_mesin', 'tarif'];

    // Relasi ke JobSheets
    public function jobSheets()
    {
        return $this->hasMany(JobSheet::class, 'jenis_pekerjaan', 'nama_mesin');
    }

    /**
     * Many-to-many relationship with SpkItem
     */
    public function spkItems()
    {
        return $this->belongsToMany(\App\Models\SpkItem::class, 'machine_spk_item')
            ->withTimestamps();
    }
}
