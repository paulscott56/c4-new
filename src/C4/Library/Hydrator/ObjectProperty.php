<?php 
namespace C4\Library\Hydrator;

use \Exception;

class ObjectProperty implements HydratorInterface
{
	/**
     * Extract values from an object
     *
     * Extracts the accessible non-static properties of the given $object.
     * 
     * @param  object $object 
     * @return array
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function extract($object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }

        return get_object_vars($object);
    }

    /**
     * Hydrate an object by populating public properties
     *
     * Hydrates an object by setting public properties of the object.
     * 
     * @param  array $data 
     * @param  object $object 
     * @return void
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
        foreach ($data as $property => $value) {
            $object->$property = $value;
        }
    }
}