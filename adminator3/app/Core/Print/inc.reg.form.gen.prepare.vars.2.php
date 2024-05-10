<?php

function validateIpAddress($ip_addr)
{
    //first of all the format of the ip address is matched
    if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        //now all the intger values are separated
        $parts = explode(".", $ip_addr);
        //now we need to check each part can range from 0-255
        foreach($parts as $ip_parts) {
            if(intval($ip_parts) > 255 || intval($ip_parts) < 0) {

                return false;
            } //if number is not within range of 0-255
        }

        return true;
    } else {
        return false;
    } //if format of ip address doesn't matches
}

$s_ValidMasks = array(
    '255.255.255.255' => true,
    '255.255.255.254' => true,
    '255.255.255.252' => true,
    '255.255.255.248' => true,
    '255.255.255.240' => true,
    '255.255.255.224' => true,
    '255.255.255.192' => true,
    '255.255.255.128' => true,
    '255.255.255.0' => true,
    '255.255.254.0' => true,
    '255.255.252.0' => true,
    '255.255.248.0' => true,
    '255.255.240.0' => true,
    '255.255.224.0' => true,
    '255.255.192.0' => true,
    '255.255.128.0' => true,
    '255.255.0.0' => true,
    '255.254.0.0' => true,
    '255.252.0.0' => true,
    '255.248.0.0' => true,
    '255.240.0.0' => true,
    '255.224.0.0' => true,
    '255.192.0.0' => true,
    '255.128.0.0' => true,
    '255.0.0.0' => true,
    '254.0.0.0' => true,
    '252.0.0.0' => true,
    '248.0.0.0' => true,
    '240.0.0.0' => true,
    '224.0.0.0' => true,
    '192.0.0.0' => true,
    '128.0.0.0' => true,
    '0.0.0.0' => true,
);

function isValidIPv4Mask($mask)
{
    global $s_ValidMasks;
    return isset($s_ValidMasks[$mask]);
}

// konverze promennych
$ec = iconv("UTF-8", "CP1250", $ec);

if((strlen($vas_technik) >= 1)) {
    $vas_technik = iconv("UTF-8", "CP1250", $vas_technik);
} else {
    $vas_technik = "- - - - - - -";
}


if((strlen($vas_technik_tel) >= 1)) {

    //check formátu
    if(!(ereg('^([[:digit:]])+$', $vas_technik_tel)) or (strlen($vas_technik_tel) <> 9)) {

        $vas_technik_tel = "- - - - - - -";

    } else {
        $vas_technik_tel = iconv("UTF-8", "CP1250", $vas_technik_tel);
    }

} else {
    $vas_technik_tel = "- - - - - - -";
}

$prip_tech_1 = "-";
$prip_tech_2 = "-";
$prip_tech_3 = "-";

if($prip_tech == 1) {
    $prip_tech_1 = "X";
} elseif($prip_tech == 2) {
    $prip_tech_2 = "X";
} elseif($prip_tech == 3) {
    $prip_tech_3 = "X";
}

$cislo_portu = intval($cislo_portu);

if($cislo_portu == 0) {
    $cislo_portu = "-";
}

if((strlen($poznamka) > 0)) {
    $poznamka = iconv("UTF-8", "CP1250", $poznamka);
} else {
    $poznamka = "- - - - - - - - - -";
}

//INTERNET ZARIZENI
$int_pocet_zarizeni = intval($_POST["int_pocet_zarizeni"]);

for($i = 1; $i <= 3; $i++) {
    $int_zarizeni = "int_zarizeni_".$i;
    $int_zarizeni_ip = "int_zarizeni_".$i."_ip";
    $int_zarizeni_pozn = "int_zarizeni_".$i."_pozn";
    $int_zarizeni_vlastnik = "int_zarizeni_".$i."_vlastnik";

    if($i <= $int_pocet_zarizeni) {

        $$int_zarizeni = iconv("UTF-8", "CP1250", $$int_zarizeni);
        $$int_zarizeni_ip = iconv("UTF-8", "CP1250", $$int_zarizeni_ip);
        $$int_zarizeni_pozn = iconv("UTF-8", "CP1250", $$int_zarizeni_pozn);
        $$int_zarizeni_vlastnik = iconv("UTF-8", "CP1250", $$int_zarizeni_vlastnik);

    } else {
        //neni vubec vybrany, cili pomlčky

        $$int_zarizeni 	       = "- - - - - - - - - - -";
        $$int_zarizeni_ip       = "- - - - - - - - - - -";
        $$int_zarizeni_pozn     = "- - - - - - - - - - -";
        $$int_zarizeni_vlastnik = "- - - - - - - - - - -";

    }
}

if($int_zarizeni_1_vlastnik == 1) {
    $int_zarizeni_1_vlastnik_x = "X";
} else {
    $int_zarizeni_1_vlastnik_x = " -";
}

if($int_zarizeni_2_vlastnik == 1) {
    $int_zarizeni_2_vlastnik_x = "X";
} else {
    $int_zarizeni_2_vlastnik_x = " -";
}

if($int_zarizeni_3_vlastnik == 1) {
    $int_zarizeni_3_vlastnik_x = "X";
} else {
    $int_zarizeni_3_vlastnik_x = " -";
}

if($ip_dhcp == "1") {
    $ip_dhcp_x = "X";
} else {
    $ip_dhcp_x = " -";
}

if(!validateIpAddress($ip_adresa)) {
    $ip_adresa = "- - - - -";
} else {
    $ip_adresa = iconv("UTF-8", "CP1250", $ip_adresa);
}

if(isValidIPv4Mask($ip_maska)) {
    $ip_maska = iconv("UTF-8", "CP1250", $ip_maska);
} else {
    $ip_maska = "- - - - ";
}

if(!validateIpAddress($ip_brana)) {
    $ip_brana = "- - - - ";
} else {
    $ip_brana = iconv("UTF-8", "CP1250", $ip_brana);
}

if(!validateIpAddress($ip_dns1)) {
    $ip_dns1 = "- - - - ";
} else {
    $ip_dns1 = iconv("UTF-8", "CP1250", $ip_dns1);
}

if(!validateIpAddress($ip_dns2)) {
    $ip_dns2 = "- - - - ";
} else {
    $ip_dns2 = iconv("UTF-8", "CP1250", $ip_dns2);
}

//IPTV
$iptv_pocet_zarizeni = intval($iptv_pocet_zarizeni);

for($i = 1; $i <= 3; $i++) {

    $iptv_zarizeni = "iptv_zarizeni_".$i;
    $iptv_zarizeni_ip = "iptv_zarizeni_".$i."_ip";
    $iptv_zarizeni_pozn = "iptv_zarizeni_".$i."_pozn";
    $iptv_zarizeni_vlastnik = "iptv_zarizeni_".$i."_vlastnik";
    $iptv_zarizeni_vlastnik_x = "iptv_zarizeni_".$i."_vlastnik_x";

    if($i <= $iptv_pocet_zarizeni) {

        $$iptv_zarizeni = iconv("UTF-8", "CP1250", $$iptv_zarizeni);
        $$iptv_zarizeni_ip = iconv("UTF-8", "CP1250", $$iptv_zarizeni_ip);
        $$iptv_zarizeni_pozn = iconv("UTF-8", "CP1250", $$iptv_zarizeni_pozn);
        $$iptv_zarizeni_vlastnik_x = ($$iptv_zarizeni_vlastnik == 1 ? "X" : " -");

    } else {
        //neni vubec vybrany, cili pomlčky

        $$iptv_zarizeni 	          = "- - - - - - - - - - -";
        $$iptv_zarizeni_ip         = "- - - - - - - - - - -";
        $$iptv_zarizeni_pozn       = "- - - - - - - - - - -";
        $$iptv_zarizeni_vlastnik_x = " -";

    }
}

//VOIP
$voip_pocet_zarizeni = intval($voip_pocet_zarizeni);

for($i = 1; $i <= 2; $i++) {

    $voip_zarizeni = "voip_zarizeni_".$i;
    $voip_zarizeni_ip = "voip_zarizeni_".$i."_ip";
    $voip_zarizeni_pozn = "voip_zarizeni_".$i."_pozn";
    $voip_zarizeni_vlastnik = "voip_zarizeni_".$i."_vlastnik";
    $voip_zarizeni_vlastnik_x = "voip_zarizeni_".$i."_vlastnik_x";

    if($i <= $voip_pocet_zarizeni) {

        $$voip_zarizeni = iconv("UTF-8", "CP1250", $$voip_zarizeni);
        $$voip_zarizeni_ip = iconv("UTF-8", "CP1250", $$voip_zarizeni_ip);
        $$voip_zarizeni_pozn = iconv("UTF-8", "CP1250", $$voip_zarizeni_pozn);
        $$voip_zarizeni_vlastnik = iconv("UTF-8", "CP1250", $$voip_zarizeni_vlastnik);
        $$voip_zarizeni_vlastnik_x = ($$voip_zarizeni_vlastnik == 1 ? "X" : " -");

    } else {
        //neni vubec vybrany, cili pomlčky

        $$voip_zarizeni 	          = "- - - - - - - - - - -";
        $$voip_zarizeni_ip         = "- - - - - - - - - - -";
        $$voip_zarizeni_pozn       = "- - - - - - - - - - -";
        $$voip_zarizeni_vlastnik_x = " -";

    }
}

//INS ZARIZENI
$mat_pocet = intval($mat_pocet);

for($i = 1; $i <= 3; $i++) {
    $mat = "mat_".$i;

    if($i <= $mat_pocet) {
        $$mat = iconv("UTF-8", "CP1250", $$mat);
    } else {
        $$mat = "- - - - - - - - - - - - - - - - - - - - ";
    }
}

if((strlen($poznamka2) > 0)) {
    $poznamka2 = iconv("UTF-8", "CP1250", $poznamka2);

    $pozn2_arr = str_split($poznamka2, 200);

    $poznamka2 = $pozn2_arr[0];

} else {
    $poznamka2 = "- - - - - - - - - - ";
}

// konec pripravy promennych
