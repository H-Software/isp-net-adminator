<?php

use \marcelbonnet\Slim\Auth\Acl\SlimAuthAcl;

class Acl extends SlimAuthAcl
{
    const GUEST                     = "guest";
    const ADMIN                     = "admin";
    const MEMBER                    = "member";
    

    public function __construct()
    {
        // APPLICATION ROLES
        $this->addRole(self::GUEST);
        
        $this->addRole(self::MEMBER, self::GUEST);
        
        /* **************************************
         * WARNING: ALLOW ALL:
         * **************************************
         */
        $this->addRole(self::ADMIN);
        $this->allow(self::ADMIN);
    }
    
    
}
