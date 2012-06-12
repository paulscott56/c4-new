<?php 

namespace C4\Library\Authentication\Storage;


class NonPersistent implements StorageInterface
{
	/**
     * Holds the actual auth data
     */
    protected $_data;

    /**
     * Returns true if and only if storage is empty
     *
     * @throws C4\Library\Authentication\Storage\Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->_data);
    }

    /**
     * Returns the contents of storage
     * Behavior is undefined when storage is empty.
     *
     * @return mixed
     */
    public function read()
    {
        return $this->_data;
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->_data = $contents;
    }

    /**
     * Clears contents from storage
     *
     * @return void
     */
    public function clear()
    {
        $this->_data = null;
    }
}