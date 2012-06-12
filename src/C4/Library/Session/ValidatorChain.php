<?php

namespace C4\Library\Session;

use C4\Library\EventManager\EventManager,
    C4\Library\Session\Storage\StorageInterface as Storage,
    C4\Library\Session\Validator\ValidatorInterface as Validator;

class ValidatorChain extends EventManager
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Construct the validation chain
     *
     * Retrieves validators from session storage and attaches them.
     * 
     * @param  Storage $storage 
     * @return void
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;

        $validators = $storage->getMetadata('_VALID');
        if ($validators) {
            foreach ($validators as $validator => $data) {
                $this->attach('session.validate', new $validator($data), 'isValid');
            }
        }
    }

    /**
     * Attach a listener to the session validator chain
     * 
     * @param  string $event
     * @param  callback $callback
     * @param  int $priority 
     * @return \C4\Library\Stdlib\CallbackHandler
     */
    public function attach($event, $callback = null, $priority = 1)
    {
        $context = null;
        if ($callback instanceof Validator) {
            $context = $callback;
        } elseif (is_array($callback)) {
            $test = array_shift($callback);
            if ($test instanceof Validator) {
                $context = $test;
            }
            array_unshift($callback, $test);
        }
        if ($context instanceof Validator) {
            $data = $context->getData();
            $name = $context->getName();
            $this->getStorage()->setMetadata('_VALID', array($name => $data));
        }

        $listener = parent::attach($event, $callback, $priority);
        return $listener;
    }

    /**
     * Retrieve session storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
