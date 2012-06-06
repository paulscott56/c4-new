<?php 

namespace C4\Library\Authentication;

class AuthenticationService
{
	/**
     * Persistent storage handler
     *
     * @var C4\Library\Authentication\Storage\StorageInterface
     */
    protected $storage = null;

    /**
     * Authentication adapter
     *
     * @var C4\Library\Authentication\Adapter\AdapterInterface
     */
    protected $adapter = null;
    
    /**
     * Constructor 
     * 
     * @param  Storage\StorageInterface $storage  
     * @param  Adapter\AdapterInterface $adapter 
     * @return void
     */
    public function __construct(Storage\StorageInterface $storage = null, Adapter\AdapterInterface $adapter = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }
        if (null !== $adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Returns the authentication adapter
     *
     * The adapter does not have a default if the storage adapter has not been set.
     *
     * @return C4\Library\Authentication\Adapter\AdapterInterface|null
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
	
    /**
     * Sets the authentication adapter
     *
     * @param  C4\Library\Authentication\Adapter\AdapterInterface $adapter
     * @return C4\Library\Authentication\AuthenticationService Provides a fluent interface
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return C4\Library\Authentication\Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session());
        }

        return $this->storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  C4\Library\Authentication\Storage\StorageInterface $storage
     * @return C4\Library\Authentication\AuthenticationService Provides a fluent interface
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }
    
    /**
     * Authenticates against the supplied adapter
     *
     * @param  C4\Library\Authentication\Adapter\AdapterInterface $adapter
     * @return C4\Library\Authentication\Result
     * @throws C4\Library\Authentication\Exception\RuntimeException
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        if (!$adapter) {
            if (!$adapter = $this->getAdapter()) {
                throw new Exception\RuntimeException('An adapter must be set or passed prior to calling authenticate()');
            }
        }
        $result = $adapter->authenticate();

        /**
         * Prevent multiple succesive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result->isValid()) {
            $this->getStorage()->write($result->getIdentity());
        }

        return $result;
    }

    /**
     * Returns true if and only if an identity is available from storage
     *
     * @return boolean
     */
    public function hasIdentity()
    {
        return !$this->getStorage()->isEmpty();
    }
    
    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        $storage = $this->getStorage();

        if ($storage->isEmpty()) {
            return null;
        }

        return $storage->read();
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->getStorage()->clear();
    }
}