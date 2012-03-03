<?php
 
// src/C4/Core/ExceptionListener.php
 
namespace C4\Core;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
 
class ExceptionListener implements EventSubscriberInterface
{
    public function onView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
 
        if (is_string($response)) {
            $event->setResponse(new Response($response));
        }
    }
 
    public static function getSubscribedEvents()
    {
        return array('kernel.view' => 'onView');
    }
}
