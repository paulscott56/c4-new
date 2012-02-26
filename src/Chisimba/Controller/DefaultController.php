<?php
 
// /src/Chisimba/Controller/DefaultController.php
 
namespace Chisimba\Controller;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Chisimba\Framework;
 
class DefaultController
{
    public function indexAction(Request $request)
    {
        $config = \Chisimba\Framework::getConfiguration();
    	$response = new Response('Welcome to Chisimba 4!');
        $response->setTtl(10);
 
        return $response;
    }
}
