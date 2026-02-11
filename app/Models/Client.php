<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nama_lengkap',
        'inisial',
    ];
    /**
     * Relationship: Client has many SPKs
     */
    public function spks()
    {
        return $this->hasMany(\App\Models\Spk::class, 'client_id');
    }
}
