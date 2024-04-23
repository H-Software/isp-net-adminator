<?php

use \czhujer\Slim\Auth\Acl\SlimAuthAcl;

class Acl extends SlimAuthAcl
{
  
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

        $core_resources = array(
            '/',
            '/login',
            '/auth/signin',
            '/logout',
            '/auth/signout',
            '/auth/notAuthenticated',
            '/auth/notAuthorized'
        );

        $app_resources = array(
            '/auth/password/change',
            '/home',
            '/vlastnici/cat',
            '/vlastnici2',
            '/vlastnici2/fakturacni-skupiny',
            '/vlastnici2/fakturacni-skupiny/action',
            '/objekty/cat',
            '/objekty',
            '/objekty/stb',
            '/objekty/stb/action',
            '/platby/cat',
            '/platby/fn',
            '/platby/fn-kontrola-omezeni',
            '/archiv-zmen',
            '/archiv-zmen/cat',
            '/archiv-zmen/work',
            '/archiv-zmen/ucetni',
            '/work',
            '/others',
            '/others/board',
            '/about',
            '/about/changes-old',
            '/about/changes',
            '/topology',
            '/topology/node-list',
            '/topology/router-list',
            '/admin',
            '/admin/admin',
            '/admin/level-action',
            '/admin/level-list',
            '/admin/tarify'
        );

        foreach ($core_resources as $c) {
            $this->addResource($c);
        }

        foreach ($app_resources as $a) {
            $this->addResource($a);
        }

        // APPLICATION PERMISSIONS
		// Now we allow or deny a role's access to resources.
		// The third argument is 'privilege'. In Slim Auth privilege == HTTP method

        foreach ($core_resources as $c) {
            $this->allow('guest', $c, array('GET', 'POST'));
        }

        foreach ($app_resources as $a) {
            $this->allow('member', $a, array('GET', 'POST'));
        }

		// $this->allow('guest', '/', $this->defaultPrivilege);
		
        // $this->allow('guest', '/auth/signin', array('GET', 'POST'));

		// $this->allow('guest', '/logout', array('GET', 'POST'));
		// $this->allow('guest', '/auth/signout', array('GET', 'POST'));
        
        // $this->allow('guest', '/auth/notAuthenticated' , $this->defaultPrivilege);
		// $this->allow('guest', '/auth/notAuthorized' , $this->defaultPrivilege);
    }
}
