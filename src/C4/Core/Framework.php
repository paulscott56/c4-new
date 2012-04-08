<?php
namespace C4\Core;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
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

    public function getSession()
    {

    }

    public function setSession()
    {

    }

    
    private function getMongoODM()
    {
        // ODM Classes
        $classLoader = new ClassLoader('Doctrine\ODM\MongoDB', '/vendor/doctrine-mongodb-odm/lib');
        $classLoader->register();

        // Common Classes
        $classLoader = new ClassLoader('Doctrine\Common',
                                       '/vendor/doctrine-mongodb-odm/lib/vendor/doctrine-common/lib');
        $classLoader->register();

        // MongoDB Classes
        $classLoader = new ClassLoader('Doctrine\MongoDB',
                                       '/vendor/doctrine-mongodb-odm/lib/vendor/doctrine-mongodb/lib');
        $classLoader->register();

        // Document classes
        $classLoader = new ClassLoader('Documents', __DIR__);
        $classLoader->register();

        $config = new Configuration();
        $config->setProxyDir(__DIR__ . '/cache');
        $config->setProxyNamespace('Proxies');

        $config->setHydratorDir(__DIR__ . '/cache');
        $config->setHydratorNamespace('Hydrators');

        $reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        //$reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\\');
        //$config->setMetadataDriverImpl(new AnnotationDriver($reader, __DIR__ . '/Documents'));

        $this->documentManager = DocumentManager::create(new Connection(), $config);
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