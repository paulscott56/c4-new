<?php

namespace C4\Library\Session\Storage;

/**
 * Session storage in $_SESSION
 *
 * Replaces the $_SESSION superglobal with an ArrayObject that allows for 
 * property access, metadata storage, locking, and immutability.
 */
class SessionStorage extends ArrayStorage
{
    /**
     * Constructor
     *
     * Sets the $_SESSION superglobal to an ArrayObject, maintaining previous 
     * values if any discovered.
     * 
     * @param  null|array|ArrayAccess $input 
     * @param  int $flags 
     * @param  string $iteratorClass 
     * @return void
     */
    public function __construct($input = null, $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        $resetSession = true;
        if ((null === $input) && isset($_SESSION)) {
            $input = $_SESSION;
            if (is_object($input) && $_SESSION instanceof \ArrayObject) {
                $resetSession = false;
            } elseif (is_object($input) && !$_SESSION instanceof \ArrayObject) {
                $input = (array) $input;
            }
        } elseif (null === $input) {
            $input = array();
        }
        parent::__construct($input, $flags, $iteratorClass);
        if ($resetSession) {
            $_SESSION = $this;
        }
    }

    /**
     * Destructor
     *
     * Resets $_SESSION superglobal to an array, by casting object using 
     * getArrayCopy().
     * 
     * @return void
     */
    public function __destruct()
    {
        $_SESSION = (array) $this->getArrayCopy();
    }

    /**
     * Load session object from an existing array
     *
     * Ensures $_SESSION is set to an instance of the object when complete.
     * 
     * @param  array $array 
     * @return SessionStorage
     */
    public function fromArray(array $array)
    {
        $this->exchangeArray($array);
        if ($_SESSION !== $this) {
            $_SESSION = $this;
        }
        return $this;
    }

    /**
     * Mark object as immutable
     * 
     * @return void
     */
    public function markImmutable()
    {
        $this['_IMMUTABLE'] = true;
    }

    /**
     * Determine if this object is immutable
     * 
     * @return bool
     */
    public function isImmutable()
    {
        return (isset($this['_IMMUTABLE']) && $this['_IMMUTABLE']);
    }
}