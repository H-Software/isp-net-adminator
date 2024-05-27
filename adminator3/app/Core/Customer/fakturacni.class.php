<?php

namespace App\Customer;

use Psr\Container\ContainerInterface;

class fakturacni
{
    // DI
    public $logger;

    // public \mysqli|\PDO $conn_mysql;

    public $conn_pgsql;

    // protected $sentinel;

    // protected $container;

    // protected $loggedUserEmail;

    // the rest
    public $firma;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        $this->logger = $container->get('logger');
        // $this->conn_mysql = $container->get('connMysql');
        $this->conn_pgsql = $container->get('connPgsql');

        // $this->sentinel = $container->get('sentinel');

        // $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function vypis($id_f, $id_v)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        $dotaz = pg_query($this->conn_pgsql, "SELECT * FROM fakturacni where id='" . intval($id_f). "'");
        $dotaz_radku = pg_num_rows($dotaz);

        if ($dotaz_radku == 0) {
            $output .= "<tr><td> CHYBA! Fakturacni udaje nenalezeny. debug: id=$id_f </td></tr>";
        } else {
            while($data = pg_fetch_array($dotaz)):
                $output .= "<tr>";

                if($this->firma == 1) {
                    $output .= "<td></td>";
                }

                $output .= " <td colspan=\"2\"> <b>Fakturační údaje:</b> <br>".$data["ftitle"]." ".$data["fadresa"]."<br> ";
                $output .= $data["fulice"]." <br> ";

                $output .= $data["fmesto"]." ".$data["fpsc"]."</td>";

                $output .= "<td colspan=\"12\">ičo: ".$data["ico"].", dič: ".$data["dic"];
                $output .= "<br>účet: ".$data["ucet"]." <br> splatnost (dnů): ".$data["splatnost"];
                $output .= "<br> četnost: ".$data["cetnost"]."</td>";

            endwhile;
        }

        return $output;
    } // konec funkce vypis
}
