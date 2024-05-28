<?php

namespace App\Core\Topology;

use App\Core\adminator;
use Psr\Container\ContainerInterface;

class RouterAction extends adminator
{
    public \mysqli|\PDO $conn_mysql;

    public $smarty;

    public $logger;

    protected $settings;

    private $form_odeslat;

    private $form_error;

    public function __construct(ContainerInterface $container)
    {
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');
        $this->logger = $container->get('logger');

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function action(): array
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        $output .= "<div style=\"padding-bottom: 10px; font-size: 18px; \">Přidání/úprava routeru</div>";

        if($this->form_odeslat == "OK") { //zda je odesláno
            // T.B.A. L73 - 164
        }

        if(($this->form_odeslat == "OK") and ($this->form_error != "1")) {
            //proces ukladani ..
            // T.B.A. L168 - 382 
        } else{
            //nechceme ukladat, tj. zobrazit form



        }

        return [$output];
    }
}
