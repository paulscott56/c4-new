<?php 

namespace C4\Library\Authentication\Adapter;

use C4\Library\Authentication\Result as AuthenticationResult;
use C4\Core\Framework as Framework;
use C4\Core\Model\User as User;

class MongoAuth implements AdapterInterface
{
	/**
     * Database collection
     *
     * @var MongoCollection
     */    
	protected $_collection = 'users';
 
    /**
     * $_identityKeyPath - the column to use as the identity
     *     
     * @var string
     */
    protected $_identityKeyPath = null;
 
    /**     
     * $_credentialKeyPaths - columns to be used as the credentials
     *
     * @var string
     */
    protected $_credentialKeyPath = null; 
    
    /**
     * $_identity - Identity value
     *
     * @var string     
     */
    protected $_identity = null;
 
    /**
     * $_credential - Credential values     
     *      
     * @var string
     */
    protected $_credential = null;
     
     /**
     * $_credentialTreatment - Treatment applied to the credential, such as MD5() or PASSWORD()
     *
     * @var string
     */    
    protected $_credentialTreatment = null;
 
    /**
     * $_authenticateResultInfo
     * 
     * @var array
     */
    protected $_authenticateResultInfo = null;
 
    /**     
     * $_resultDoc - Results of database authentication query
     *
     * @var array
     */
    protected $_resultDoc = null; 
    
    /**
     * $_ambiguityIdentity - Flag to indicate same Identity can be used with
     * different credentials. 
     * Default is FALSE and need to be set to true to
     * allow ambiguity usage.     
     * 
     * @var boolean
     */
    protected $_ambiguityIdentity = false;
    
    /**
     * __construct() - Sets configuration options
     *
     * @param  MongoCollection $collection If not set 'users' is assumed
     * @param  string                   $identityKeyPath     
     * @param  string                   $credentialKeyPath
     * @param  string                   $credentialTreatment
     * @return void
     */
    public function __construct(MongoCollection $collection = null, $identityKeyPath = null,                                $credentialKeyPath = null, $credentialTreatment = null)
    {
        $this->_setCollection($collection);
 
        if (null !== $identityKeyPath) {            
        	$this->setIdentityKeyPath($identityKeyPath);
        }
 
        if (null !== $credentialKeyPath) {
            $this->setCredentialKeyPath($credentialKeyPath);        }
 
        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }    
    }
    
    /**
     * _setCollection() - set the database collection to be used for quering
     *
     * @param MongoCollection
     * @throws C4\Library\Authentication\Exception
     * @return this
     */
    protected function _setCollection(MongoCollection $collection = null)
    {
        $this->_collection = $collection;

        /**
         * If no adapter is specified, fetch default database adapter.
         */
        if(null === $this->_collection) {
            $this->_collection = 'users';
            if (null === $this->_collection) {
                throw new Exception('No collection specified!');
            }
        }

        return $this;
    }
    
    /**
     * setIdentityKeyPath() - set the column name to be used as the identity column
     *
     * @param  string $identityKeyPath
     * @return Provides a fluent interface
     */
    public function setIdentityKeyPath($identityKeyPath)
    {
        $this->_identityKeyPath = $identityKeyPath;
        return $this;
    }
    
    /**
     * setCredentialKeyPath() - set the column name to be used as the credential column
     *
     * @param  string $credentialKeyPath
     * @return Provides a fluent interface
     */
    public function setCredentialKeyPath($credentialKeyPath)
    {
        $this->_credentialKeyPath = $credentialKeyPath;
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a parameterized string that is
     * used to transform or treat the input credential data.
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. By specifying a
     * parameterized treatment string with this method, a developer may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     *  'PASSWORD(?)'
     *  'MD5(?)'
     *  'SHA1(?)'
     *
     * @param  string $treatment
     * @return Provides a fluent interface
     */
    public function setCredentialTreatment($treatment)
    {
        $this->_credentialTreatment = $treatment;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
     *
     * @param  string $credential
     * @return Provides a fluent interface
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * setAmbiguityIdentity() - sets a flag for usage of identical identities
     * with unique credentials. It accepts integers (0, 1) or boolean (true,
     * false) parameters. Default is false.
     *
     * @param  int|bool $flag
     * @return Provides a fluent interface
     */
    public function setAmbiguityIdentity($flag)
    {
        if (is_integer($flag)) {
            $this->_ambiguityIdentity = (1 === $flag ? true : false);
        } elseif (is_bool($flag)) {
            $this->_ambiguityIdentity = $flag;
        }
        return $this;
    }
    /**
     * getAmbiguityIdentity() - returns TRUE for usage of multiple identical
     * identies with different credentials, FALSE if not used.
     *
     * @return bool
     */
    public function getAmbiguityIdentity()
    {
        return $this->_ambiguityIdentity;
    }

    /**
     * getResultDocObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultDocObject($returnColumns = null, $omitColumns = null)
    {
        if (!$this->_resultDoc) {
            return false;
        }
        
        return $this->_resultDoc; 
    }

    /**
     * authenticate() - defined by C4\Library\Authentication\AuthenticationInterface.  
     * 
     * This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * collection and attempt to find a record matching the provided identity.
     *
     * @throws C4\Library\Authentication\Exception if answering the authentication query is impossible
     * @return C4\Library\Authentication\Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        
        $cursor = $this->_collection->find(array(
            $this->_identityKeyPath => $this->_identity
        ));

        $count = $cursor->count(); 
        if ($count == 0) {
            $this->_authenticateResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
        } elseif ($count == 1) {
            $resultIdentity = $cursor->getNext();
            $this->_resultDoc = $resultIdentity;
            if ($resultIdentity[$this->_credentialKeyPath] == $this->_credential) {
                $this->_authenticateResultInfo['code'] = AuthenticationResult::SUCCESS;
                $this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
            } else {
                $this->_authenticateResultInfo['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
                $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            }   
        } elseif ($count > 1) {
            $this->_authenticateResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
        }
        
        $authResult = $this->_authenticateCreateAuthResult();        

        return $authResult;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws C4\Library\Authentication\Exception - in the event that setup was not done properly
     * @return true
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if ($this->_identityKeyPath == '') {
            $exception = 'An identity column must be supplied for the MongoAuth authentication adapter.';
        } elseif ($this->_credentialKeyPath == '') {
            $exception = 'A credential column must be supplied for the MongoAuth authentication adapter.';
        } elseif ($this->_identity == '') {
            $exception = 'A value for the identity was not provided prior to authentication with MongoAuth.';
        } elseif ($this->_credential === null) {
            $exception = 'A credential value was not provided prior to authentication with MongoAuth.';
        }

        if (null !== $exception) {
            throw new C4\Library\Authentication\Exception($exception);
        }

        $this->_authenticateResultInfo = array(
            'code'     => AuthenticationResult::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
            );

        return true;
    }

    /**
     * _authenticateCreateAuthResult() - Creates a AuthenticationResult object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return AuthenticationResult
     */
    protected function _authenticateCreateAuthResult()
    {
        return new AuthenticationResult(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['identity'],
            $this->_authenticateResultInfo['messages']
            );
    }
    
}