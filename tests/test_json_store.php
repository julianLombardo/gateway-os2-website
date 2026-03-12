<?php
define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/lib/helpers/JsonStore.php';

$passed = 0;
$failed = 0;

function assert_test($name, $condition) {
    global $passed, $failed;
    if ($condition) { echo "  \033[32m✓\033[0m $name\n"; $passed++; }
    else { echo "  \033[31m✗\033[0m $name\n"; $failed++; }
}

echo "\nJsonStore Tests\n===============\n\n";

$store = new JsonStore();
$testFile = sys_get_temp_dir() . '/gw_test_' . bin2hex(random_bytes(4)) . '.json';

// Write and read
$store->write($testFile, ['key' => 'value']);
$data = $store->read($testFile);
assert_test('Write and read', $data['key'] === 'value');

// Read non-existent
$data = $store->read($testFile . '.nope');
assert_test('Non-existent returns empty array', $data === []);

// Overwrite
$store->write($testFile, ['new' => 'data']);
$data = $store->read($testFile);
assert_test('Overwrite works', $data['new'] === 'data' && !isset($data['key']));

// Cleanup
@unlink($testFile);

echo "\nPassed: $passed, Failed: $failed\n";
exit($failed > 0 ? 1 : 0);
