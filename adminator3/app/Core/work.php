<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class work
{
    // DI
    protected $logger;

    protected $conn_mysql;

    protected $conn_pgsql;

    protected $sentinel;

    // protected $container;

    protected $loggedUserEmail;

    public function __construct(ContainerInterface $container)
    {
        // $this->container = $container;
        $this->logger = $container->get('logger');
        $this->conn_mysql = $container->get('connMysql');
        // $this->conn_pgsql = $container->get('connPgsql');

        $this->sentinel = $container->get('sentinel');

        $this->loggedUserEmail = $this->sentinel->getUser()->email;

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");
    }

    public function work_handler($item_id)
    {
        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . " called");

        $output = "";

        //item_id - cislo ktery odpovida vzdy nejaky akci :)

        //seznam cisel a akcí
        // 1 - osvezeni net-n/sikany na reinhard-3
        // zbytek viz databáze

        $item_id = intval($item_id);

        $rs_count = $this->conn_mysql->query("SELECT id FROM workitems WHERE (number_request = '$item_id' AND in_progress = '0') ");

        $count = $rs_count->num_rows;

        $rs_item_name = $this->conn_mysql->query("SELECT name FROM workitems_names WHERE id = '$item_id' ");

        $rs_item_name->data_seek(0);
        list($item_name) = $rs_item_name->fetch_row();

        $this->logger->info(__CLASS__ . "\\" . __FUNCTION__ . ": parsed item_name: " . var_export($item_name, true));

        if($count > 1) {
            $output .= "<div> WARNING: Požadavek na restart \"".$item_name."\" (No. ".$item_id.") nalezen vícekrát. </div>\n";
        }

        if($count == 1) {
            $output .= "<div> <span style=\"color: #1e90ff; \">INFO: </span>".
            "Požadavak na restart <b>\"".$item_name."\"</b> (No. ".$item_id.") ".
            "<span style=\"color: #1e90ff;\">není potřeba přidávat, již se nachází ve frontě restart. subsystému. </div>\n";
        } else {
            //polozka na seznamu restart. subsystému není, tj. pridame

            $add = $this->conn_mysql->query("INSERT INTO workitems (number_request) VALUES ('".intval($item_id)."') ");

            if($add == 1) {
                $rs_write = "1";
            } else {
                $rs_write = "0";
            }

            $akce_az = "<b>akce:</b> požadavek na restart;<br>[<b>item_id</b>] => ".$item_id;
            $akce_az .= ", [<b>item_name</b>] => ".$item_name;

            $sql_az = "INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
               "('".$this->conn_mysql->real_escape_string($akce_az)."','" .$this->loggedUserEmail . "','".intval($rs_write)."')";

            $add_az = $this->conn_mysql->query($sql_az);

            $output .= "<div style=\"\">Požadavek na restart <b>\"".$item_name."\"</b> (No. ".$item_id.") - ";

            if($add) {
                $output .= "<span style=\"color: green;\"> úspěšně přidán do fronty</span>";
            } else {
                $output .= "<span style=\"color: red;\"> chyba při přidání požadavku do fronty</span>";
            }

            if($add_az) {
                $output .= " - <span style=\"color: green;\"> úspěšně přidán do archivu změn.</span>";
            } else {
                $output .= " - <span style=\"color: red;\"> chyba při přidání požadavku do archivu změn.</span>";
                $output .= "</div><div> sql: ".$sql_az."\n";
            }

            $output .= "</div>";
        }

        return array($output);

    } //end of function work_handler
}
