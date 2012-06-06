<?php 

namespace C4\Library\Authentication\Adapter;

interface AdapterInterface
{
	/**
     * Performs an authentication attempt
     *
     * @return C4\Library\Authentication\Result
     * @throws C4\Library\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate();
}