<?php

require("include/main.function.shared.php");
require("include/config.php");
require("include/check_login.php");
require("include/check_level.php");

if( !( check_level($level,13) ) ) {
 // neni level
 header("Location: nolevelpage.php"); 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}

$id_cloveka = intval($_GET["id_klienta"]);

$dotaz = pg_query($db_ok2, "SELECT ".
			"fakturacni, jmeno, prijmeni, ulice, mesto, psc, telefon, mail, k_platbe, vs, ucetni_index ".
			" FROM vlastnici WHERE id_cloveka = '$id_cloveka'");

while($data = pg_fetch_array($dotaz)){

    if( $data["fakturacni"] > 0 ){
	    $data_company = iconv("UTF-8", "Windows-1250", "FAKTURACNI NEUMIME");    
    }
    else{
        //domácí
        $data_name = iconv("UTF-8", "Windows-1250", $data["jmeno"]." ".$data["prijmeni"] );    	
        $data_city = iconv("UTF-8", "Windows-1250", $data["mesto"] );
        $data_street = iconv("UTF-8", "Windows-1250", $data["ulice"] );
        $data_zip = iconv("UTF-8", "Windows-1250", $data["psc"] );
    }
    
    if($data["telefon"] != "NULL") {
        $data_phone = iconv("UTF-8", "Windows-1250", $data["telefon"]);
    }
    
    $data_email = iconv("UTF-8", "Windows-1250", $data["mail"]);    
    
    //klice a skupiny, zjednodusene
    if($data["k_platbe"] == 248) {
        $data_addGroup = iconv("UTF-8", "Windows-1250", "SMALL CITY");
        $data_addKey = "FS74";
    }
    elseif($data["k_platbe"] == 416.5) {
        $data_addGroup = iconv("UTF-8", "Windows-1250", "METROPOLITNÍ");
        $data_addKey = "FS75";
    }
    else{
	    //ostatni neresime
    }
    
    $data_agreement = $data["vs"];
    
//    $data_number = "27DM".sprintf("%05d", $data["ucetni_index"]);
//    $data_number = intval(sprintf("%05d", $data["ucetni_index"]));
    
//    $data_number = "27DM";
        
}

// pomoci hlavicky urcime mime typ text/xml
header('content-type: text/xml');

// autoloaded
// require 'include/xml/xml_generator.class.php';

// vytvorime instanci tridy c_xml_generator
$xml = new c_xml_generator;
$xml->xml_encoding="Windows-1250";

// vytvorime si nejvyssi element
$root_arr = array();

$root_arr["id"] = "id_klienta_".$id_cloveka;
$root_arr["ico"] = "26109824";
$root_arr["application"] = iconv("UTF-8", "Windows-1250", "ISP-Net-Adminator2");
$root_arr["version"] = "2.0";
$root_arr["note"] = iconv("UTF-8", "Windows-1250", "Import adresářového záznamu");

$root_arr["xmlns:dat"] = "http://www.stormware.cz/schema/version_2/data.xsd";
$root_arr["xmlns:adb"] = "http://www.stormware.cz/schema/version_2/addressbook.xsd";
$root_arr["xmlns:typ"] = "http://www.stormware.cz/schema/version_2/type.xsd";

$top = $xml->add_node(0, 'dat:dataPack', $root_arr );

$dat_dataPackItem = $xml->add_node($top, 'dat:dataPackItem', array("id" => "id_klienta_".$id_cloveka, "version" => "2.0") );

$adb_addressbook = $xml->add_node($dat_dataPackItem, 'adb:addressbook', array("version" => "2.0") );

$adb_addressbookHeader = $xml->add_node($adb_addressbook, 'adb:addressbookHeader');

$adb_identity = $xml->add_node($adb_addressbookHeader, 'adb:identity');

$typ_address = $xml->add_node($adb_identity, 'typ:address');

//nazev spolecnosti
$typ_company = $xml->add_node($typ_address, 'typ:company');
if( !empty($data_company) ){ 
    $typ_company_data = $xml->add_cdata($typ_company, $data_company);
}

//jmeno (a prijmeni)
$typ_name = $xml->add_node($typ_address, 'typ:name');
if( !empty($data_name) ){ 
    $typ_company_data = $xml->add_cdata($typ_name, $data_name);
}

//Mesto
$typ_city = $xml->add_node($typ_address, 'typ:city');
if( !empty($data_city) ){ 
    $typ_city_data = $xml->add_cdata($typ_city, $data_city);
}

//ulice
$typ_street = $xml->add_node($typ_address, 'typ:street');
if( !empty($data_street) ){ 
    $typ_street_data = $xml->add_cdata($typ_street, $data_street);
}

//zip, alias PSČ
$typ_zip = $xml->add_node($typ_address, 'typ:zip');
if( !empty($data_zip) ){ 
    $typ_zip_data = $xml->add_cdata($typ_zip, $data_zip);
}

//mobil
$adb_mobil = $xml->add_node($adb_addressbookHeader, 'adb:mobil');
if( !empty($data_phone) ){ 
    $adb_mobil_data = $xml->add_cdata($adb_mobil, $data_phone);
}

//email
$adb_email = $xml->add_node($adb_addressbookHeader, 'adb:email');
if( !empty($data_email) ){ 
    $adb_email_data = $xml->add_cdata($adb_email, $data_email);
}

//skupina
$adb_group = $xml->add_node($adb_addressbookHeader, 'adb:adGroup');
if( !empty($data_addGroup) ){ 
    $adb_group_data = $xml->add_cdata($adb_group, $data_addGroup);
}

//klic
$adb_key = $xml->add_node($adb_addressbookHeader, 'adb:adKey');
if( !empty($data_addKey) ){ 
    $adb_key_data = $xml->add_cdata($adb_key, $data_addKey);
}

//agreement - smlouva
$adb_agg = $xml->add_node($adb_addressbookHeader, 'adb:agreement');
if( !empty($data_agreement) ){
    $adb_agg_data = $xml->add_cdata($adb_agg, $data_agreement);
}

$adb_number = $xml->add_node($adb_addressbookHeader, 'adb:number');
if( !empty($data_number) ){
    $adb_number_data = $xml->add_cdata($adb_number, $data_number);
}

echo $xml->create_xml();

?>
