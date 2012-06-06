<?php 
namespace C4\Library\Hydrator;

interface HydratorInterface
{
	public function extract($object);
	
	public function hydrate(array $data, $object);
}