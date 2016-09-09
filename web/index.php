<?php

use Symfony\Component\HttpFoundation\Request;
use Pekkis\Phetomane\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Pimple\Container;
use Pekkis\Phetomane\PimpleListener;
use Pekkis\Phetomane\StringResponseListener;

require_once __DIR__ . '/../vendor/autoload.php';

function template(string $template) {
    return function (Container $container) use ($template) {
        /** @var Twig_Environment $twig */
        $twig = $container['twig'];
        return $twig->render($template);
    };
}

function hello(string $who, Container $container) {
    /** @var Twig_Environment $twig */
    $twig = $container['twig'];
    return $twig->render('hello.twig', ['who' => $who]);
}

function goodbye() {
}

$container = new Container();

$container['twig'] = function() {
    $twig = new Twig_Environment(
        new Twig_Loader_Filesystem(__DIR__ . '/../views'),
        [
            'cache' => false //__DIR__ . '/../tmp',
        ]
    );
    return $twig;
};

$request = Request::createFromGlobals();

$requestStack = new RequestStack();

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$routes = new RouteCollection();
$routes->add(
    'index',
    new Route('', [
        'who' => 'nobody',
        '_controller' => '\hello'
    ])
);
$routes->add(
    'hello',
    new Route('hello/{who}', [
        'who' => 'nobody',
        '_controller' => '\hello'
    ])
);
$routes->add(
    'goodbye',
    new Route('goodbye', [
        '_controller' => template('goodbye.twig')
    ])
);

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

$errorHandler = function (FlattenException $exception) {
    $msg = 'Piäleen män!!!! ('.$exception->getMessage().')';
    return new Response($msg, $exception->getStatusCode());
};
$dispatcher->addSubscriber(new ExceptionListener($errorHandler));

$dispatcher->addSubscriber(new PimpleListener($container));
$dispatcher->addSubscriber(new StringResponseListener());

$phetomane = new Application(
    $dispatcher,
    $controllerResolver,
    $requestStack,
    $argumentResolver
);

$response = $phetomane->handle($request);
$response->send();
