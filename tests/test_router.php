<?php
define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/lib/core/Router.php';

$passed = 0;
$failed = 0;

function assert_test($name, $condition) {
    global $passed, $failed;
    if ($condition) {
        echo "  \033[32m✓\033[0m $name\n";
        $passed++;
    } else {
        echo "  \033[31m✗\033[0m $name\n";
        $failed++;
    }
}

echo "\nRouter Tests\n============\n\n";

$router = new Router();

// Test basic route matching
$router->add('GET', '/', 'HomeController@index');
$match = $router->match('GET', '/');
assert_test('Root route matches', $match !== null);
assert_test('Root route handler', $match['handler'] === 'HomeController@index');

// Test parameterized routes
$router->add('GET', '/blog/:slug', 'BlogController@show');
$match = $router->match('GET', '/blog/hello-world');
assert_test('Param route matches', $match !== null);
assert_test('Param extracted', $match['params']['slug'] === 'hello-world');

// Test no match
$match = $router->match('GET', '/nonexistent');
assert_test('No match returns null', $match === null);

// Test method matching
$router->add('POST', '/contact', 'ContactController@submit');
$match = $router->match('GET', '/contact');
assert_test('Wrong method no match', $match === null || $match['handler'] !== 'ContactController@submit');

$match = $router->match('POST', '/contact');
assert_test('Correct method matches', $match !== null && $match['handler'] === 'ContactController@submit');

echo "\nPassed: $passed, Failed: $failed\n";
exit($failed > 0 ? 1 : 0);
