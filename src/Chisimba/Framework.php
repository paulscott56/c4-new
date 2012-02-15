<?php

// src/Chisimba/Framework.php

namespace Chisimba;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Framework extends HttpKernel\HttpKernel
{
    public function __construct($routes)
    {
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

        parent::__construct($dispatcher, $resolver);
    }
}