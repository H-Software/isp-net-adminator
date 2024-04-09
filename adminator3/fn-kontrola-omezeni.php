<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 149;
$auth->check_all();

$ac = new adminatorController($conn_mysql, $smarty, $logger, $auth);
$a = new adminator($conn_mysql, $smarty, $logger, $auth);

$smarty->assign("page_title","Adminator3 :: N.F. :: Kontrola omezeni vs. platby");

$smarty->assign("nick_a_level",$nick." (".$level.")");
$smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

//kategorie
$uri=$_SERVER["REQUEST_URI"];
$uri_replace = str_replace ("adminator3", "", $uri);

list($kategorie, $kat_2radka, $mapa) = $ac->zobraz_kategorie($uri,$uri_replace);

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

$prihl_uziv = $ac->vypis_prihlasene_uziv();

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

// vlastni obsah
$pocet_synchro_faktur = $a->synchro_db_nf();
 
$rs = $a->fn_kontrola_omezeni();

 $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");

 $smarty->assign("faktury_pocet",$pocet_synchro_faktur);
 
 $smarty->assign("vlastnici_pocet",$dotaz_vlastnici_num);
  
 $smarty->assign("pole_data",$zaznam);

 $smarty->display('faktury/fn-kontrola-omezeni.tpl');
