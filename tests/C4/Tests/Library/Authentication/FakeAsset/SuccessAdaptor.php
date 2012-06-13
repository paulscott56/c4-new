<?php

namespace C4\Tests\Library\Authentication\FakeAsset;

use C4\Library\Authentication\Adapter\AdapterInterface as AuthenticationAdapter,
    C4\Library\Authentication\Result as AuthenticationResult;

class SuccessAdaptor implements AuthenticationAdapter
{
    public function authenticate()
    {
        return new AuthenticationResult(true, 'someIdentity');
    }
}