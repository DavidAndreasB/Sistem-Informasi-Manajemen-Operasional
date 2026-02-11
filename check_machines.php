<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Data Mesin di Database:\n";
echo "======================\n\n";

$machines = App\Models\Machine::all(['id', 'nama_mesin', 'tarif']);

foreach ($machines as $machine) {
    echo sprintf(
        "ID: %d | Nama: %-30s | Tarif: Rp %s\n",
        $machine->id,
        $machine->nama_mesin,
        number_format($machine->tarif, 0, ',', '.')
    );
}

echo "\n\nTotal: " . $machines->count() . " mesin\n";
