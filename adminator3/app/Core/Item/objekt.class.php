<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class objekt extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $dns_find;

    var $ip_find;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->validator;
        $this->conn_mysql = $container->connMysql;   
        $this->logger = $container->logger;

        $i = $container->auth->getIdentity();
        $this->loggedUserEmail = $i['username'];
    }

    public function objektyListGetBodyContent()
    {
        $output = "T.B.A.";

        // TODO: fix checking levels for update/erase
        //promena pro update objektu
        // if ( check_level($level,29) ) { $update_povolen="true"; }
        // if ( check_level($level,33) ) { $mazani_povoleno="true"; }
        // if ( check_level($level,34) ) { $garant_akce="true"; }
        // if ( check_level($level,59) ) { $export_povolen="true"; }

        // TODO: fix export
        // if ( $export_povolen == true )
        // { objekt_a2::export_vypis_odkaz(); }	

        if ( ( strlen($this->dns_find) > 0 ) )
        {
            $co=1;
            $sql=$this->dns_find;
        }  

        if ( ( strlen($this->ip_find) > 0  ) )
        {
            $co=2;
            $sql=$this->ip_find;
        }


        return array($output);
    }
}