<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Spk;
use App\Models\SpkItem;
use App\Models\JobSheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================================================
        // 1. BUAT AKUN UTAMA (STATIC)
        // ==============================================================
        
        // Admin
        $admin = User::updateOrCreate(['username' => 'superadmin'], [
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // QC
        $qc = User::updateOrCreate(['username' => 'qc_user'], [
            'password' => Hash::make('password'),
            'role' => User::ROLE_QUALITY_CONTROL,
        ]);

        // 3 Operator
        $operators = [];
        for ($i = 1; $i <= 3; $i++) {
            $operators[] = User::updateOrCreate(['username' => "operator_$i"], [
                'password' => Hash::make('password'),
                'role' => User::ROLE_OPERATOR,
            ]);
        }

        // ==============================================================
        // 2. GENERATE 10 SPK DUMMY
        // ==============================================================

        for ($i = 1; $i <= 10; $i++) {
            
            // Tentukan status secara acak
            // 20% Draft, 50% Diproses, 30% Selesai
            $rand = rand(1, 10);
            if ($rand <= 2) $status = 'Draft';
            elseif ($rand <= 7) $status = 'Diproses';
            else $status = 'Selesai';

            // Buat Header SPK
            $spk = Spk::create([
                // Format No SPK: 00X/VT/BLN/THN
                'no_spk' => sprintf('%03d/VT/PRJ/%s', $i, date('Y')), 
                'tanggal' => Carbon::now()->subDays(rand(1, 60)), // Tanggal acak 2 bulan terakhir
                'nama_pemesan' => fake()->company(),
                'judul_proyek' => 'Project ' . fake()->words(3, true),
                'status' => $status,
                'created_by' => $admin->id,
            ]);

            // ==========================================================
            // 3. GENERATE ITEM BARANG UNTUK SETIAP SPK
            // ==========================================================
            
            $jumlahItem = rand(2, 5); // Setiap SPK punya 2-5 jenis barang
            
            for ($j = 1; $j <= $jumlahItem; $j++) {
                
                // Tentukan status per item berdasarkan status SPK
                $statusPengerjaan = 'Proses';
                $statusQC = 'Pending';

                if ($status == 'Selesai') {
                    $statusPengerjaan = 'Selesai';
                    $statusQC = 'OK';
                } elseif ($status == 'Diproses') {
                    // Jika diproses, itemnya bisa campur (ada yg selesai, ada yg belum)
                    $statusPengerjaan = rand(0, 1) ? 'Selesai' : 'Proses';
                    if ($statusPengerjaan == 'Selesai') {
                        // Jika operator selesai, QC bisa Pending/OK/Reject
                        $qcRand = rand(1, 10);
                        if ($qcRand > 8) $statusQC = 'Reject';
                        elseif ($qcRand > 4) $statusQC = 'OK';
                        else $statusQC = 'Pending';
                    }
                }

                SpkItem::create([
                    'spk_id' => $spk->id,
                    'nama_barang' => 'Part ' . fake()->word() . '-' . $j,
                    'rincian' => fake()->sentence(10), // Kalimat acak
                    'quantity' => rand(1, 50),
                    'status_pengerjaan' => $statusPengerjaan,
                    'status_qc' => $statusQC,
                    'catatan_qc' => ($statusQC == 'Reject') ? 'Ukuran tidak presisi, tolong perbaiki.' : null,
                ]);
            }

            // ==========================================================
            // 4. GENERATE JOBSHEET (HANYA JIKA SUDAH DIPROSES/SELESAI)
            // ==========================================================

            if ($status != 'Draft') {
                $jumlahLog = rand(3, 8); // Ada 3-8 aktivitas pengerjaan

                for ($k = 1; $k <= $jumlahLog; $k++) {
                    $mesin = array_rand(JobSheet::TARIF_MESIN); // Pilih mesin acak
                    $durasi = rand(1, 5); // 1-5 jam
                    
                    // Tentukan operator acak dari 3 operator yang ada
                    $randomOp = $operators[array_rand($operators)]->id;

                    JobSheet::create([
                        'spk_id' => $spk->id,
                        'operator_id' => $randomOp,
                        'tanggal' => Carbon::now()->subDays(rand(1, 30)),
                        'jenis_pekerjaan' => $mesin,
                        'jam_mulai' => '08:00:00',
                        'jam_selesai' => sprintf('%02d:00:00', 8 + $durasi),
                        'total_jam' => (float)$durasi,
                        'keterangan' => 'Pengerjaan tahap ' . $k,
                    ]);
                }
            }
        }
    }
}