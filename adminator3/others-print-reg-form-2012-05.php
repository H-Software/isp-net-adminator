<?php

require __DIR__ . '/vendor/autoload.php';

// init db functions defs
require "include/main.function.shared.php";
// autoload, init DB conns, init Illuminate\Database
require "app/bootstrap.php";

// end of app bootstrap
$logger = $container->get('logger');
$smarty = $container->get('smarty');

$logger->info("others-print-reg-form-2012-05 called");
        
$a = new \App\Core\adminator($conn_mysql, $smarty, $logger);

$auth = new auth_service($container, $conn_mysql, $smarty, $logger);
$auth->checkLevel(146, $a);

$smarty->assign("page_title","Adminator3 :: Ostatní :: Tisk - Reg. Form. 2012-05");

$smarty->assign("nick_a_level",\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email." (".$level.")");
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
