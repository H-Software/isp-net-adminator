<?php

namespace App\Print;

use Exception;
use App\Core\adminator;
use Psr\Container\ContainerInterface;

class printClass extends adminator
{
    private $container;

    private $validator;

    public $conn_pgsql;
    public $conn_mysql;

    public $pdoMysql;

    public $logger;

    public $loggedUserEmail;

    public $adminator; // handler for instance of adminator class

    public $csrf_html;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        // $this->validator = $container->get('validator');
        // $this->conn_mysql = $container->get('connMysql');
        // $this->pdoMysql = $container->get('pdoMysql');

        $this->logger = $container->get('logger');
        $this->smarty = $container->get('smarty');

        // $this->loggedUserEmail = \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email;

        // $this->adminator = new adminator($this->conn_mysql, $this->smarty, $this->logger);

    }

    private function nacti_soubory($find_string)
    {
        $handle = opendir('print/temp/');
        $i = 0;

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && !is_dir($file) && preg_match('/'.$find_string."/", $file)) {
                $soubor[$i] = "$file";
                $i++;
            }
        }
        closedir($handle);

        if(is_array($soubor)) {
            sort($soubor);
        }

        return $soubor;

    }

    public function printListAll()
    {
        $this->smarty->assign("action", "/print/redirect");
        $this->smarty->assign("csrf_html", $this->csrf_html);

        $soubor3 = $this->nacti_soubory("smlouva-fiber");
        $this->smarty->assign("soubory_smlouvy_new", $soubor3);

        $soubor4 = $this->nacti_soubory("reg-form-pdf");
        $this->smarty->assign("soubory_regform_new", $soubor4);

        $soubor5 = $this->nacti_soubory("smlouva-v3");
        $this->smarty->assign("soubory_smlouva_v3", $soubor5);

        $soubor6 = $this->nacti_soubory("reg-form-v3");
        $this->smarty->assign("soubory_reg_form_2012_05", $soubor6);


        $this->smarty->display('print/list-all.tpl');
    }
}
