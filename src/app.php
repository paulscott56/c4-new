<?php
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();

$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
    'year' => null,
    '_controller' => 'Modules\\Calendar\\Controller\\LeapYearController::indexAction',
)));

$routes->add('index', new Routing\Route('/', array(
    '_controller' => 'Chisimba\\Controller\\DefaultController::indexAction',
)));

$routes->add('modulecatalogue', new Routing\Route('/modulecatalogue/*', array(
    '_controller' => 'Modules\\ModuleCatalogue\\DefaultController::indexAction',
)));

return $routes;
