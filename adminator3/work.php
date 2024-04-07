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


if( !( check_level($level,16) ) )
{ // neni level
 
 $smarty->assign("page_title","Adminator3 :: chybny level");
 $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");

 $smarty->display('index-nolevel.tpl');

 exit;
}

$smarty->assign("page_title","Adminator3 :: Work");

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
$smarty->assign("enable_work",1); //slozeni JS skriptu pro stranku
$smarty->assign("action",$_SERVER["PHP_SELF"]);

$data_s = "/srv/www/htdocs.ssl/reinhard.remote.log";

/*
$akce = $_POST["akce"];
$iptables = $_POST["iptables"];
$dns = $_POST["dns"];
$optika = $_POST["optika"];


 if( $iptables == 1 ){ $prvni=$iptables; $pocet+20; }else{ $prvni = 0; }
 if( $dns == 1 ){ $druha=$dns; $pocet+20; }else{ $druha = 0; }
 if( $optika == 1 ){ $treti=$optika; $pocet+20; }else{ $treti = 0; } 
 if( ( $iptables==0 and $dns==0 and $optika == 0 ) ){ $akce=""; }
*/
    
 // uložení odpovědi v případě vypnutého JavaScriptu
 if( isset($_GET["akce"]) )
 { // nelze pouzi JS/ajax
   echo "neumim AJAX ";
   //mysql_query("UPDATE anketa SET pocet = pocet + 1 WHERE id = " . intval($_GET["anketa"]));
 }

 if( ( file_exists ($data_s) ) )
 {
   $fp = fopen($data_s, "r");
   $odpoved_file = fread($fp, filesize ($data_s));
   //echo $data;
   fclose ($fp);
 }
 else
 { $odpoved_file = "\n log soubor neexistuje \n"; }
		 
$smarty->assign("odpoved_file",$odpoved_file); //log ze souboru
     
$smarty->display('work.tpl');

?>
