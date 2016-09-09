<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$path = $request->getPathInfo();

$map = [
    '/' => '/hello',
    '/hello' => function(Request $request) {
        $response = new Response();
        $who = $request->query->get('hello', 'nobody');
        $response->setContent(sprintf('hello, %s', $who));
        return $response;
    },
    '/goodbye' => function() {
        return Response::create('goodbye :(');
    }
];

if (isset($map[$path])) {

    // Index page kludge!
    if (is_string($map[$path])) {
        $path = $map[$path];
    }

    $response = $map[$path]($request);
} else {
    $response = Response::create('not found')->setStatusCode(404);
}

$response
    ->prepare($request)
    ->send();
