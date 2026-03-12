<?php
/**
 * GatewayOS2 Website — Front Controller
 * All requests route through here.
 */

define('BASE_DIR', dirname(__DIR__));

// Load core
require_once BASE_DIR . '/lib/core/App.php';

// Boot and run
$app = App::getInstance();
$app->boot();
$app->run();
