<?php

namespace C4\Library\Session;

use C4\Library\Session\ManagerInterface as Manager,
    C4\Library\Session\SaveHandler\SaveHandlerInterface as SaveHandler,
    C4\Library\Session\Storage\StorageInterface as Storage,
    C4\Library\Session\Configuration\ConfigurationInterface as Configuration;

/**
 * Base ManagerInterface implementation
 *
 * Defines common constructor logic and getters for Storage and Configuration
 *
 */
abstract class AbstractManager implements Manager
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * Default configuration class to use when no configuration provided
     * @var string
     */
    protected $configDefaultClass = 'C4\\Library\\Session\\Configuration\\SessionConfiguration';

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Default storage class to use when no storage provided
     * @var string
     */
    protected $storageDefaultClass = 'C4\\Library\\Session\\Storage\\SessionStorage';

    /**
     * @var SaveHandler
     */
    protected $saveHandler;


    /**
     * Constructor
     *
     * Allow passing a configuration object or class name, a storage object or 
     * class name, or an array of configuration.
     * 
     * @param  Configuration $config 
     * @param  Storage $storage 
     * @param  SaveHandler $saveHandler
     * @return void
     */
    public function __construct(Configuration $config = null, Storage $storage = null, SaveHandler $saveHandler = null)
    {
        $this->setOptions($config);
        $this->setStorage($storage);
        if ($saveHandler) {
            $this->setSaveHandler($saveHandler);
        }
    }

    /**
     * Set configuration object
     *
     * @param  null|Configuration $config 
     * @return void
     */
    public function setOptions(Configuration $config = null)
    {
        if (null === $config) {
            $config = new $this->configDefaultClass();
            if (!$config instanceof Configuration) {
                throw new Exception\InvalidArgumentException('Default configuration type provided is invalid; must implement C4\\Library\\Session\\Configuration');
            }
        }

        $this->config = $config;
    }

    /**
     * Retrieve configuration object
     * 
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set session storage object
     *
     * @param  null|Storage $storage 
     * @return void
     */
    public function setStorage(Storage $storage = null)
    {
        if (null === $storage) {
            $storage = new $this->storageDefaultClass();
            if (!$storage instanceof Storage) {
                throw new Exception\InvalidArgumentException('Default storage type provided is invalid; must implement C4\\Library\\Session\\Storage');
            }
        }

        $this->storage = $storage;
    }

    /**
     * Retrieve storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set session save handler object
     *
     * @param SaveHandler $saveHandler
     * @return void
     */
    public function setSaveHandler(SaveHandler $saveHandler)
    {
        if ($saveHandler === null) {
            return ;
        }
        $this->saveHandler = $saveHandler;
    }

    /**
     * Get SaveHandler Object
     *
     * @return SaveHandler
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }
}