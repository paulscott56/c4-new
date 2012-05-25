<?php
namespace C4\Core;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\JsCompressorFilter as YuiCompressorFilter;

    


class Framework extends HttpKernel\HttpKernel
{
    public static $mainConfiguration;
    public $yamlParser;
    private $logger;
    public $entityManager;
    public $documentManager;

    public function __construct($routes, $logger)
    {
    	// get the main configuration
    	$this->parseMainConfiguration();
    	
    	$this->logger = $logger;
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../../logging/System_Log.log', \Monolog\Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        $this->logger->addInfo('My logger is now ready');

        try {
            $context = new Routing\RequestContext();
            $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
            $resolver = new HttpKernel\Controller\ControllerResolver();

            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
            $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

            $dispatcher->addSubscriber(new ExceptionListener(function (Request $request)
                {
                    $msg = 'Something went wrong! ('.$request->get('exception')->getMessage().')';
                    return new Response($msg, 500);
                }
            ));

            // get ORM
            $this->getORM();
            $this->getMongoODM();
            // set up the templating system

            // Profit!!!1
            
            // wrap up the JS
            $this->js = new AssetCollection(array(
                new FileAsset(__DIR__.'/../../../assets/js/bootstrap.js'),
                //new FileAsset(__DIR__.'/application.js'),
            ), array(
                   new YuiCompressorFilter(__DIR__.'/../../../assets/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar'),
            ));

            //header('Content-Type: application/js');
            //echo $this->js->dump();
            
            
        }
        catch(Exception $e)
        {
        	echo "oops";
        }
        
        parent::__construct($dispatcher, $resolver);
    }

    public function parseMainConfiguration()
    {
        try {
            // The YAML parser object can be re-used, so we instantiate it here
            $this->yamlParser = new Parser();
            self::$mainConfiguration = $this->yamlParser->parse(file_get_contents(__DIR__.'/../../../config/systemConfig.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
            die();
        }
        return $this;
    }

    public function parseGeneralConfiguration($configFile)
    {
        try {
            $configuration = $this->yamlParser->parse(
                file_get_contents(__DIR__.'/../../../config/'.$configFile.'.yml')
            );
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

    public static function getConfiguration()
    {
        return self::$mainConfiguration;
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

    
    private function getMongoODM()
    {
    	$config = new \Doctrine\ODM\MongoDB\Configuration();
    	$config->setProxyDir('/var/www/c4/cache');
    	$config->setProxyNamespace('Proxies');
    	
    	$config->setHydratorDir('/var/www/c4/cache');
    	$config->setHydratorNamespace('Hydrators');
    	
    	
    	
    	$reader = new \Doctrine\Common\Annotations\AnnotationReader();
    	$config->setMetadataDriverImpl(new AnnotationDriver($reader, __DIR__ . '/Documents'));
    	AnnotationDriver::registerAnnotationClasses();
    	//$reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\Annotations\\');
    	//var_dump($reader);
    	
    	$dm = \Doctrine\ODM\MongoDB\DocumentManager::create(new Connection(), $config);
    	
        $this->documentManager = $dm;
        return $this;
    }

    private function getORM()
    {
        // set up the database connection
        $loader = new \Doctrine\Common\ClassLoader("Doctrine");
        $loader->register();

        $dbParams = array(
                    'driver' => 'pdo_mysql',
                    'user' => 'root',
                    'password' => '',
                    'dbname' => 'c4'
        );
        $path = array(__DIR__ . '/entities');
        $config = Setup::createAnnotationMetadataConfiguration($path, true);
        $this->entityManager = EntityManager::create($dbParams, $config);

        return $this;
    }
    
    /**
     * Grabs the client IP address
     *
     * This function should be used to grab IP addresses, even those behind proxies, to gather data from
     *
     * @return string $ip
     */
    private function getIpAddr() {
    	if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	}
    	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    		// pass from proxy
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	}
    	else {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	}
    	return $ip;
    }
    
    public function logVisitor()
    {
    	// test user
    	$hitCount = new Model\VisitCounter();
    	$hitCount->setIp($this->getIpAddr());
    	$hitCount->setCounter(1);
    	$hitCount->setHostname();
    	$hitCount->incrementCounter();
    	
    	// persist
    	$this->documentManager->persist($hitCount);
    	$this->documentManager->flush();
    	
    	return true;
    }
    
}