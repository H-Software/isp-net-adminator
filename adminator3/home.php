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


if( !( check_level($level,38) ) )
{ // neni level
 
 $smarty->assign("page_title","Adminator3 - chybny level");
 $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br> (current_level: " . $level . ")");

 $smarty->display('index-nolevel.tpl');

 exit;
}

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
 $dotaz_historie=$conn_mysql->query("SELECT nick, date, ip FROM login_log ORDER BY date DESC LIMIT 5");

 while ( $data_historie=$dotaz_historie->fetch_array() )
 {
    $datum = strftime("%d.%m.%Y %H:%M:%S", $data_historie["date"] );

    $logged_users[] = array( "nick" => $data_historie["nick"], "datum" => $datum, "ip" => $data_historie["ip"]);    
 }

$smarty->assign("logged_users",$logged_users);

//opravy a zavady vypis
if ( check_level($level,101) )
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
 
   $nastenka = new board;
   $nastenka->prepare_vars();
   
   $nastenka->view_number = 10; //zprávy budou zobrazeny po ...
   
   $zpravy = $nastenka->show_messages();
   
   $smarty->assign("zpravy",$zpravy);
   
   $page = $nastenka->show_pages();
   
   $smarty->assign("strany",$page);
	
 }

$smarty->display('home.tpl');
		 
?>
