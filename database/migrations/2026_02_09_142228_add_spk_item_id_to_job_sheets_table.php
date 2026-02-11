<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_sheets', function (Blueprint $table) {
            $table->unsignedBigInteger('spk_item_id')->nullable()->after('spk_id');
            $table->foreign('spk_item_id')->references('id')->on('spk_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_sheets', function (Blueprint $table) {
            $table->dropForeign(['spk_item_id']);
            $table->dropColumn('spk_item_id');
        });
    }
};
