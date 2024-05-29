<?php

class objektypridani
{
    public static function checkmac($mac)
    {
        $mac_check = preg_match('/^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$/', $mac);
        if (!($mac_check)) {
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

    //function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
    public static function validateIpAddress($ip_addr)
    {
        //first of all the format of the ip address is matched
        if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
            //now all the intger values are separated
            $parts = explode(".", $ip_addr);
            //now we need to check each part can range from 0-255
            foreach($parts as $ip_parts) {
                if(intval($ip_parts) > 255 || intval($ip_parts) < 0) {

                    return false; //if number is not within range of 0-255
                }
            }

            return true;
        } else {
            return false; //if format of ip address doesn't matches
        }
    }

    public static function checkip($ip)
    {
        if (!(objektypridani::validateIpAddress($ip))) {
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
