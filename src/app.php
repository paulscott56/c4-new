<?php
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();

$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
    'year' => null,
    '_controller' => 'C4\\Modules\\Calendar\\Controller\\LeapYearController::indexAction',
)));

$routes->add('index', new Routing\Route('/', array(
    '_controller' => 'C4\\Core\\Controller\\DefaultController::indexAction',
)));

return $routes;
