<?php

namespace C4\Library\Authentication\Adapter;

use C4\Library\Authentication\Result as AuthenticationResult;

class Digest implements AdapterInterface
{
    /**
     * Filename against which authentication queries are performed
     *
     * @var string
     */
    protected $_filename;

    /**
     * Digest authentication realm
     *
     * @var string
     */
    protected $_realm;

    /**
     * Digest authentication user
     *
     * @var string
     */
    protected $_username;

    /**
     * Password for the user of the realm
     *
     * @var string
     */
    protected $_password;

    /**
     * Sets adapter options
     *
     * @param  mixed $filename
     * @param  mixed $realm
     * @param  mixed $username
     * @param  mixed $password
     * @return void
     */
    public function __construct($filename = null, $realm = null, $username = null, $password = null)
    {
        $options = array('filename', 'realm', 'username', 'password');
        foreach ($options as $option) {
            if (null !== $$option) {
                $methodName = 'set' . ucfirst($option);
                $this->$methodName($$option);
            }
        }
    }

    /**
     * Returns the filename option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Sets the filename option value
     *
     * @param  mixed $filename
     * @return C4\Library\Authentication\Adapter\Digest Provides a fluent interface
     */
    public function setFilename($filename)
    {
        $this->_filename = (string) $filename;
        return $this;
    }

    /**
     * Returns the realm option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getRealm()
    {
        return $this->_realm;
    }

    /**
     * Sets the realm option value
     *
     * @param  mixed $realm
     * @return C4\Library\Authentication\Adapter\Digest Provides a fluent interface
     */
    public function setRealm($realm)
    {
        $this->_realm = (string) $realm;
        return $this;
    }

    /**
     * Returns the username option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the username option value
     *
     * @param  mixed $username
     * @return C4\Library\Authentication\Adapter\Digest Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }

    /**
     * Returns the password option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the password option value
     *
     * @param  mixed $password
     * @return C4\Library\Authentication\Adapter\Digest Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * Defined by C4\Library\Authentication\Adapter\AdapterInterface
     *
     * @throws C4\Library\Authentication\Adapter\Exception\ExceptionInterface
     * @return C4\Library\Authentication\Result
     */
    public function authenticate()
    {
        $optionsRequired = array('filename', 'realm', 'username', 'password');
        foreach ($optionsRequired as $optionRequired) {
            if (null === $this->{"_$optionRequired"}) {
                throw new Exception\RuntimeException("Option '$optionRequired' must be set before authentication");
            }
        }

        if (false === ($fileHandle = @fopen($this->_filename, 'r'))) {
            throw new Exception\UnexpectedValueException("Cannot open '$this->_filename' for reading");
        }

        $id       = "$this->_username:$this->_realm";
        $idLength = strlen($id);

        $result = array(
            'code'  => AuthenticationResult::FAILURE,
            'identity' => array(
                'realm'    => $this->_realm,
                'username' => $this->_username,
                ),
            'messages' => array()
            );

        while ($line = trim(fgets($fileHandle))) {
            if (substr($line, 0, $idLength) === $id) {
                if ($this->_secureStringCompare(substr($line, -32), md5("$this->_username:$this->_realm:$this->_password"))) {
                    $result['code'] = AuthenticationResult::SUCCESS;
                } else {
                    $result['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
                    $result['messages'][] = 'Password incorrect';
                }
                return new AuthenticationResult($result['code'], $result['identity'], $result['messages']);
            }
        }

        $result['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
        $result['messages'][] = "Username '$this->_username' and realm '$this->_realm' combination not found";
        return new AuthenticationResult($result['code'], $result['identity'], $result['messages']);
    }

    /**
     * Securely compare two strings for equality while avoided C level memcmp()
     * optimisations capable of leaking timing information useful to an attacker
     * attempting to iteratively guess the unknown string (e.g. password) being
     * compared against.
     *
     * @param string $a
     * @param string $b
     * @return bool
     */
    protected function _secureStringCompare($a, $b)
    {
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $result == 0;
    }
}