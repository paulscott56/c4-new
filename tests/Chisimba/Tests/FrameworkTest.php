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
    
    public function testYamlWriter()
    {
    	$routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$framework->yamlWriter(array('data' => array(1, 2, 3, 4, 5), 'second' => 'test String'), 'phpunit_test');
    	$this->assertTrue(file_exists(__DIR__.'/../../../config/phpunit_test.yml'));
    }
    
    public function testParseGeneralConfiguration()
    {
    	$routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$this->assertEquals(array('data' => array(1, 2, 3, 4, 5), 'second' => 'test String'), 
    	                    $framework->parseGeneralConfiguration('phpunit_test'));
    	
    	// exception test
    	$framework->yamlWriter(array('data' => array('monkey', '')), 'phpunitException.yml');
    	$framework->parseGeneralConfiguration('phpunitException.yml');
    }
    
    public function testParseMainConfigurationException()
    {
    	$routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$framework->parseMainConfiguration();
    }
    
    public function testParseGeneralConfigurationException()
    {
    	$routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$framework->parseGeneralConfiguration('phpunit_testException');
    }
    
    public function testSetPhpSettings()
    {
        $routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$framework->setPhpSettings(array('max_execution_time' => 80));
        $this->assertEquals(ini_get('max_execution_time'), 80);
    }
    
    public function testSetIncludePath() 
    {
    	$routes = include __DIR__.'/../../../src/app.php';
    	$framework = new Framework($routes);
    	$framework->setIncludePaths(array('/var/www/', '/var/www/html'));
    	$this->assertContains('/var/www/'.PATH_SEPARATOR.'/var/www/html', get_include_path());
    }
 
    protected function getFrameworkForException($exception)
    {
        $routes = include __DIR__.'/../../../src/app.php';
        return new Framework($routes);
    }
    
    
}