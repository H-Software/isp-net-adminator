<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 146;
$auth->check_all();

$ac = new adminatorController($conn_mysql, $smarty, $logger, $auth);

$smarty->assign("page_title","Adminator3 :: Ostatní :: Tisk");

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

//zacatek vlastniho obsahu

//pokus o vypsani souborů

function nacti_soubory($find_string)
{
 $handle=opendir('print/temp/'); 
 $i=0;

 while (false!==($file = readdir($handle))) 
 { 
    if ( $file!="." && $file!=".." && !is_dir($file) && ereg($find_string,$file) ) 
    { 
        $soubor[$i]="$file";
        $i++;
    } 
 }
 closedir($handle); 

 sort($soubor);
 
 return $soubor;
 
}

$smarty->assign("action","others-print-redirect.php");

$soubor3 = nacti_soubory("smlouva-fiber");
$smarty->assign("soubory_smlouvy_new",$soubor3);

$soubor4 = nacti_soubory("reg-form-pdf");
$smarty->assign("soubory_regform_new",$soubor4);

$soubor5 = nacti_soubory("smlouva-v3");
$smarty->assign("soubory_smlouva_v3",$soubor5);

$soubor6 = nacti_soubory("reg-form-v3");
$smarty->assign("soubory_reg_form_2012_05",$soubor6);


$smarty->display('others/print.tpl');

?>
