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
        Schema::table('spks', function (Blueprint $table) {
            // Add client_id column as nullable first (for migration)
            $table->unsignedBigInteger('client_id')->nullable()->after('no_spk');

            // Add foreign key constraint
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('restrict'); // Prevent deleting client if used in SPK
        });

        // Migrate existing data from nama_pemesan to client_id
        $spks = \App\Models\Spk::all();
        foreach ($spks as $spk) {
            $client = \App\Models\Client::where('nama_lengkap', $spk->nama_pemesan)->first();
            if ($client) {
                $spk->client_id = $client->id;
                $spk->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
