<?php

namespace C4\Library\Session\Storage;

use Traversable,
    ArrayAccess,
    Serializable,
    Countable;

/**
 * Session storage interface
 *
 * Defines the minimum requirements for handling userland, in-script session 
 * storage (e.g., the $_SESSION superglobal array).
 *
 */
interface StorageInterface extends Traversable, ArrayAccess, Serializable, Countable
{
    public function getRequestAccessTime();
    public function lock($key = null);
    public function isLocked($key = null);
    public function unlock($key = null);
    public function markImmutable();
    public function isImmutable();

    public function setMetadata($key, $value, $overwriteArray = false);
    public function getMetadata($key = null);

    public function clear($key = null);

    public function toArray();
    public function fromArray(array $array);
}