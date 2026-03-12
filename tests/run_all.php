<?php
echo "\n\033[1mGatewayOS2 — Test Suite\033[0m\n";
echo "========================\n";

$tests = glob(__DIR__ . '/test_*.php');
$total_pass = 0;
$total_fail = 0;

foreach ($tests as $test) {
    $name = basename($test, '.php');
    $output = [];
    $code = 0;
    exec("php " . escapeshellarg($test) . " 2>&1", $output, $code);
    echo implode("\n", $output) . "\n";
    if ($code !== 0) $total_fail++;
    else $total_pass++;
}

echo "\n\033[1mTotal: " . count($tests) . " test files";
if ($total_fail > 0) {
    echo " (\033[31m{$total_fail} failed\033[0m)\n";
    exit(1);
} else {
    echo " (\033[32mall passed\033[0m)\n";
}
