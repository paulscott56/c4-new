<?php

// src/C4/Framework.php

namespace C4\Core;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


class Framework extends HttpKernel\HttpKernel
{
	public $mainConfiguration;
	public $yamlParser;
	private $logger;
	
    public function __construct($routes, $logger)
    {
        $this->logger = $logger;
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../../logging/System_Log.log', \Monolog\Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        $this->logger->addInfo('My logger is now ready');
        
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
        
        $dispatcher->addSubscriber(new ExceptionListener(function (Request $request) {
            $msg = 'Something went wrong! ('.$request->get('exception')->getMessage().')';
            return new Response($msg, 500);
        }));
        
        // get the main configuration
        $this->parseMainConfiguration();
        // set up the database connection
        
        // set up the templating system
        
        // Profit!!!1
        
        parent::__construct($dispatcher, $resolver);
    }
    
    public function parseMainConfiguration() 
    {
    	try {
    	    $this->logger->addInfo('Parsing main configs');
    		// The YAML parser object can be re-used, so we instantiate it here
    		$this->yamlParser = new Parser();
    		$this->mainConfiguration = $this->yamlParser->parse(file_get_contents(__DIR__.'/../../../config/systemConfig.yml'));
    	} catch (ParseException $e) {
    		printf("Unable to parse the YAML string: %s", $e->getMessage());
    		die();
    	}
    	return $this;
    }
    
    public function parseGeneralConfiguration($configFile) 
    {
    	try {
    		$configuration = $this->yamlParser->parse(file_get_contents(__DIR__.'/../../../config/'.$configFile.'.yml'));
    		return $configuration;
    	} catch (ParseException $e) {
    		printf("Unable to parse the YAML string: %s", $e->getMessage());
    	}
    }
    
    public function yamlWriter(array $data, $filename) 
    {
    	$dumper = new Dumper();
    	$yaml = $dumper->dump($data);
    	file_put_contents(__DIR__.'/../../../config/'.$filename.'.yml', $yaml);
    }
    
    public function getConfiguration() 
    {
    	return self::parseMainConfiguration();
    }
    
    /**
    * Set PHP configuration settings
    *
    * @param  array $settings
    * @return Framework
    */
    public function setPhpSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            if (is_scalar($value)) {
                ini_set($key, $value);
            }
        }
    
        return $this;
    }
    
    /**
     * Set include path
     *
     * @param  array $paths
     * @return Framework
     */
    public function setIncludePaths(array $paths)
    {
        $path = implode(PATH_SEPARATOR, $paths);
        set_include_path($path . PATH_SEPARATOR . get_include_path());
        return $this;
    }
    
}