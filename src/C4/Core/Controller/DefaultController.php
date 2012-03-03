<?php
 
// /src/C4/Controller/DefaultController.php
 
namespace C4\Core\Controller;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use C4\Core\Framework;
 
class DefaultController
{
    public function indexAction(Request $request)
    {
        $config = \C4\Core\Framework::getConfiguration();
    	$response = new Response('Welcome to C4!');
    	
        //$response->setTtl(10);
     
        return $response;
    }
}