<?php

namespace C4\Core\Controller;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use C4\Core\Framework;
use C4\Core\Controller;
 
class DefaultController extends Controller
{
    public $logger;
    public function indexAction(Request $request)
    {
    	$config = Framework::getConfiguration();
    	$response = new Response($this->renderPage(), 200, array('content-type' => 'text/html'));
    	$response->headers->setCookie(new Cookie('C4', 'test'));
    	$response->setCache(array(
            'etag'          => 'c4',
            'last_modified' => new \DateTime(),
            'max_age'       => 600,
            's_maxage'      => 600,
            'private'       => false,
            'public'        => true,
        ));
        $response->setCharset('UTF-8');
        //$response->setTtl(10);
    	//$this->js->dump();
        
     
    	//var_dump($request->cookies);
        return $response;
    }
}
