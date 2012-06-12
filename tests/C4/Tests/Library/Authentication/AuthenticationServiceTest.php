<?php

namespace C4\Tests\Library\Authentication;

use C4\Library\Authentication\AuthenticationService,
    C4\Library\Authentication as Auth;
use C4\Tests\Library\Authentication\TestAsset as TestAsset;

class AuthenticationServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->auth = new AuthenticationService();
    }

    /**
     * Ensures that getStorage() returns Session storage adaptor
     *
     * @return void
     */
    public function testGetStorage()
    {
        $storage = $this->auth->getStorage();
        $this->assertTrue($storage instanceof Auth\Storage\Session);
    }

    public function testAdapter()
    {
        $this->assertNull($this->auth->getAdapter());
        //$successAdapter = new TestAsset\SuccessAdapter();
        //var_dump($successAdapter); die();
        $ret = $this->auth->setAdapter($successAdapter);
        $this->assertSame($ret, $this->auth);
        $this->assertSame($successAdapter, $this->auth->getAdapter());
    }

    /**
     * Ensures expected behavior for successful authentication
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $result = $this->authenticate();
        $this->assertTrue($result instanceof Auth\Result);
        $this->assertTrue($this->auth->hasIdentity());
        $this->assertEquals('someIdentity', $this->auth->getIdentity());
    }

    public function testAuthenticateSetAdapter()
    {
        //$result = $this->authenticate(new TestAsset\SuccessAdapter());
        $this->assertTrue($result instanceof Auth\Result);
        $this->assertTrue($this->auth->hasIdentity());
        $this->assertEquals('someIdentity', $this->auth->getIdentity());
    }

    /**
     * Ensures expected behavior for clearIdentity()
     *
     * @return void
     */
    public function testClearIdentity()
    {
        $this->authenticate();
        $this->auth->clearIdentity();
        $this->assertFalse($this->auth->hasIdentity());
        $this->assertEquals(null, $this->auth->getIdentity());
    }

    protected function authenticate($adapter = null)
    {
        if ($adapter === null) {
            //$adapter = new TestAsset\SuccessAdapter();
        }
        return $this->auth->authenticate($adapter);
    }
}