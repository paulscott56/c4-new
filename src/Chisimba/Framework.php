<?php

// src/Chisimba/Framework.php

namespace Chisimba;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class Framework extends HttpKernel\HttpKernel
{
	public $mainConfiguration;
	public $yamlParser;
	
    public function __construct($routes)
    {
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
        
        // The YAML parser object can be re-used, so we instantiate it here
        $this->yamlParser = new Parser();
        
        // get the main configuration
        $this->parseMainConfiguration();
        
        parent::__construct($dispatcher, $resolver);
    }
    
    public function parseMainConfiguration() 
    {
    	try {
    		$this->mainConfiguration = $this->yamlParser->parse(file_get_contents(__DIR__.'/../../config/systemConfig.yml'));
    	} catch (ParseException $e) {
    		printf("Unable to parse the YAML string: %s", $e->getMessage());
    	}
    }
    
    public function parseGeneralConfiguration($configFile) 
    {
    	try {
    		$configuration = $this->yamlParser->parse(file_get_contents(__DIR__.'/../../config/'.$configFile.'.yml'));
    		return $configuration;
    	} catch (ParseException $e) {
    		printf("Unable to parse the YAML string: %s", $e->getMessage());
    	}
    }
    
    public function yamlWriter(array $data, $filename) 
    {
    	$dumper = new Dumper();
    	$yaml = $dumper->dump($data);
    	file_put_contents(__DIR__.'/../../config/'.$filename.'.yml', $yaml);
    }
}