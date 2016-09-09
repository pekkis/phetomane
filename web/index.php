<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();


function hello(Request $request) {
    $response = new Response();
    $who = $request->attributes->get('who');
    $response->setContent(sprintf('hello, %s', $who));
    return $response;
}

function goodbye() {
    return Response::create('goodbye :(');
}

$routes = new RouteCollection();
$routes->add(
    'index',
    new Route('', [
        'who' => 'nobody',
        '_controller' => 'hello'
    ])
);
$routes->add(
    'hello',
    new Route('hello/{who}', [
        'who' => 'nobody',
        '_controller' => 'hello'
    ])
);
$routes->add(
    'goodbye',
    new Route('goodbye', [
        '_controller' => 'goodbye'
    ])
);

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $match = $matcher->match($request->getPathInfo());
    $request->attributes->add($match);
    $response = $match['_controller']($request);
} catch (ResourceNotFoundException $e) {
    $response = Response::create('not found')->setStatusCode(404);
}

$response
    ->prepare($request)
    ->send();
