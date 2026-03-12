<?php
define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/lib/helpers/Validator.php';

$passed = 0;
$failed = 0;

function assert_test($name, $condition) {
    global $passed, $failed;
    if ($condition) { echo "  \033[32m✓\033[0m $name\n"; $passed++; }
    else { echo "  \033[31m✗\033[0m $name\n"; $failed++; }
}

echo "\nValidator Tests\n===============\n\n";

$v = new Validator();

// Required
$result = $v->validate(['name' => ''], ['name' => 'required']);
assert_test('Required fails on empty', !$result['valid']);

$result = $v->validate(['name' => 'John'], ['name' => 'required']);
assert_test('Required passes', $result['valid']);

// Email
$result = $v->validate(['email' => 'bad'], ['email' => 'email']);
assert_test('Email validation fails', !$result['valid']);

$result = $v->validate(['email' => 'test@example.com'], ['email' => 'email']);
assert_test('Email validation passes', $result['valid']);

// Min length
$result = $v->validate(['pw' => 'short'], ['pw' => 'min:8']);
assert_test('Min length fails', !$result['valid']);

$result = $v->validate(['pw' => 'longenough'], ['pw' => 'min:8']);
assert_test('Min length passes', $result['valid']);

// Combined rules
$result = $v->validate(['name' => '', 'email' => 'bad'], ['name' => 'required', 'email' => 'required|email']);
assert_test('Multiple errors', count($result['errors']) >= 2);

echo "\nPassed: $passed, Failed: $failed\n";
exit($failed > 0 ? 1 : 0);
