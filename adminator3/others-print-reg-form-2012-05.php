<?php

require "include/main.function.shared.php";
require "app/config.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);

require __DIR__ ."/app/bootstrap-doctrine.php";

require "app/dependencies.php";

require "app/routing.php";

$logger = $container->logger;

$logger->info("others-print called");
        
$a = new \App\Core\adminator($conn_mysql, $smarty, $logger);

$auth = new auth_service($container, $conn_mysql, $smarty, $logger);
$auth->checkLevel(146, $a);

$smarty->assign("page_title","Adminator3 :: Ostatní :: Tisk - Reg. Form. 2012-05");

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

//
//zacatek vlastniho obsahu
//

$rf = new print_reg_form();

$button_send = $_POST["send"];

if( isset($button_send) ){

    //check a processing form
    $rf->load_input_vars();        
    
    //generate pdf file
    $rf->generate_pdf_file();

    $smarty->assign("file_name",$rf->file_name);

    //finalni zobrazeni sablony
    $smarty->display('others/print-reg-form-2012-05.tpl');
}
else{

    //check a processing form
    $rf->load_input_vars();

    //zobrazeni formu a vyplneni hodnot
    $smarty->assign("form_action","");

    $smarty->assign("input_ec",$rf->input_ec);

    //finalni zobrazeni sablony
    $smarty->display('others/print-reg-form-2012-05-form.tpl');
}
