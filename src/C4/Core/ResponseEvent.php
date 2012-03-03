<?php
 
// src/C4/ResponseEvent.php
 
namespace C4\Core;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;
use C4\Core\View;
 
class ResponseEvent extends Event
{
    private $request;
    private $response;
 
    public function __construct(Response $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }
 
    public function getResponse()
    {
        return $this->response;
    }
 
    public function getRequest()
    {
        return $this->request;
    }
}
