<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty);
$auth->page_level_id = "38";
$auth->check_all();

$smarty->assign("page_title","Adminator3 :: úvodní stránka");

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

$se_cat_adminator_link = $_SERVER['HTTP_HOST'];
$se_cat_adminator_link = str_replace("adminator3", "adminator2", $se_cat_adminator_link);

$smarty->assign("se_cat_adminator","adminator2");
$smarty->assign("se_cat_adminator_link",$se_cat_adminator_link);

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


//vlozeni prihlasovaci historie
list_logged_users_history($conn_mysql, $smarty);

//opravy a zavady vypis
if ( $auth->check_level(101,false) )
{
 $v_reseni_filtr = $_GET["v_reseni_filtr"];
 $vyreseno_filtr = $_GET["vyreseno_filtr"];
 $limit=$_GET["limit"];

 if( !isset($v_reseni_filtr) ){ $v_reseni_filtr="99"; }
 if( !isset($vyreseno_filtr) ){ $vyreseno_filtr="0"; }

 if( !isset($limit) ){ $limit="10"; }

 // vypis
 $smarty->assign("opravy_povoleno",0);

 $smarty->assign("pocet_bunek",11);
 
 $smarty->assign("vyreseno_filtr",$vyreseno_filtr);
 $smarty->assign("v_reseni_filtr",$v_reseni_filtr);
 $smarty->assign("limit",$limit);
  
 $smarty->assign("action",$_SERVER["PHP_SELF"]);
  
 $oprava = new opravy;

 $oprava->vypis_opravy();
 
 $smarty->assign("dotaz_radku",$dotaz_radku);
		   
}

//informace z modulu neuhrazené faktury
	
 $neuhr_faktury_pole = show_stats_faktury_neuhr();
 

 $smarty->assign("d",$neuhr_faktury_pole[0]);

 $smarty->assign("count_total", $neuhr_faktury_pole[0]);
 $smarty->assign("count_ignored", $neuhr_faktury_pole[1]);
 $smarty->assign("count_unknown", $neuhr_faktury_pole[2]);
 $smarty->assign("date_last_import", $neuhr_faktury_pole[3]);

 //tady opravy az se dodelaj
 																    
 //generovani zprav z nastenky

 if ( check_level($level,87) )
 {
   $smarty->assign("nastenka_povoleno",1);
   $smarty->assign("datum",date("j. m. Y"));
   $smarty->assign("sid",$sid);
 
   $nastenka = new board($conn_mysql);

   $nastenka->prepare_vars("");
   
   $nastenka->view_number = 10; //zprávy budou zobrazeny po ...
   
   $zpravy = $nastenka->show_messages();
   
   $smarty->assign("zpravy",$zpravy);
   
   $page = $nastenka->show_pages();
   
   $smarty->assign("strany",$page);
	
 }

$smarty->display('home.tpl');
