<?php

namespace C4\Library\Session;

use C4\Library\EventManager\EventManagerInterface,
    C4\Library\Session\Configuration\ConfigurationInterface as Configuration,
    C4\Library\Session\Storage\StorageInterface as Storage,
    C4\Library\Session\SaveHandler\SaveHandlerInterface as SaveHandler;

/**
 * Session manager interface
 *
 */
interface ManagerInterface
{
    public function __construct(Configuration $config = null, Storage $storage = null, SaveHandler $saveHandler = null);

    public function getConfig();
    public function getStorage();
    public function getSaveHandler();
    
    public function sessionExists();
    public function start();
    public function destroy();
    public function writeClose();

    public function getName();
    public function setName($name);
    public function getId();
    public function setId($id);
    public function regenerateId();

    public function rememberMe($ttl = null);
    public function forgetMe();
    public function expireSessionCookie();

    public function setValidatorChain(EventManagerInterface $chain);
    public function getValidatorChain();
    public function isValid();
}