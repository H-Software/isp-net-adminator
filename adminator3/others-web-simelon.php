<?php

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/config.php";
// slim config
require "app/settings.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App($config);

require __DIR__ ."/app/bootstrap-doctrine.php";

require "app/dependencies.php";

require "app/routing.php";

$logger = $container->logger;

$logger->info("others-web-simelon called");
        
// $this->checkLevel(95);
$a = new \App\Core\adminator($conn_mysql, $smarty, $logger);

$auth = new auth_service($container, $conn_mysql, $smarty, $logger);
$auth->checkLevel(151, $a);

// $ac = new adminatorController($conn_mysql, $smarty, $logger, $auth);

$smarty->assign("page_title","Adminator3 :: Ostatní :: Web Simelon");

$smarty->assign("nick_a_level",$nick." (".$level.")");
$smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

//kategorie
$uri=$_SERVER["REQUEST_URI"];
$uri_replace = str_replace ("adminator3", "", $uri);

// list($kategorie, $kat_2radka, $mapa) = $ac->zobraz_kategorie($uri,$uri_replace);

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

// $prihl_uziv = $ac->vypis_prihlasene_uziv();

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
// $smarty->assign("windowtext2",$prihl_uziv[3]);

// // velikost okna
// $smarty->assign("windowdelka2","170");
// $smarty->assign("windowpadding2","40");
	 
// // pozice okna
// $smarty->assign("windowtop2","150");
// $smarty->assign("windowleft2","350");

// $smarty->assign("subcat_select",0);

//zacatek vlastniho obsahu
try {
	$count = $conn_mysql->select_db("simelonnet");
} catch (Exception $e) {
	die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

//tab qestions
try {
	$dotaz_q = $conn_mysql->query("
	SELECT id_question, jmeno, prijmeni, telefon, email, vs, dotaz, text, datum_vlozeni
	FROM questions ORDER BY id_question
	");
} catch (Exception $e) {
	die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

  $pole_q = array();
  
  while( $data_q = $dotaz_q->fetch_array() )
  {
    $pole_q[] = array( 
			"id_question" => $data_q["id_question"], "jmeno" => $data_q["jmeno"], 
			"prijmeni" => $data_q["prijmeni"], "telefon" => $data_q["telefon"],
			"email" => $data_q["email"], "vs" => $data_q["vs"],
			"dotaz" => $data_q["dotaz"], "text" => $data_q["text"],
			"datum_vlozeni" => $data_q["datum_vlozeni"]
		    );  
  }

$smarty->assign("data_q",$pole_q);

//tab orders
try {
	$dotaz_o = $conn_mysql->query("
	SELECT id_order, jmeno, prijmeni, adresa, telefon, email,
		internet, text_internet, iptv, balicek, text_iptv,
		voipcislo, voip, text_voip, poznamka, datum_vlozeni
	FROM orders ORDER BY id_order
	");
} catch (Exception $e) {
	die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

  $pole_o = array();
  
  while( $data_o = $dotaz_o->fetch_array() )
  {
    $pole_o[] = array( 
			"id_order" => $data_o["id_order"], "jmeno" => $data_o["jmeno"], 
			"prijmeni" => $data_o["prijmeni"], "adresa" => $data_o["adresa"],
			"telefon" => $data_o["telefon"], "email" => $data_o["email"],
			"internet" => $data_o["internet"], "text_internet" => $data_o["text_internet"],
			"iptv" => $data_o["iptv"], "balicek" => $data_o["balicek"],
			"text_iptv" => $data_o["text_iptv"], "voipcislo" => $data_o["voipcislo"],
			"voip" => $data_o["voip"], "text_voip" => $data_o["text_voip"],
			"poznamka" => $data_o["poznamka"], "datum_vlozeni" => $data_o["datum_vlozeni"]
		    );  
  }

$smarty->assign("data_o",$pole_o);

//print_r($pole_o);

//zpatky default DB
try {
	$count = $conn_mysql->select_db("adminator2");
} catch (Exception $e) {
	die (init_helper_base_html("adminator3") . "<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

//finalni zobrazeni stranky
$smarty->display("others/web-simelon.tpl");
