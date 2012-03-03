<?php
 
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Monolog\Logger;


$routes = include __DIR__.'/app.php';

$sc = new DependencyInjection\ContainerBuilder();
$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array($routes, new Reference('context')))
;
$sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');
 
$sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments(array(new Reference('matcher')))
;
$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(array('UTF-8'))
;
$sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments(array('C4\\Core\\Controller\\ErrorController::exceptionAction'))
;
$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
;


// logging
//$sc->register('log.writer','StreamHandler')
//    ->addArgument(__DIR__.'/my_app.log')
//    ->addArgument(Logger::DEBUG)
//;

//$sc->register('log.FirePHPwriter','FirePHPHandler')
//;

$sc->register('logger', '\Monolog\Logger')
    ->addArgument(__DIR__.'/System_Log')
;

$sc->register('framework', 'C4\Core\Framework')
->setArguments(array($routes, new Reference('logger')));
;
return $sc;