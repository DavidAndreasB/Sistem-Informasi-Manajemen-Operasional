<?php

// Test script to verify machine attachment logic
// Run with: php test_machine_attachment.php

// Simulate the rincian input format
$itemData = [
    'rincian' => "Milling\nLas\nGrinding"
];

echo "Testing machine name parsing...\n\n";

// Parse the rincian string (same logic as controller)
$machineNames = explode("\n", $itemData['rincian']);
$machineNames = array_map('trim', $machineNames);
$machineNames = array_filter($machineNames);

echo "Input: " . $itemData['rincian'] . "\n\n";
echo "Parsed machines:\n";
foreach ($machineNames as $name) {
    echo "  - '$name'\n";
}

echo "\nThis should show 3 machines: Milling, Las, Grinding\n";
