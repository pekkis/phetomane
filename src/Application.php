<?php

namespace Pekkis\Phetomane;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;


function hello(string $who) {
    $response = new Response();
    $response->setContent(sprintf('hello, %s', $who));
    return $response;
}

function goodbye() {
    return Response::create('goodbye :(');
}


class Application implements HttpKernelInterface
{

    public function __construct()
    {

    }


    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $routes = new RouteCollection();
        $routes->add(
            'index',
            new Route('', [
                'who' => 'nobody',
                '_controller' => 'Pekkis\Phetomane\hello'
            ])
        );
        $routes->add(
            'hello',
            new Route('hello/{who}', [
                'who' => 'nobody',
                '_controller' => 'Pekkis\Phetomane\hello'
            ])
        );
        $routes->add(
            'goodbye',
            new Route('goodbye', [
                '_controller' => 'Pekkis\Phetomane\goodbye'
            ])
        );

        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($routes, $context);

        try {
            $match = $matcher->match($request->getPathInfo());
            $request->attributes->add($match);

            $controllerResolver = new ControllerResolver();
            $argumentResolver = new ArgumentResolver();
            $controller = $controllerResolver->getController($request);
            $arguments = $argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = Response::create('not found')->setStatusCode(404);
        } catch (\Exception $e) {
            $response = Response::create('infernal server error')->setStatusCode(404);
        }

        return $response->prepare($request);
    }
}
