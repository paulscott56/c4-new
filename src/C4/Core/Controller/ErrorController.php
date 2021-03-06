<?php
 
// /src/C4/Controller/ErrorController.php
 
namespace C4\Core\Controller;
 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
 
class ErrorController
{
    public function exceptionAction(FlattenException $exception)
    {
        $msg = 'Something went wrong! ('.$exception->getMessage().')';
 
        return new Response($msg, $exception->getStatusCode());
        
        die();
    }
}