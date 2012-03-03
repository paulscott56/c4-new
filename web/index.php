<?php
 
// web/index.php
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
 
require_once __DIR__.'/../vendor/.composer/autoload.php';

$routes = include __DIR__.'/../src/app.php';
$sc = include __DIR__.'/../src/container.php';
 
$request = Request::createFromGlobals();
 
$sc->register('listener.string_response', 'C4\StringResponseListener');
$sc->getDefinition('dispatcher')->addMethodCall('addSubscriber', array(new Reference('listener.string_response')));

$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(array('%charset%'))
;

$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array('%routes%', new Reference('context')))
;

$sc->setParameter('routes', include __DIR__.'/../src/app.php');
$sc->setParameter('charset', 'UTF-8');
$sc->setParameter('debug', true);
 
// echo $sc->getParameter('debug');

$response = $sc->get('framework')->handle($request);
 
$response->send();