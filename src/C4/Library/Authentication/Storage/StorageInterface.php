<?php 

namespace C4\Library\Authentication\Storage;

interface StorageInterface
{
	 /**
     * Returns true if and only if storage is empty
     *
     * @throws C4\Library\Authentication\Storage\Exception\ExceptionInterface If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws C4\Library\Authentication\Storage\Exception\ExceptionInterface If reading contents from storage is impossible
     * @return mixed
     */
    public function read();

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws C4\Library\Authentication\Storage\Exception\ExceptionInterface If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents);

    /**
     * Clears contents from storage
     *
     * @throws C4\Library\Authentication\Storage\Exception\ExceptionInterface If clearing contents from storage is impossible
     * @return void
     */
    public function clear();
	
}