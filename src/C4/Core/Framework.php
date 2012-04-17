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

class Framework extends HttpKernel\HttpKernel
{
    public static $mainConfiguration;
    public $yamlParser;
    private $logger;
    public $entityManager;
    public $documentManager;

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

        $dispatcher->addSubscriber(new ExceptionListener(function (Request $request)
        {
            $msg = 'Something went wrong! ('.$request->get('exception')->getMessage().')';
            return new Response($msg, 500);
        }
        ));

        // get the main configuration
        $this->parseMainConfiguration();

        // get ORM
        $this->getORM();
        $this->getMongoODM();
        // set up the templating system

        // Profit!!!1
        
        // test user
        $user = new Model\User();
        $user->setUsername('Paul');
        $user->setPassword('test');
        
        // persist
        $this->documentManager->persist($user);
        
        $this->documentManager->flush();
        
        $users = $this->documentManager->getRepository('C4\Core\Model\User')->findOneBy(array('password' => 'test'));
        //var_dump($users); die();
        parent::__construct($dispatcher, $resolver);
        //var_dump($request);
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

    public function getSession()
    {

    }

    public function setSession()
    {

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
}