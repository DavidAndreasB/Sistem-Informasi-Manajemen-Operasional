<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spk_items', function (Blueprint $table) {
            // Tambahkan kolom status_qc dan catatan_qc
            // Kita taruh setelah kolom 'quantity' agar rapi
            $table->enum('status_qc', ['Pending', 'OK', 'Reject'])
                  ->default('Pending')
                  ->after('quantity')
                  ->comment('Status verifikasi QC');

            $table->text('catatan_qc')
                  ->nullable()
                  ->after('status_qc')
                  ->comment('Alasan jika barang di-reject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spk_items', function (Blueprint $table) {
            // Hapus kolom jika di-rollback
            $table->dropColumn(['status_qc', 'catatan_qc']);
        });
    }
};