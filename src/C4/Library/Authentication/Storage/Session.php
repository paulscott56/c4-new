<?php

namespace C4\Library\Authentication\Storage;

use C4\Library\Authentication\Storage\StorageInterface as AuthenticationStorage,
    C4\Library\Session\Container as SessionContainer,
    C4\Library\Session\ManagerInterface as SessionManager;

/**
 * @category   C4
 * @package    C4 Authentication
 * @subpackage Storage
 */
class Session implements StorageInterface
{
    /**
     * Default session namespace
     */
    const NAMESPACE_DEFAULT = 'C4Auth';

    /**
     * Default session object member name
     */
    const MEMBER_DEFAULT = 'storage';

    /**
     * Object to proxy $_SESSION storage
     *
     * @var SessionContainer
     */
    protected $session;

    /**
     * Session namespace
     *
     * @var mixed
     */
    protected $namespace = self::NAMESPACE_DEFAULT;

    /**
     * Session object member
     *
     * @var mixed
     */
    protected $member = self::MEMBER_DEFAULT;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @return void
     */
    public function __construct($namespace = null, $member = null, SessionManager $manager = null)
    {
        if ($namespace !== null) {
            $this->namespace = $namespace;
        }
        if ($member !== null) {
            $this->member = $member;
        }
        $this->session   = new SessionContainer($this->namespace, $manager);
    }

    /**
     * Returns the session namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the name of the session object member
     *
     * @return string
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Defined by C4\Library\Authentication\Storage\StorageInterface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !isset($this->session->{$this->member});
    }

    /**
     * Defined by C4\Library\Authentication\Storage\StorageInterface
     *
     * @return mixed
     */
    public function read()
    {
        return $this->session->{$this->member};
    }

    /**
     * Defined by C4\Library\Authentication\Storage\StorageInterface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->session->{$this->member} = $contents;
    }

    /**
     * Defined by C4\Library\Authentication\Storage\StorageInterface
     *
     * @return void
     */
    public function clear()
    {
        unset($this->session->{$this->member});
    }
}