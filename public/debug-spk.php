<!DOCTYPE html>
<html>

<head>
    <title>SPK Debug Test</title>
</head>

<body>
    <h1>SPK Debug - Check Database</h1>
    <pre>
<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Total SPK in database: " . \App\Models\Spk::count() . "\n\n";

echo "Last 5 SPK:\n";
$spks = \App\Models\Spk::latest()->take(5)->get(['id', 'no_spk', 'nama_pemesan', 'tanggal', 'created_at']);
foreach ($spks as $spk) {
    echo "ID: {$spk->id} | No: {$spk->no_spk} | Pemesan: {$spk->nama_pemesan} | Tanggal: {$spk->tanggal} | Created: {$spk->created_at}\n";
}

echo "\n\nAll SPK (for comparison with web display):\n";
$allSpks = \App\Models\Spk::with('items')->latest()->get();
echo "Total with relations loaded: " . $allSpks->count() . "\n";
foreach ($allSpks as $spk) {
    echo "- {$spk->no_spk} ({$spk->nama_pemesan}) - Items: " . $spk->items->count() . "\n";
}
?>
    </pre>
</body>

</html>