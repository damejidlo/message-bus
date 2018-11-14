<?php
declare(strict_types = 1);

$autoloader = require_once __DIR__ . '/../vendor/autoload.php';

if (getenv('IS_PHPSTAN') !== '1') {
	// configure environment
	date_default_timezone_set('Europe/Prague');
	umask(0);
	Tester\Environment::setup();
}

$_ENV = $_GET = $_POST = $_FILES = [];

return $autoloader;
