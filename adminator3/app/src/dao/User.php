<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="core__users", 
 		uniqueConstraints={
 			@ORM\UniqueConstraint(
 				name="username_idx", columns={"username"})
 			}
 		)
 * @ORM\HasLifecycleCallbacks
 */
class User {
	/**
	 * @ORM\Id 
	 * @ORM\GeneratedValue 
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	protected $username;
	
	/**
	 * If the user is authenticated through LDAP, it must be null, because
	 * we copy its username to this entity thus it is possible to associate its roles
	 * varchar(255) must be enought for years of evolution of the password hash factor (bcrypt)
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	protected $passwordHash;
	
	
	/**
	 * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist"})
	 * @var UserRole $userRoles
	 */
	protected $userRoles = null;
	
	/*
	 * other properties like:
	 * email, full name, telephone
	 * are intended to be taken from LDAP search by username
	 */
	
	 /**
	 * @ORM\Column(type="integer", length=1, nullable=false, options={"default" : 0})
	 * @var integer
	 */
	protected $level;
	
	public function __construct() {
		$this->userRoles = new ArrayCollection();
	}
	
	
	/**
	 * @ORM\PrePersist
	 */
	public function doChain(){
		//set created datetime
	}
	
	/**
	 * @ORM\PreUpdate
	 */
	public function doTokens(){
		//set updated datetime
	}
	

	public function __toString()
	{
		return strval("[Class=User"
				.", id=".$this->getId()
				.", username=".$this->getUsername()
				."]");
	}
	
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getUsername() {
		return $this->username;
	}
	public function setUsername($username) {
		$this->username = $username;
	}
	public function getUserRoles() {
		return $this->userRoles;
	}
	public function setUserRoles($userRoles) {
		$this->userRoles = $userRoles;
	}
	public function getPasswordHash() {
		return $this->passwordHash;
	}
	public function setPasswordHash($passwordHash) {
		$this->passwordHash = $passwordHash;
	}
	
}
