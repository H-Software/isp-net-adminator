<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

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


if( !( check_level($level,92) ) )
{ // neni level
 
 $smarty->assign("page_title","Adminator3 :: chybny level");
 $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");

 $smarty->display('index-nolevel.tpl');

 exit;
}

$smarty->assign("page_title","Adminator3 :: Platby");

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

// $body .= "";
$body .= "Prosím vyberte z podkategorie výše....";

$smarty->assign("body",$body);

$smarty->display('platby-cat.tpl');

?>
