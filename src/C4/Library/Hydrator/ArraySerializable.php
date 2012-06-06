<?php 
namespace C4\Library\Hydrator;

use \Exception;

class ArraySerializable implements HydratorInterface
{
    /**
     * Extract values from the provided object
     * 
     * Extracts values via the object's getArrayCopy() method.
     * 
     * @param  object $object 
     * @return array
     * @throws Exception\BadMethodCallException for an $object not implementing getArrayCopy()
     */
    public function extract($object)
    {
        if (!is_callable(array($object, 'getArrayCopy'))) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement getArrayCopy()',
                __METHOD__
            ));
        }
        return $object->getArrayCopy();
    }

    /**
     * Hydrate an object
     *
     * Hydrates an object by passing $data to either its exchangeArray() or 
     * populate() method.
     * 
     * @param  array $data 
     * @param  object $object 
     * @return void
     * @throws Exception\BadMethodCallException for an $object not implementing exchangeArray() or populate()
     */
    public function hydrate(array $data, $object)
    {
        if (!is_callable(array($object, 'exchangeArray'))
            && !is_callable(array($object, 'populate'))
        ) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement exchangeArray() or populate()',
                __METHOD__
            ));
        }

        if (is_callable(array($object, 'exchangeArray'))) {
            $object->exchangeArray($data);
            return;
        }

        $object->populate($data);
    }
}