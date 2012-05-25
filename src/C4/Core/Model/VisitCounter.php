<?php
namespace C4\Core\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


/** @ODM\Document(db="c4", collection="visitcount")*/
class VisitCounter
{
	/** @ODM\Id*/
	private $id;

	/** @ODM\String*/
	private $ip;
	
	/** @ODM\Int*/
	private $counter;

	/** @ODM\Increment */
	protected $totalCounter = 0;
	
	/** @ODM\String*/
	private $hostname;
	
	public function incrementCounter()
	{
		$this->totalCounter++;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setIp($ip)
	{
		$this->ip = $ip;
	}

	public function getIp()
	{
		return $this->ip;
	}

	public function setCounter($count)
	{
		$this->counter = $count;
	}

	public function getCounter()
	{
		return $this->counter;
	}
	
	public function setHostname()
	{
		$this->hostname = gethostbyaddr($this->ip);
	}
}