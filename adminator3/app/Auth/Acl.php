<?php

use \marcelbonnet\Slim\Auth\Acl\SlimAuthAcl;

class Acl extends SlimAuthAcl
{
    const GUEST                     = "guest";
    const ADMIN                     = "admin";
    const MEMBER                    = "member";
  
	protected $defaultPrivilege = array('GET');    

    public function __construct()
    {
        // // APPLICATION ROLES
        // $this->addRole(self::GUEST);
        
        // $this->addRole(self::MEMBER, self::GUEST);
        
        // /* **************************************
        //  * WARNING: ALLOW ALL:
        //  * **************************************
        //  */
        // $this->addRole(self::ADMIN);
        // $this->allow(self::ADMIN);


        // APPLICATION ROLES
		$this->addRole('guest');
		// member role "extends" guest, meaning the member role will get all of
		// the guest role permissions by default
		$this->addRole('member', 'guest');
		$this->addRole('admin');
		// APPLICATION RESOURCES
		// Application resources == Slim route patterns
		$this->addResource('/');
		$this->addResource('/login');
		$this->addResource('/logout');

        $this->addResource('/home');
		$this->addResource('/auth/notAuthenticated');
		$this->addResource('/auth/notAuthorized');

        // APPLICATION PERMISSIONS
		// Now we allow or deny a role's access to resources.
		// The third argument is 'privilege'. In Slim Auth privilege == HTTP method
		$this->allow('guest', '/', $this->defaultPrivilege);
		$this->allow('guest', '/login', array('GET', 'POST'));
		$this->allow('guest', '/logout', $this->defaultPrivilege);
        
        $this->allow('guest', '/auth/notAuthenticated' , $this->defaultPrivilege);
		$this->allow('guest', '/auth/notAuthorized' , $this->defaultPrivilege);
        
    }
    
    
}
