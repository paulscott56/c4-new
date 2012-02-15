<?php
 
// tests/Chisimba/Tests/FrameworkTest.php
 
namespace Chisimba\Tests;
 
use Chisimba\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $routes = include __DIR__.'/../../../src/app.php';
        $sc = include __DIR__.'/../../../src/container.php';
    }

    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());
 
        $response = $framework->handle(new Request());
 
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    /**
     * @todo fix this
     * Enter description here ...
     */
    public function testErrorHandling()
    {
    	$framework = $this->getFrameworkForException(new \RuntimeException());
    
    	$response = $framework->handle(new Request(array('/do_nothing/')));
    
    	$this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testControllerResponse()
    {
        $routes = include __DIR__.'/../../../src/app.php';
        //$matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
    	//$matcher
    	//->expects($this->once())
    	//->method('match')
    	//->will($this->returnValue(array(
        //        '_route' => '/',
        //        '_controller' => function () {
    	//return new Response('Welcome to Chisimba 4!');
    //	}
    //	)))
    //	;
    	//$resolver = new ControllerResolver();
    
    	$framework = new Framework($routes);
    
    	$response = $framework->handle(new Request());
    
    	$this->assertEquals(200, $response->getStatusCode());
    	$this->assertContains('Welcome to Chisimba 4!', $response->getContent());
    }
 
    protected function getFrameworkForException($exception)
    {
        $routes = include __DIR__.'/../../../src/app.php';
        return new Framework($routes);
    }
}
