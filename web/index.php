<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$response = Response::create('hello');
$response->headers->set('Content-Type', 'text/html');
$response->send();
