<?php

require __DIR__ . "/../include/main.function.shared.php";
require __DIR__ . "/../app/config.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => $slim_config]);

require __DIR__ ."/../app/bootstrap-doctrine.php";

require __DIR__ ."/../app/dependencies.php";

require __DIR__ ."/../app/routing.php";

$logger = $container->logger;

$logger->info("others-smlouva-pdf called");

$a = new \App\Core\adminator($conn_mysql, $smarty, $logger);

$auth = new auth_service($container, $conn_mysql, $smarty, $logger);
$auth->checkLevel(146, $a);


$ec = $_POST["ec"];

$jmeno = $_POST["jmeno"];
$nazev_spol = $_POST["nazev_spol"];

$adresa = $_POST["adresa"];
$ico_dic = $_POST["ico_dic"];

$mesto = $_POST["mesto"];
$email = $_POST["email"];
$telefon = $_POST["telefon"];

$kor_adresa = $_POST["kor_adresa"];
$kor_mesto = $_POST["kor_mesto"];

$spec_prip_mista = $_POST["spec_prip_mista"];

$prip_misto_adresa = $_POST["prip_misto_adresa"];
$prip_misto_cp = $_POST["prip_misto_cp"];
$prip_misto_mesto = $_POST["prip_misto_mesto"];
$prip_misto_psc = $_POST["prip_misto_psc"];

$adr_prip_jako_kor = $_POST["adr_prip_jako_kor"];

$prip_tech = $_POST["prip_tech"];

//
//sluzba internet
//
$internet_sluzba = $_POST["internet_sluzba"];

$int_tarify_id_tarifu = $_POST["int_tarify_id_tarifu"];
$int_sluzba_tarif_text = $_POST["int_sluzba_tarif_text"];
$int_sluzba_tarif_agr = $_POST["int_sluzba_tarif_agr"];

$int_sluzba_tarif_cena = $_POST["int_sluzba_tarif_cena"];
$int_sluzba_tarif_cena_s_dph = $_POST["int_sluzba_tarif_cena_s_dph"];

$int_sluzba_rychlost = $_POST["int_sluzba_rychlost"];

$int_verejna_ip = $_POST["int_verejna_ip"];

$int_verejna_ip_cena = $_POST["int_verejna_ip_cena"];
$int_verejna_ip_cena_s_dph = $_POST["int_verejna_ip_cena_s_dph"];

if( $int_verejna_ip == 1 )
{
  if( !isset($int_verejna_ip_cena) )
  { $int_verejna_ip_cena = "99.2"; }
	  
  if( !isset($int_verejna_ip_cena_s_dph) )
  { $int_verejna_ip_cena_s_dph = "119"; }
}

//
//sluzba iptv
//
$iptv_sluzba = $_POST["iptv_sluzba"];

if( $iptv_sluzba == 1 )
{
 $iptv_sluzba_id_tarifu = $_POST["iptv_sluzba_id_tarifu"];

 $iptv_sluzba_cena = $_POST["iptv_sluzba_cena"];
 $iptv_sluzba_cena_s_dph = $_POST["iptv_sluzba_cena_s_dph"];

 $pocet_tb = $_POST["pocet_tb"];

 for($i=1; $i<=$pocet_tb; $i++)
 {	
     $tb = "tb".$i;
     $$tb = $_POST[$tb];
     
     $tb_cena = "tb_cena_".$i;
     $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     
     $$tb_cena = $_POST[$tb_cena];
     $$tb_cena_s_dph = $_POST[$tb_cena_s_dph];     
 }

}
	    
//sluzba voip
$voip_sluzba = $_POST["voip_sluzba"];

$voip_cislo = $_POST["voip_cislo"];
$voip_typ = $_POST["voip_typ"];

//soucet, pro potreby slev
$soucet_bez_dph_pole = array($int_sluzba_tarif_cena,$int_verejna_ip_cena,$iptv_sluzba_cena);
$soucet_s_dph_pole = array($int_sluzba_tarif_cena_s_dph,$int_verejna_ip_cena_s_dph,$iptv_sluzba_cena_s_dph);

for($i=1; $i<=$pocet_tb; $i++)
{
    $tb = "tb".$i;
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     	    
    $soucet_bez_dph_pole[] = $$tb_cena;
    $soucet_s_dph_pole[] = $$tb_cena_s_dph;
}
	
$soucet_bez_dph = array_sum($soucet_bez_dph_pole);
$soucet_s_dph = array_sum($soucet_s_dph_pole);

//sleva a dal

// prepinac jestli doporucit slevu
if( ($internet_sluzba == 1 ) and ($iptv_sluzba == 1) )
{
    if( $soucet_s_dph > 900 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "15";
    }
    elseif( $soucet_s_dph > 700 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "10";
    }
    elseif( $soucet_s_dph > 500 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "5";
    }
}

$sleva_select = $_POST["sleva_select"];

if( $sleva_select == 1)
{
    $sleva_hodnota = $_POST["sleva_hodnota"];

    if( (strlen($sleva_hodnota) < 1 ) )
    { $sleva_hodnota = $sleva_hodnota_dop; }
    
    if( (strlen($sleva_hodnota) < 1 ) )
    { $sleva_hodnota = "0"; }
    
    $slevova_cast = ( $soucet_bez_dph / 100 ) * $sleva_hodnota;

    $celk_cena_po_sleve = $soucet_bez_dph - $slevova_cast;
}

$zpusob_placeni = $_POST["zpusob_placeni"];
$vs = $_POST["vs"];

if( (strlen($vs) < 1 ))
{ 
    if( (strlen($ico_dic) > 1 ))
    { $vs = "dle faktury"; }
    else
    { $vs = $ec; }
}

$splatnost_ke_dni = $_POST["splatnost_ke_dni"];

if( (strlen($splatnost_ke_dni) < 1 ))
{ $splatnost_ke_dni = "15."; }

$celk_cena = $_POST["celk_cena"];
$celk_cena_s_dph = $_POST["celk_cena_s_dph"];


if($sleva_select == 1)
{
    if( (strlen($celk_cena) < 1 ) )
    { $celk_cena = $celk_cena_po_sleve; }

    if( (strlen($celk_cena_s_dph) < 1 ) )
    { $celk_cena_s_dph = round( $celk_cena_po_sleve * 1.20); }
}
else
{
    if( (strlen($celk_cena) < 1 ) )
    { $celk_cena = $soucet_bez_dph; }
    
    if( (strlen($celk_cena_s_dph) < 1 ) )
    { $celk_cena_s_dph = $soucet_s_dph; }
}

if ( $prip_tech == 1 )
{
 $celk_cena = round($celk_cena,2);
 $celk_cena_s_dph = round($celk_cena_s_dph);
}

$odeslano = $_POST["odeslano"];

$min_plneni = $_POST["min_plneni"];
$min_plneni_doba = $_POST["min_plneni_doba"];

$platba = $_POST["platba"];

#
#	zacatek stranky pro zobrazeni formu
#

if( ( ( strlen($jmeno) < 2 ) or ( !isset($odeslano) ) ) )
{

  echo '<html>

  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
  <title>Průvodce tiskem smlouvy </title>

  <style>

  .input1{ width: 80px; height: 17px; font-size: 10px; }

  .input-size-big{ font-size: 12px; }

  .label-font { font-size: 13px; }

  .select1 { font-size: 10px;; color: grey; }

  .input-border{ border: 2px solid black; }

  </style>

  </head>

  <body>';

  if ( $send != "OK" )
  { echo "<p><span style=\"color: blue; font-weight: bold; \"> Pro odeslání formuláře použijte tlačítko OK. </span></p>"; }

  //
  //  zobrazeni hlavni casti formu
  //
  require("inc.smlouva.input.form.php");
 
  echo "</body>
  </html>";

} // konec if !isset nazev
else
{ //budeme generovat
 
 // konverze promennych
 require("inc.smlouva.gen.prepare.vars.php");
 // konec pripravy promennych
 
 // opravdovy zacatek generovani 
 define('FPDF_FONTPATH',"include/font/");
 // require("../include/fpdf.class.php");

 require("inc.smlouva.gen.main.php");
 
 //presmerovani na dpdf soubor

 echo '<html>
        <head>
            <title>Tisk smlouvy</title>
        </head>
       <body>
          Vygenerovany soubor je <a href="/'.$nazev_souboru.'" >zde</a>.
       </body>
      </html>';
								    
} //konec else !isset nazev
