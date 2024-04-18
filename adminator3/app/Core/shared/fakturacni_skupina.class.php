<?php

class fakturacni_skupina
{
   
 function check_nazev($nazev)
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

} //konec tridy fakturacni_skupina
