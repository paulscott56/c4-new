<?php
namespace C4\Core;

use C4\Core\View\BaseLayout;
use C4\Core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class Controller 
{
	public $baseLayout;
	
	public function __construct()
	{
		
	}
	
	public function setContent($content)
	{
		$this->baseLayout = new BaseLayout();
		$this->baseLayout->setPageContent($content); 
	}
	
	public function renderPage()
	{
		return $this->baseLayout->documentFactory();
	}
	
	public function prepareResponse(Request $request)
	{
		$response = new Response($this->renderPage(), 200, array('content-type' => 'text/html'));
    	//$response->headers->setCookie(new Cookie('C4', 'test'));
    	$response->setCache(array(
            'etag'          => 'c4',
            'last_modified' => new \DateTime(),
            'max_age'       => 600,
            's_maxage'      => 600,
            'private'       => false,
            'public'        => true,
        ));
        $response->setCharset('UTF-8');
        $response->setTtl(10);
    	return $response;
	}
}