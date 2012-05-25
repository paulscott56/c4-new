<?php
namespace C4\Core\Model;
// User Class to define a user

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


/** @ODM\Document(db="c4", collection="users")*/
class User
{
	/** @ODM\Id*/
	private $id;
	
	/** @ODM\String*/
	private $username;
	
	/** @ODM\String*/
	private $password;
	
	public function setId($id) 
	{
		$this->id = $id;
	}
	
	public function getId() 
	{
		return $this->id;
	}
	
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	
	public function setPassword($password)
	{
		$this->password = sha1($password);
	}
	
	public function getPassword()
	{
		return $this->password;
	}
}