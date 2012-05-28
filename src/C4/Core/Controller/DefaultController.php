<?php

namespace C4\Core\Controller;
 

use C4\Core\Framework;
use C4\Core\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
 
class DefaultController extends Controller
{
    public $logger;
    private $config;
    
    public function __construct()
    {
    	$this->config = Framework::getConfiguration();
    }
    
    public function indexAction(Request $request)
    {
    	$this->setContent($this->config);
    	$response = $this->prepareResponse($request);
    	return $response;
    }
}