<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model // Pastikan S besar
{
    use HasFactory;

    protected $table = 'spks';
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(SpkItem::class, 'spk_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function jobsheets()
    {
        return $this->hasMany(JobSheet::class, 'spk_id');
    }

    /**
     * Relationship: SPK belongs to one Client
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }

    /**
     * Get client inisial (using relationship)
     */
    public function getClientInisialAttribute()
    {
        return $this->client ? $this->client->inisial : '-';
    }

    /**
     * Get client full name (using relationship)
     */
    public function getClientNameAttribute()
    {
        return $this->client ? $this->client->nama_lengkap : '-';
    }

    /**
     * Generate next SPK number with auto-increment
     * Format: VT.(DD).(MM Roman).(YY).(XXX)
     * Example: VT.09.II.26.001
     */
    public static function generateNextSpkNumber()
    {
        $day = date('d');        // 01-31
        $month = date('n');      // 1-12
        $year = date('y');       // 26

        // Convert month to Roman numerals
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        $monthRoman = $romanMonths[$month];

        // Build prefix for current date: VT.09.II.26
        $prefix = "VT.{$day}.{$monthRoman}.{$year}";

        // Get the last SPK number for current date (day.month.year)
        // Pattern: VT.09.II.26.%
        $lastSpk = self::where('no_spk', 'LIKE', $prefix . '.%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(no_spk, \'.\', -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastSpk) {
            // Extract the increment part (last segment after final dot)
            // Example: VT.09.II.26.005 -> extract "005"
            $parts = explode('.', $lastSpk->no_spk);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            // First SPK for this date
            $nextNumber = 1;
        }

        // Format: VT.09.II.26.001
        return $prefix . '.' . sprintf('%03d', $nextNumber);
    }
}