<?php
 
// src/C4/StringResponseListener.php
 
namespace C4\Core;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use C4\Core\View;
 
class StringResponseListener implements EventSubscriberInterface
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
