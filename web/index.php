<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$response = new Response();
$response->headers->set('Content-Type', 'text/html');

$who = $request->query->get('hello', 'nobody');

$response
    ->setContent(sprintf('hello, %s', $who))
    ->prepare($request)
    ->send();
