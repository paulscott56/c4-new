<?php 

namespace C4\Library\TimeSync;

use C4\Library\TimeSync\Exception;

abstract class Protocol
{
	/**
     * Holds the current socket connection
     *
     * @var array
     */
    protected $_socket;

    /**
     * Exceptions that might have occured
     *
     * @var array
     */
    protected $_exceptions;

    /**
     * Hostname for timeserver
     *
     * @var string
     */
    protected $_timeserver;

    /**
     * Holds information passed/returned from timeserver
     *
     * @var array
     */
    protected $_info = array();

    /**
     * Abstract method that prepares the data to send to the timeserver
     *
     * @return mixed
     */
    abstract protected function _prepare();

    /**
     * Abstract method that reads the data returned from the timeserver
     *
     * @return mixed
     */
    abstract protected function _read();

    /**
     * Abstract method that writes data to to the timeserver
     *
     * @param  string $data Data to write
     * @return void
     */
	/**
     * Abstract method that writes data to to the timeserver
     *
     * @param  string $data Data to write
     * @return void
     */
    abstract protected function _write($data);

    /**
     * Abstract method that extracts the binary data returned from the timeserver
     *
     * @param  string|array $data Data returned from the timeserver
     * @return integer
     */
    abstract protected function _extract($data);

    /**
     * Connect to the specified timeserver.
     *
     * @return void
     * @throws Exception\ExceptionInterface When the connection failed
     */
    protected function _connect()
    {
        $socket = @fsockopen($this->_timeserver, $this->_port, $errno, $errstr,
                             TimeSync::$options['timeout']);
        if ($socket === false) {
            throw new Exception\RuntimeException('could not connect to ' .
                "'$this->_timeserver' on port '$this->_port', reason: '$errstr'");
        }

        $this->_socket = $socket;
    }

    /**
     * Disconnects from the peer, closes the socket.
     *
     * @return void
     */
    protected function _disconnect()
    {
        @fclose($this->_socket);
        $this->_socket = null;
    }
    
    /**
     * Return information sent/returned from the timeserver
     *
     * @return  array
     */
    public function getInfo()
    {
        if (empty($this->_info) === true) {
            $this->_write($this->_prepare());
            $timestamp = $this->_extract($this->_read());
        }

        return $this->_info;
    }

    /**
     * Query this timeserver without using the fallback mechanism
     *
     * @param  string|\C4\Locale\Locale $locale (Optional) Locale
     * @return \C4\Date\Date
     */
    public function getDate($locale = null)
    {
        $this->_write($this->_prepare());
        $timestamp = $this->_extract($this->_read());

        $date = new \C4\Date\Date($this, null, $locale);
        return $date;
    }
    
}