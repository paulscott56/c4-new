<?php
 
// tests/C4/Tests/FrameworkTest.php
// some stuff 
namespace C4\Tests;
 
use C4\Core\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;
    
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
        
        //$matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
    	//$matcher
    	//->expects($this->once())
    	//->method('match')
    	//->will($this->returnValue(array(
        //        '_route' => '/',
        //        '_controller' => function () {
    	//return new Response('Welcome to C4!');
    //	}
    //	)))
    //	;
    	//$resolver = new ControllerResolver();
    
    	$framework = $this->getFramework();
    
    	$response = $framework->handle(new Request());
    
    	$this->assertEquals(200, $response->getStatusCode());
    	//$this->assertContains('Welcome to C4!', $response->getContent());
    	$this->assertNotEmpty($response->getContent());
    }
    
    public function testYamlWriter()
    {
    	$framework = $this->getFramework();
    	$framework->yamlWriter(array('data' => array(1, 2, 3, 4, 5), 'second' => 'test String'), 'phpunit_test');
    	$this->assertTrue(file_exists(__DIR__.'/../../../config/phpunit_test.yml'));
    }
    
    public function testParseGeneralConfiguration()
    {
    	$framework = $this->getFramework();
    	$this->assertEquals(array('data' => array(1, 2, 3, 4, 5), 'second' => 'test String'), 
    	                    $framework->parseGeneralConfiguration('phpunit_test'));
    	
    	// exception test
    	$framework->yamlWriter(array('data' => array('monkey', '')), 'phpunitException.yml');
    	$framework->parseGeneralConfiguration('phpunitException.yml');
    }
    
    public function testParseMainConfigurationException()
    {
    	$framework = $this->getFramework();
    	$framework->parseMainConfiguration();
    }
    
    public function testParseGeneralConfigurationException()
    {
    	$framework = $this->getFramework();
    	$framework->parseGeneralConfiguration('phpunit_testException');
    }
    
    public function testSetPhpSettings()
    {
        $framework = $this->getFramework();
    	$framework->setPhpSettings(array('max_execution_time' => 80));
        $this->assertEquals(ini_get('max_execution_time'), 80);
    }
    
    public function testSetIncludePath() 
    {
    	$framework = $this->getFramework();
    	$framework->setIncludePaths(array('/var/www/', '/var/www/html'));
    	$this->assertContains('/var/www/'.PATH_SEPARATOR.'/var/www/html', get_include_path());
    }
    
    public function testLogVisitor()
    {
    	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    	$framework = $this->getFramework();
    	$this->assertTrue($framework->logVisitor());
    }
 
    protected function getFrameworkForException($exception)
    {
        $routes = include __DIR__.'/../../../src/app.php';
        $logger = new \Monolog\Logger(__DIR__.'/../../../logging/SystemPHPUnit_Log.log');
        $this->logger = $logger;
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../../logging/SystemPHPUnit_Log.log', \Monolog\Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        return new Framework($routes, $this->logger);
    }
    
    protected function getFramework()
    {
        $routes = include __DIR__.'/../../../src/app.php';
        $logger = new \Monolog\Logger(__DIR__.'/../../../logging/SystemPHPUnit_Log.log');
        $this->logger = $logger;
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../../logging/SystemPHPUnit_Log.log', \Monolog\Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        $framework = new Framework($routes, $this->logger);
        return $framework;
    }
    
    
}
