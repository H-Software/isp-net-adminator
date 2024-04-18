<?php

namespace App\Customer;

use App\Core\adminator;
use App\Models\FakturacniSkupina;

class fakturacniSkupiny extends adminator
{

    var $conn_mysql;


    function __construct($conn_mysql)
    {
        $this->conn_mysql = $conn_mysql;
    }

    function getItems()
    {
        $items = FakturacniSkupina::all();
        
        // $items = FakturacniSkupina::where('active', 1)
        //     ->orderBy('name')
        //     ->take(10)
        //     ->get();
    }

    function checkNazev($nazev)
    {
        $nazev_check = preg_match('/^([[:alnum:]]|_|-)+$/', $nazev);
        
        if($nazev_check === false)
        {
            global $fail;
            $fail = "true";
            
            global $error;     
            $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Název ( ".$nazev." ) obsahuje nepovolené znaky! (Povolené: čísla, písmena a-Z,_ ,- )</H4></div>";
        }

    } //konec funkce check_nazev
}
