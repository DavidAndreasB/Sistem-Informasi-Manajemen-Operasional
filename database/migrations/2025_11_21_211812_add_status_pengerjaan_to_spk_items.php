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
        // Status Pengerjaan oleh Operator
        // 'Proses': Sedang dikerjakan
        // 'Selesai': Operator sudah selesai, siap QC
        $table->enum('status_pengerjaan', ['Proses', 'Selesai'])
              ->default('Proses')
              ->after('quantity'); 
    });
}

public function down(): void
{
    Schema::table('spk_items', function (Blueprint $table) {
        $table->dropColumn('status_pengerjaan');
    });
}
};
