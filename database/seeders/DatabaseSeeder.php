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
        // 2. SEED MACHINES (MASTER DATA)
        // ==============================================================

        $machines = [
            ['nama_mesin' => 'Milling', 'tarif' => 120000],
            ['nama_mesin' => 'Bubut Kecil', 'tarif' => 120000],
            ['nama_mesin' => 'Bubut Besar', 'tarif' => 250000],
            ['nama_mesin' => 'Grinding', 'tarif' => 250000],
            ['nama_mesin' => 'Las', 'tarif' => 200000],
            ['nama_mesin' => 'Metal Spray', 'tarif' => 150000],
            ['nama_mesin' => 'Sand Blasting / Pengecatan', 'tarif' => 200000],
        ];

        foreach ($machines as $machine) {
            \App\Models\Machine::updateOrCreate(
                ['nama_mesin' => $machine['nama_mesin']],
                ['tarif' => $machine['tarif']]
            );
        }

        // ==============================================================
        // 3. SEED CLIENTS (MASTER DATA)
        // ==============================================================

        $clients = [
            ['nama_lengkap' => 'PT. Maju Bersama', 'inisial' => 'PT. MB'],
            ['nama_lengkap' => 'CV. Teknologi Nusantara', 'inisial' => 'CV. TN'],
            ['nama_lengkap' => 'PT. Sinar Manufaktur', 'inisial' => 'PT. SM'],
            ['nama_lengkap' => 'UD. Berkah Sejahtera', 'inisial' => 'UD. BS'],
        ];

        foreach ($clients as $client) {
            \App\Models\Client::updateOrCreate(
                ['nama_lengkap' => $client['nama_lengkap']],
                ['inisial' => $client['inisial']]
            );
        }

        // Ambil semua client untuk digunakan di SPK
        $allClients = \App\Models\Client::all();

        // ==============================================================
        // 4. GENERATE 10 SPK DUMMY
        // ==============================================================

        for ($i = 1; $i <= 10; $i++) {

            // Tentukan status secara acak
            // 70% Diproses, 30% Selesai (Draft dihapus)
            $rand = rand(1, 10);
            if ($rand <= 7)
                $status = 'Diproses';
            else
                $status = 'Selesai';

            $randomClient = $allClients->random();

            $spk = \App\Models\Spk::create([
                'no_spk' => sprintf('SPK/%s/%04d', date('Y.m'), $i),
                'tanggal' => now()->subDays(rand(0, 60)),
                'client_id' => $randomClient->id, // Use client_id instead of nama_pemesan
                'judul_proyek' => 'Proyek ' . fake()->sentence(3),
                'status' => $status,
                'created_by' => 1,
            ]);

            // ==========================================================
            // 4. GENERATE ITEM BARANG UNTUK SETIAP SPK
            // ==========================================================

            $jumlahItem = rand(2, 5); // Setiap SPK punya 2-5 jenis barang
            $spkItems = []; // Array untuk menyimpan item-item yang dibuat

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
                        if ($qcRand > 8)
                            $statusQC = 'Reject';
                        elseif ($qcRand > 4)
                            $statusQC = 'OK';
                        else
                            $statusQC = 'Pending';
                    }
                }

                $item = SpkItem::create([
                    'spk_id' => $spk->id,
                    'nama_barang' => 'Part ' . fake()->word() . '-' . $j,
                    'quantity' => rand(1, 50),
                    'status_pengerjaan' => $statusPengerjaan,
                    'status_qc' => $statusQC,
                    'catatan_qc' => ($statusQC == 'Reject') ? 'Ukuran tidak presisi, tolong perbaiki.' : null,
                ]);

                // Attach 1-3 random machines to each item via pivot table
                $randomMachines = \App\Models\Machine::inRandomOrder()->take(rand(1, 3))->pluck('id');
                $item->machines()->attach($randomMachines);

                $spkItems[] = $item; // Simpan item ke array
            }

            // ==========================================================
            // 5. GENERATE JOBSHEET (untuk semua SPK)
            // ==========================================================

            $jumlahLog = rand(3, 8); // Ada 3-8 aktivitas pengerjaan

            for ($k = 1; $k <= $jumlahLog; $k++) {
                // Pilih mesin acak dari database
                $mesin = \App\Models\Machine::inRandomOrder()->first()->nama_mesin;
                $durasi = rand(1, 5); // 1-5 jam

                // Tentukan operator acak dari 3 operator yang ada
                $randomOp = $operators[array_rand($operators)]->id;

                // Pilih item SPK secara acak untuk dihubungkan ke jobsheet
                $randomItem = $spkItems[array_rand($spkItems)];

                JobSheet::create([
                    'spk_id' => $spk->id,
                    'spk_item_id' => $randomItem->id, // Hubungkan ke item spesifik
                    'operator_id' => $randomOp,
                    'tanggal' => Carbon::now()->subDays(rand(1, 30)),
                    'jenis_pekerjaan' => $mesin,
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => sprintf('%02d:00:00', 8 + $durasi),
                    'total_jam' => (float) $durasi,
                    'keterangan' => 'Pengerjaan ' . $randomItem->nama_barang . ' - tahap ' . $k,
                ]);
            }
        }
    }
}