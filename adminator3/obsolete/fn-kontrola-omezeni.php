<?php

require 'smarty/Smarty.class.php';

require "include/config.php";
require "include/main.function.php";

require "include/main.classes.php";

$smarty = new Smarty;

$smarty->compile_check = true;
//$smarty->debugging = true;

start_ses();
$cl = check_login();

if( $cl[0] == "false" )
{ //chybny login ...
   
 $smarty->assign("page_title","Adminator3 :: chybný login");
 $smarty->assign("body",$cl[1]);

 $last_page = last_page();
 $smarty->assign("last_page",$last_page);
   
 $smarty->display('index-nologin.tpl');

 exit;
}


if( !( check_level($level,149) ) )
{ // neni level
 
 $smarty->assign("page_title","Adminator3 :: chybny level");
 $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");

 $smarty->display('index-nolevel.tpl');

 exit;
}

$smarty->assign("page_title","Adminator3 :: N.F. :: Kontrola omezeni vs. platby");

$smarty->assign("nick_a_level",$nick." (".$level.")");
$smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

//kategorie
$uri=$_SERVER["REQUEST_URI"];
$uri_replace = str_replace ("adminator3", "", $uri);

list($kategorie, $kat_2radka, $mapa) = zobraz_kategorie($uri,$uri_replace);

$smarty->assign("kategorie",$kategorie);
$smarty->assign("kat_2radka",$kat_2radka);

$smarty->assign("show_se_cat_values", array("0","1"));
$smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

$show_se_cat = $_POST["show_se_cat"];

if( $show_se_cat == 0 )
{ $smarty->assign("show_se_cat_selected", "0"); }
else
{ $smarty->assign("show_se_cat_selected", "1"); }

$smarty->assign("show_se_cat",$show_se_cat);

$prihl_uziv = vypis_prihlasene_uziv($nick);

if( $prihl_uziv[100] == true )
{
  $smarty->assign("pocet_prihl_uziv",0);
}
else
{
  $smarty->assign("pocet_prihl_uziv",$prihl_uziv[0]);

  $smarty->assign("prvni_jmeno",$prihl_uziv[1]);
  $smarty->assign("prvni_level",$prihl_uziv[2]);
}

//button na vypis vsech prihl. uziv.
$smarty->assign("windowtext2",$prihl_uziv[3]);

// velikost okna
$smarty->assign("windowdelka2","170");
$smarty->assign("windowpadding2","40");
	 
// pozice okna
$smarty->assign("windowtop2","150");
$smarty->assign("windowleft2","350");

$smarty->assign("subcat_select",0);

//zacatek vlastniho obsahu

 //zkontrolujem jeslti probehnou includy
 if( (include "include/function.fn-kontrola-omezeni.php") != '1' )
 {
    $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");
    $smarty->assign("chyba","Chyba! Nelze vlozit externi soubor \"function.fn-kontrola-omezeni\" ");
    $smarty->display('faktury/fn-kontrola-omezeni.tpl');
    exit;
 }
 
 if( (include "plugins/serializer/Serializer.php") != '1' )
 {
    $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");
    $smarty->assign("chyba","Chyba! Nelze (správně) includovat externi knihovnu \"Serializer\" ");
    $smarty->display('faktury/fn-kontrola-omezeni.tpl');
    exit;
 }

 if( (include "plugins/xml2array/function.xml2array.php") != '1' )
 {
    $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");
    $smarty->assign("chyba","Chyba! Nelze (správně) includovat externi knihovnu \"xml2array\" ");
    $smarty->display('faktury/fn-kontrola-omezeni.tpl');
    exit;
 }

 $smarty->assign("enable_fn_check",1); //include javascriptu pro ajax funkce
 
 //vypsani hlavicky
 $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");
 
 $smarty->assign("form_action1",$_SERVER["PHP_SELF"]);
 
 if( $_GET["mod"] == 1 )
 { //zobrazeni seznamu souboru
 
    $smarty->assign("mod",1);
    $smarty->assign("form_action",$_SERVER["PHP_SELF"]);
 
    $soubor = nacti_soubory("fn_check_log");
    $smarty->assign("soubory",$soubor);
 
 }
 elseif( $_GET["mod"] == 2 )
 { //vygenrovani logu

   $zamek_stav = zamek("status");
   
   if( $zamek_stav == 1 )
   {
    $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");
    $smarty->assign("chyba","Chyba! Generování souboru již probíhá.");
    $smarty->display('faktury/fn-kontrola-omezeni.tpl');
    exit;
   }
   
   zamek("lock");
   
   //vlastni kontrola
   list($vlastnici, $vlastnici_pocet) = nacteni_vlastniku();

   $objekty_kontrola = vyber_objektu($vlastnici);

   $data = kontrola($objekty_kontrola);

   $smarty->assign("vlastnici_pocet",$vlastnici_pocet);
 
   //transformace do xml formatu
   $options = array( "addDecl" => true,  "defaultTagName" => "zaznam",
	    "linebreak" => "\n",  "encoding" => "UTF-8",  "rootName" => "objekty");
   $serializer = new XML_Serializer($options);
   $serializer->serialize($data);

   $xml_data = $serializer->getSerializedData();

   $datum_nz = date('Y-m-d-H-i-s');
   $nazev_souboru = "export/fn_check/fn_check_log-".$datum_nz.".xml";

   $nazev_souboru2 = "fn_check_log-".$datum_nz.".xml";
 
   // zapis xml formatu do souboru
   $soubor = fopen($nazev_souboru, "w");
   fwrite($soubor, $xml_data);
   fclose($soubor);

   zamek("unlock");
   
   $smarty->assign("mod",2);
   
   $smarty->assign("nazev_souboru2",$nazev_souboru2);
   
 }
 elseif( $_GET["mod"] == 3)
 {
    $soubor = $_GET["soubor"];
    
    $smarty->assign("mod",3);
    $smarty->assign("nazev_logu","export/fn_check/".$soubor);
    
    $data_z_xml = xml2array(file_get_contents("export/fn_check/".$soubor));
    
    $smarty->assign("data_z_xml",$data_z_xml[objekty][zaznam]);    
 }
 
 //zobrazeni sablony
 $smarty->display('faktury/fn-kontrola-omezeni.tpl');

?>