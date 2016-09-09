<?php

use Symfony\Component\HttpFoundation\Request;
use Pekkis\Phetomane\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$phetomane = new Application();

$response = $phetomane->handle($request);
$response->send();
