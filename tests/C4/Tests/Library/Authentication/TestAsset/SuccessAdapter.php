<?php

namespace C4\Tests\Library\Authentication\TestAsset;

use C4\Library\Authentication\Adapter\AdapterInterface as AuthenticationAdapter,
    C4\Library\Authentication\Result as AuthenticationResult;

class SuccessAdapter implements AuthenticationAdapter
{
    public function authenticate()
    {
        return new AuthenticationResult(true, 'someIdentity');
    }
}