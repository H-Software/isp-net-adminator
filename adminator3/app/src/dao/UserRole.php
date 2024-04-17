<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="core__user_roles", 
 		uniqueConstraints={
 			@ORM\UniqueConstraint(
 				name="role_idx", columns={"fk_user_id","role"})
 			}
 		)
 * @ORM\HasLifecycleCallbacks
 */
class UserRole {
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
	protected $role;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="userRoles", cascade={"persist"})
	 * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $user = null;
	
	
	
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
		return strval("[Class=UserRole"
				.", id=".$this->getId()
				.", role=".$this->getRole()
				."]");
	}
	
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getRole() {
		return $this->role;
	}
	public function setRole($role) {
		$this->role = $role;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
	}
	
}
