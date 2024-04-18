<?php

namespace App\Customer;

use App\Core\adminator;
use App\Models\FakturacniSkupina;

class fakturacniSkupiny extends adminator
{

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
