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


if( !( check_level($level,149) ) )
{ // neni level
 
 $smarty->assign("page_title","Adminator3 :: chybny level");
 $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");

 $smarty->display('index-nolevel.tpl');

 exit;
}

$smarty->assign("page_title","Adminator3 :: N.F. :: Kontrola omezeni vs. platby");

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

function synchro_db_nf() 
{

 // synchro tabulky neuhr. faktur mezi MySQL a Postgresem :)
 $pocet_cyklu=0;

 $vymazani_pg_fn = pg_query("DELETE FROM faktury_neuhrazene");

 $dotaz_mysql_fn = mysql_query("SELECT * FROM faktury_neuhrazene ORDER BY id");
 $dotaz_mysql_fn_radku = mysql_num_rows($dotaz_mysql_fn);

 while( $data = mysql_fetch_array($dotaz_mysql_fn) )
 {
  //vypis z mysql
  $id = $data["id"];
  $Cislo = $data["Cislo"];
  $VarSym = $data["VarSym"];
  $Datum = $data["Datum"];
  $DatSplat = $data["DatSplat"];
  $KcCelkem = $data["KcCelkem"];
  $KcLikv = $data["KcLikv"];
  $Firma = $data["Firma"];
  $Jmeno = $data["Jmeno"];
  $ICO = $data["ICO"];
  $DIC = $data["DIC"];
  $par_id_vlastnika = $data["par_id_vlastnika"];
  $par_stav = $data["par_stav"];
  $datum_vlozeni = $data["datum_vlozeni"];
  $overeno = $data["overeno"];
  $aut_email_stav = $data["aut_email_stav"];
  $aut_email_datum = $data["aut_email_datum"];
  $aut_sms_stav = $data["aut_sms_stav"];
  $aut_sms_datum = $data["aut_sms_datum"];
  $ignorovat = $data["ignorovat"];
  $po_splatnosti_vlastnik = $data["po_splatnosti_vlastnik"];

  //vlozeni do PG
  $fn_add = array( "id" => $id, "cislo" => $Cislo,"varsym" => $VarSym, "datum" => $Datum,
                     "datsplat" => $DatSplat, "kccelkem" => $KcCelkem, "kclikv" => $KcLikv,
                     "firma" => $Firma, "jmeno" => $Jmeno , "ico" => $ICO, "dic" => $DIC,
                     "par_id_vlastnika" => $par_id_vlastnika, "par_stav" => $par_stav,
                     "datum_vlozeni" => $datum_vlozeni, "overeno" => $overeno,
                     "aut_email_stav" => $aut_email_stav, "aut_email_datum" => $aut_email_datum,
                     "aut_sms_stav" => $aut_sms_stav, "aut_sms_datum" => $aut_sms_datum,
                     "ignorovat" => $ignorovat, "po_splatnosti_vlastnik" => $po_splatnosti_vlastnik
     );


   global $db_ok2;    
   $res = pg_insert($db_ok2, 'faktury_neuhrazene', $fn_add);

   $pocet_cyklu++;

 } //konec while 

 return $pocet_cyklu;

} //konec funkce synchro_db_nf

$pocet_synchro_faktur = synchro_db_nf();

//vlastni obsah
 
    $sql_dotaz =
    
    "SELECT 
     DISTINCT ON (t2.ip)
      COALESCE(nf.id,0),
	t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.billing_suspend_status,
	t2.id_komplu, t2.ip, t2.dov_net, t2.sikana_status, 
        t2.sikana_text, nf.datsplat, nf.cislo, count(nf.id) AS nf_pocet,
	to_char(nf.datum, 'YYYY-MM') as nf_datum2
   FROM 
	vlastnici AS t1 LEFT JOIN objekty AS t2
   ON t1.id_cloveka=t2.id_cloveka 

   LEFT JOIN faktury_neuhrazene nf
   ON t1.id_cloveka=nf.par_id_vlastnika

 	WHERE ( t1.archiv IS NULL OR t1.archiv = 0) 
           AND (t2.dov_net LIKE 'n' 
		OR t2.sikana_status LIKE 'a')
	   AND (t1.billing_suspend_status = 0)

        GROUP BY t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.billing_suspend_status,
	     t2.id_komplu, t2.ip, t2.dov_net, t2.sikana_status, 
             nf.datsplat, nf.cislo, nf.datum, nf.id, t2.sikana_text";
    
   $dotaz_vlastnici = pg_query($sql_dotaz);
   $dotaz_vlastnici_num = pg_num_rows($dotaz_vlastnici);

   $index = 1;
   
   while( $data = pg_fetch_array($dotaz_vlastnici))
   { 
	//print "objekt $i: ".$data_obj["id_komplu"]."<br>";
        $id_komplu = $data["id_komplu"];
        $id_cloveka = $data["id_cloveka"];
        $sikana_text = $data["sikana_text"];
	$nf_cislo = $data["cislo"];

	$nf_pocet = $data["nf_pocet"];
	$nf_datum2 = $data["nf_datum2"];

        $zprava = "";

        if( $data["dov_net"] == "n" )
        { $duvod = "netn"; }
        elseif( $data["sikana_status"] == "a")
        { 
	    $duvod = "sikana"; 
	
	    if( ereg(".+za fakturu č. [0123456789]+.+", $sikana_text) )
            {
                list($a1, $a2) = split("za fakturu č.", $sikana_text, 2);
                list($b1, $b2, $b3) = split(" ", $a2, 3);

                $cislo_faktury_sikana = ereg_replace(" ","",$b2);
                //print "cislo faktury: -".$cislo_faktury."-<br>";
            }
            else
            { $cislo_faktury_sikana = ""; }

	}
        else
        { $duvod = ""; }

        //$dotaz_fa = mysql_query("SELECT Cislo,DATE_FORMAT(datum, '%Y-%m') as datum2 FROM faktury_neuhrazene WHERE par_id_vlastnika = '$id_cloveka' ");
        //$dotaz_fa_num = mysql_num_rows($dotaz_fa);

        if( $nf_pocet == 0 )
        { //ne-nalezena dluzna faktura

          if( ($duvod == "sikana") and ( $cislo_faktury_sikana > 0 ) )
          { $zprava .= "<span style=\"color: red;\" > chyba! nic nedluzi, ale ma sikanu za FA </span>"; }
          else
          { $zprava .= "<span style=\"color: maroon;\" > nic nedluzi (divny) </span>"; }
        }
        elseif( $nf_pocet == 1 )
        { //k objektu nalezena 1. faktura
        
             if( ($duvod == "sikana") and ($nf_cislo == $cislo_faktury_sikana) )
             {
                $platba_dotaz = pg_query("SELECT * FROM platby WHERE ( id_cloveka = '$id_cloveka' AND zaplaceno_za LIKE '$nf_datum2' ) ");
                $platba_dotaz_num = pg_num_rows($platba_dotaz);

                if( $platba_dotaz_num > 0 )
                {
                  $zprava .= "<span style=\"color: red;\" > chyba! existuje hot. platba a ma sikanu za Neuhr. FA</span>";
                }
                else
                {
                  $zprava .= "<span style=\"color: green;\" > dluzi furt (OK) </span>";
                }
              }
              elseif( ($duvod == "netn") and ($nf_cislo == $cislo_faktury_sikana) )
              {
                  $zprava .= "<span style=\"color: maroon;\" >nic nedluzi, ale ma netn (divny)</span>";
              }
              else
              {
                  $zprava .= "<span style=\"color: maroon;\" > nic nedluzi, ale ma omezeni (asi za neco jinyho) </span>";
              }
            }
            else
            { //nalezeno více faktur
              $zprava .= "<span style=\"color: maroon;\" >dluzi vice faktur, neumim zjistit </span>";
            }

            $zaznam[] = "<b>zaznam c</b>: ".$index.", <b>id_komplu</b>: ".$id_komplu.", <b>id_cloveka</b>: ".$id_cloveka
            . ",<b>duvod</b>: ".$duvod.", <b>cislo_fa</b>: ".$nf_cislo.", <b>cislo_fa_sikana:</b> ".$cislo_faktury_sikana
	    .". ".$zprava."<br>";

	$index++;
   }

   //return array($vlastnici,$dotaz_vlastnici_num);


 $smarty->assign("nadpis","Kontrola omezení objektu vs. neuhr. fakturám");

 $smarty->assign("faktury_pocet",$pocet_synchro_faktur);
 
 $smarty->assign("vlastnici_pocet",$dotaz_vlastnici_num);
  
 $smarty->assign("pole_data",$zaznam);

 $smarty->display('faktury/fn-kontrola-omezeni.tpl');

?>
