<?php

class objektypridani
{
    public static function checkmac($mac)
    {
        if (filter_var($mac, FILTER_VALIDATE_MAC) == false) {
            global $fail;
            $fail = "true";
            global $error;
            $error .= "<div class=\"objekty-add-fail-mac\"><H4>MAC adresa ( ".$mac." ) není ve správném formátu !!! ( Správný formát je: 00:00:64:65:73:74 ) </H4></div>";
        }

        //konec funkce check-mac
    }

    public static function checkSikanaCas($sikanacas)
    {
        global $fail, $error;

        $sikanacas = intval($sikanacas);

        if(($sikanacas > 9) or ($sikanacas < 1)) {

            $fail = "true";

            $error .= "<div class=\"objekty-add-fail-mac\">".
            "<H4>Do pole \"Šikana - počet dní\" je třeba vyplnit číslo 1 až 9.</H4></div>";
        }
    } //end of function checkSikanaCas

    public static function checkSikanaText($sikanatext)
    {
        global $fail, $error;

        if((strlen($sikanatext) > 150)) {

            $fail = "true";

            $error .= "<div class=\"objekty-add-fail-mac\">".
            "<H4>Do pole \"Šikana - text\" je možno zadat max. 150 znaků. (aktuálně: ".strlen($sikanatext).")</H4></div>";

        }

    } //end of function checkSikanaText

    public static function checkip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) == false) {
            global $fail;
            $fail = "true";
            global $error;
            $error .= "<div class=\"objekty-add-fail-ip\"><H4>IP adresa ( ".$ip." ) není ve správném formátu !!!</H4></div>";
        }
    } //konec funkce check-ip

    public static function checkcislo($cislo)
    {
        $rra_check = preg_match('/^([[:digit:]]+)$/', $cislo);

        if (!($rra_check)) {
            global $fail;
            $fail = "true";
            global $error;
            $error .= "<H4>Zadaný číselný údaj(e) ( ".$cislo." ) není ve  správném formátu !!! </H4>";
        }
    } //konec funkce check cislo

    public static function checkdns($dns)
    {
        $dns_check = preg_match('/^([[:alnum:]]|\.|-)+$/', $dns);
        if (!($dns_check)) {
            global $fail;
            $fail = "true";
            global $error;
            $error .= "<div class=\"objekty-add-fail-dns\"><H4>DNS záznam ( ".$dns." ) není ve správnem formátu !!! </H4></div>";
        }
    } // konec funkce check rra

    public static function check_l2tp_cr($cr)
    {
        $cr_check = preg_match('/^([[:alnum:]])+$/', $cr);

        if(!($cr_check)) {
            global $fail;
            $fail = "true";

            global $error;
            $error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) není ve správnem formátu !!! </H4></div>";
        }

        if((strlen($cr) <> 4)) {
            global $fail;
            $fail = "true";

            global $error;
            $error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) musí mít 4 znaky !!! </H4></div>";

        }
    } //konec funkce check_l2tp_cr

} //konec objketu objekty-pridani
