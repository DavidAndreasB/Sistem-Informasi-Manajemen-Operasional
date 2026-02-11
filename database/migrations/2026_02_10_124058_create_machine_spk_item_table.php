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
        // Create pivot table for many-to-many relationship
        Schema::create('machine_spk_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_item_id')
                ->constrained('spk_items')
                ->onDelete('cascade');
            $table->foreignId('machine_id')
                ->constrained('machines')
                ->onDelete('restrict'); // Prevent deleting machine if in use
            $table->timestamps();

            // Ensure unique combination
            $table->unique(['spk_item_id', 'machine_id']);
        });

        // Migrate existing data from rincian (comma-separated string) to pivot table
        $spkItems = \App\Models\SpkItem::all();
        foreach ($spkItems as $item) {
            if ($item->rincian) {
                // Split "Milling, Las, Grinding" into array
                $machineNames = array_map('trim', explode(',', $item->rincian));

                foreach ($machineNames as $machineName) {
                    $machine = \App\Models\Machine::where('nama_mesin', $machineName)->first();
                    if ($machine) {
                        // Insert into pivot table
                        \DB::table('machine_spk_item')->insert([
                            'spk_item_id' => $item->id,
                            'machine_id' => $machine->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Make rincian nullable since we now use pivot table
        Schema::table('spk_items', function (Blueprint $table) {
            $table->text('rincian')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spk_items', function (Blueprint $table) {
            $table->text('rincian')->nullable(false)->change();
        });

        Schema::dropIfExists('machine_spk_item');
    }
};
