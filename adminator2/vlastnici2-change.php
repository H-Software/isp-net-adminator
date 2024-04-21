<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/class.php"); 
require("include/check_login.php");
require("include/check_level.php");

if( !( check_level($level,40) ) ) 
{
    header("Location: nolevelpage.php");
 
    echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";  
    exit;
}
   
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ("include/charset.php"); 

echo '<link rel="stylesheet" type="text/css" href="/adminator3/plugins/tigra_calendar/tcal.css" />';
echo '<script type="text/javascript" src="/adminator3/plugins/tigra_calendar/tcal.js"></script>';
echo '<script type="text/javascript" src="/adminator3/plugins/tigra_calendar/custom-a2-vlastnici2-change.js"></script>';
?>

<title>Adminator2 - Přidání / úprava vlastníka </title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

<tr>
  <td colspan="2">

<?php

//vytvoreni objektu
$Aglobal = new Aglobal();

$update_id = intval($_POST["update_id"]);
$odeslano=$_POST["odeslano"];
$send=$_POST["send"];
$firma_add = $_GET["firma_add"];

if( ($update_id > 0) ) { $update_status=1; }

if( ( $update_status==1 and !( isset($send)) ) )
{ //rezim upravy

  $dotaz_upd = pg_query( "SELECT * FROM vlastnici WHERE id_cloveka='".intval($update_id)."' ");
  $radku_upd= pg_num_rows($dotaz_upd);
 
  if($radku_upd==0) echo "Chyba! Požadovaná data nelze načíst! ";
  else
  {
    
    while($data=pg_fetch_array($dotaz_upd)):
    
      // primy promenny 
      $nick2=$data["nick"];    $vs=$data["vs"];	 $k_platbe=$data["k_platbe"];	
      $jmeno=$data["jmeno"];   $prijmeni=$data["prijmeni"];	
      $ulice=$data["ulice"];   $mesto=$data["mesto"]; 	$psc=$data["psc"];
      $email=$data["mail"];     $icq=$data["icq"];	$tel=$data["telefon"];
      $firma=$data["firma"];	$poznamka=$data["poznamka"];
      $ucetni_index=$data["ucetni_index"]; $archiv=$data["archiv"]; 
      $fakt_skupina=$data["fakturacni_skupina_id"]; $typ_smlouvy=$data["typ_smlouvy"];
      $fakturacni=$data["fakturacni"]; $splatnost=$data["splatnost"];        
      $trvani_do=$data["trvani_do"];   $datum_podpisu=$data["datum_podpisu"];
      $sluzba_int = $data["sluzba_int"];	$sluzba_iptv = $data["sluzba_iptv"];
      $sluzba_voip = $data["sluzba_voip"];

      $sluzba_int_id_tarifu = $data["sluzba_int_id_tarifu"];
      $sluzba_iptv_id_tarifu = $data["sluzba_iptv_id_tarifu"];
        
      $billing_freq = $data["billing_freq"];
        
      $billing_suspend_status = $data["billing_suspend_status"];
      $billing_suspend_reason = $data["billing_suspend_reason"];
      
      $billing_suspend_start  = $data["billing_suspend_start"];
      $billing_suspend_stop   = $data["billing_suspend_stop"];
            
      //konverze z DB formatu
      list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$billing_suspend_start);
      $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

      list($b_s_t_rok,$b_s_t_mesic,$b_s_t_den) = explode("-",$billing_suspend_stop);
      $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;
	
    endwhile;
    
  }
    
}
else
{ // rezim pridani, ukladani

    $nick2=$_POST["nick2"];		$vs=$_POST["vs"];		$k_platbe=$_POST["k_platbe"];
    $jmeno=$_POST["jmeno"];		$prijmeni=$_POST["prijmeni"];	$ulice=$_POST["ulice"];	

    $mesto=$_POST["mesto"]; 		$psc=$_POST["psc"];	
    $email=$_POST["email"];		$icq=$_POST["icq"];		$tel=$_POST["tel"];

    $fakturacni=$_POST["fakturacni"];	$ftitle=$_POST["ftitle"];	$fulice=$_POST["fulice"];	
    $fmesto=$_POST["fmesto"];		$fpsc=$_POST["fpsc"];		
    $ico=$_POST["ico"]; 			$dic=$_POST["dic"]; 		$ucet=$_POST["ucet"];
    $splatnost=$_POST["splatnost"];		$cetnost=$_POST["cetnost"];
    $firma=$_POST["firma"];			$poznamka=$_POST["poznamka"];
    $ucetni_index=$_POST["ucetni_index"];	$archiv=$_POST["archiv"];	
    $fakt_skupina=$_POST["fakt_skupina"];	$splatnost=$_POST["splatnost"];
    
    $typ_smlouvy = intval($_POST["typ_smlouvy"]);
    
    $trvani_do=$_POST["trvani_do"];  
    $datum_podpisu=$_POST["datum_podpisu"];

    $sluzba_int = intval($_POST["sluzba_int"]);
    $sluzba_iptv = intval($_POST["sluzba_iptv"]);
    $sluzba_voip = intval($_POST["sluzba_voip"]);

    $sluzba_int_id_tarifu = intval($_POST["sluzba_int_id_tarifu"]);
    $sluzba_iptv_id_tarifu = intval($_POST["sluzba_iptv_id_tarifu"]);

    $billing_freq = intval($_POST["billing_freq"]);

    $billing_suspend_status = intval($_POST["billing_suspend_status"]);
    $billing_suspend_reason = $_POST["billing_suspend_reason"];
    $billing_suspend_start  = $_POST["billing_suspend_start"];
    $billing_suspend_stop   = $_POST["billing_suspend_stop"];
        
    //systémove
    $send=$_POST["send"];

    if( $firma_add == 2){ $firma=""; }
    elseif ($firma_add == 1){ $firma="1"; }

    if( (strlen($splatnost) < 1) ){ $splatnost="15"; }
     
}

//kontrola promených
if( isset($send) )
{
    if( (strlen($nick2) > 0) )  { vlastnici2pridani::checknick($nick2); }

    if( ( strlen($vs) > 0 ) ) { vlastnici2pridani::checkvs($vs); }   

    if( ( strlen($k_platbe) > 0 ) ) { vlastnici2pridani::check_k_platbe($k_platbe); }   

    if( ( strlen($splatnost) > 0 ) ) { vlastnici2pridani::check_splatnost($splatnost); }   

    if( ( strlen($icq) > 0 ) ) { vlastnici2pridani::check_icq($icq); }   

    if( ( strlen($email) > 0 ) ){ vlastnici2pridani::check_email($email); }

    if( ( strlen($ucetni_index) > 0 ) ){ vlastnici2pridani::check_uc_index($ucetni_index); }

    if( ( strlen($tel) > 0 ) ) { vlastnici2pridani::check_tel($tel); }
    
    if( ( strlen($datum_podpisu) > 0 ) ){ vlastnici2pridani::check_datum($datum_podpisu, "Datum podpisu"); }

    if( $typ_smlouvy == 2){ vlastnici2pridani::check_datum($trvani_do, "Trvání do"); }
    elseif( ( strlen($trvani_do) > 0 ) ){ vlastnici2pridani::check_datum($trvani_do, "Trvání do"); }

    if( $billing_suspend_status == 1 ){ 

	    vlastnici2pridani::check_datum($billing_suspend_start,"Poz. fakturace - od kdy"); 
	    vlastnici2pridani::check_datum($billing_suspend_stop,"Poz. fakturace - do kdy"); 
        
    }

    if( ( strlen($billing_suspend_reason) > 0 ) and ($billing_suspend_status == 1) )
    { vlastnici2pridani::check_b_reason($billing_suspend_reason); }
    
}

if( ( $update_status==1 and !( isset($send)) ) )
{
	// $trvani_do = "";        
	if( (strlen($trvani_do) > 0) )
	{
    	    list($trvani_do_rok,$trvani_do_mesic,$trvani_do_den) = explode("\-",$trvani_do);
    	    $trvani_do=$trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
	}
    
	if( (strlen($datum_podpisu) > 0) )
	{
    	    list($datum_podpisu_rok,$datum_podpisu_mesic,$datum_podpisu_den) = explode("\-",$datum_podpisu);
    	    $datum_podpisu=$datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
	}

}

// jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
if( ($nick2 != "") and ($vs != "") and ($k_platbe != "") and (($fakt_skupina > 0) or ($firma <> 1) or ($archiv == 1)) ):

if($update_status!=1)
{
 
 //zjisti jestli neni duplicitni : nick, vs
 $MSQ_NICK = pg_query( "SELECT * FROM vlastnici WHERE nick LIKE '$nick2' ");    
 if (pg_num_rows($MSQ_NICK) > 0){ $error .= "<h4>Nick ( ".$nick2." ) již existuje!!!</h4>"; $fail = "true"; }

}

// check v modu uprava
if( ( $update_status==1 and (isset($odeslano)) ) )
{

 //zjisti jestli neni duplicitni : nick, vs
 $MSQ_NICK = pg_query( "SELECT * FROM vlastnici WHERE nick LIKE '$nick2' and id_cloveka <> '$update_id' ");    
 if (pg_num_rows($MSQ_NICK) > 0){ $error .= "<h4>Nick ( ".$nick2." ) již existuje!!!</h4>"; $fail = "true"; }

}

//checkem jestli se macklo na tlacitko "OK" :)
if ( preg_match("/^OK$/",$odeslano) ) { echo ""; }
else 
{ 
  $fail="true"; 
  $error.="<div class=\"vlastnici2-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", ";
  $error .= "pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
}

//ulozeni
if ( !( isset($fail) ) ) 
{ 
 
if ( $update_status =="1" )
{
// rezim upravy
    
    //prvne stavajici data docasne ulozime 
    $pole2 .= "<b>akce: uprava vlastnika; </b><br>";
	 
    $vysl4=pg_query("select * from vlastnici WHERE id_cloveka='".intval($update_id)."' ");
    if( ( pg_num_rows($vysl4) <> 1 ) ) {echo "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>"; }
    else
    { 
	while ($data4=pg_fetch_array($vysl4) ):
	
	$nick3 = $data4["nick"];
	$vlast_upd_old["id_cloveka"]=$data4["id_cloveka"];

        //novy zpusob archivace - pro porovnavani zmen
	$pole_puvodni_data["id_cloveka"]=$data4["id_cloveka"];
	$pole_puvodni_data["nick"]=$nick3;			$pole_puvodni_data["jmeno"]=$data4["jmeno"];
	$pole_puvodni_data["prijmeni"]=$data4["prijmeni"];	$pole_puvodni_data["ulice"]=$data4["ulice"];
	$pole_puvodni_data["mesto"]=$data4["mesto"];		$pole_puvodni_data["psc"]=$data4["psc"];
	$pole_puvodni_data["icq"]=$data4["icq"];		$pole_puvodni_data["mail"]=$data4["mail"];
	$pole_puvodni_data["telefon"]=$data4["telefon"];	$pole_puvodni_data["poznamka"]=$data4["poznamka"];
	$pole_puvodni_data["vs"]=$data4["vs"];			$pole_puvodni_data["datum_podpisu"]=$data4["datum_podpisu"];
	$pole_puvodni_data["k_platbe"]=$data4["k_platbe"];	$pole_puvodni_data["ucetni_index"]=$data4["ucetni_index"];
	$pole_puvodni_data["archiv"]=$data4["archiv"];		$pole_puvodni_data["fakturacni_skupina_id"]=$data4["fakturacni_skupina_id"];
	$pole_puvodni_data["splatnost"]=$data4["splatnost"];	$pole_puvodni_data["typ_smlouvy"]=$data4["typ_smlouvy"];
	$pole_puvodni_data["firma"]=$data4["firma"];		$pole_puvodni_data["trvani_do"]=$data4["trvani_do"];
	
	$pole_puvodni_data["sluzba_int"] = $data4["sluzba_int"]; $pole_puvodni_data["sluzba_iptv"] = $data4["sluzba_iptv"];
	$pole_puvodni_data["sluzba_voip"] = $data4["sluzba_voip"];

	$pole_puvodni_data["billing_freq"] = $data4["billing_freq"];

	$pole_puvodni_data["billing_suspend_status"] = $data4["billing_suspend_status"];
	$pole_puvodni_data["billing_suspend_reason"] = $data4["billing_suspend_reason"];
	
	$pole_puvodni_data["billing_suspend_start"]  = $data4["billing_suspend_start"];
	$pole_puvodni_data["billing_suspend_stop"]   = $data4["billing_suspend_stop"];

	if( $sluzba_int == 1 )
	{ $pole_puvodni_data["sluzba_int_id_tarifu"] = $data4["sluzba_int_id_tarifu"]; }
	
	if( $sluzba_iptv == 1 )
	{ $pole_puvodni_data["sluzba_iptv_id_tarifu"] = $data4["sluzba_iptv_id_tarifu"]; }
	
	//$pole_puvodni_data["fakturacni"]=$data4["fakturacni"];
	
        endwhile;   
    }
	
    if( (strlen($trvani_do) > 0) )
    {
	list($trvani_do_den,$trvani_do_mesic,$trvani_do_rok) = split("\.",$trvani_do);
	$trvani_do=$trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
    }

    if( (strlen($datum_podpisu) > 0) )
    {
	list($datum_podpisu_den,$datum_podpisu_mesic,$datum_podpisu_rok) = split("\.",$datum_podpisu);
	$datum_podpisu=$datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
    }

    if( (strlen($billing_freq) <> 1) )
    {
	$billing_freq = 0;
    }
    							   
     $vlast_upd = array( "nick" => $nick2, "jmeno" => $jmeno, "prijmeni" => $prijmeni, "ulice" => $ulice, "mesto" => $mesto, "psc" => $psc,
			 "vs" => $vs, "k_platbe" => $k_platbe, "archiv" => $archiv, "fakturacni_skupina_id" => $fakt_skupina,
			 "splatnost" => $splatnost, "trvani_do" => $trvani_do, "sluzba_int" => $sluzba_int,
			 "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
			 "billing_freq" => $billing_freq );
    
    if ( (strlen($firma) > 0) ){ $vlast_upd["firma"]=$firma; } else{ $vlast_upd["firma"]=NULL; } // u firmy else musi byt
    if ( (strlen($email) > 0) ){ $vlast_upd["mail"]=$email; } else{ $vlast_upd["mail"]=NULL; }
    if ( $icq > 0 ){ $vlast_upd["icq"]=$icq; } else{ $vlast_upd["icq"]=""; }
    if ( (strlen($tel) > 0) ){ $vlast_upd["telefon"]=$tel; }else{ $vlast_upd["telefon"]=NULL; }
    if ( $ucetni_index > 0 ){ $vlast_upd["ucetni_index"]=$ucetni_index; } else{ $vlast_upd["ucetni_index"]=""; }
    //if ( (strlen($poznamka) > 0 ) )
    { $vlast_upd["poznamka"]=$poznamka; }
    if ( (strlen($datum_podpisu) > 0 ) ){ $vlast_upd["datum_podpisu"]=$datum_podpisu; }else{ $vlast_upd["datum_podpisu"]=NULL; }
    if ( (strlen($typ_smlouvy) > 0 ) ){ $vlast_upd["typ_smlouvy"]=$typ_smlouvy; }else{ $vlast_upd["typ_smlouvy"]=0; }

    if( $sluzba_int == 1 ){ $vlast_upd["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu; }
    if( $sluzba_iptv == 1 ){ $vlast_upd["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu; }

    if($billing_suspend_status == 1)
    {
	    $vlast_upd["billing_suspend_status"] = intval($billing_suspend_status);
	    $vlast_upd["billing_suspend_reason"] = $conn_mysql->real_escape_string($billing_suspend_reason);
	    
	    list($b_s_s_den,$b_s_s_mesic,$b_s_s_rok) = split("\.",$billing_suspend_start);
	    $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

	    list($b_s_t_den,$b_s_t_mesic,$b_s_t_rok) = split("\.",$billing_suspend_stop);
	    $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;
	    
	    $vlast_upd["billing_suspend_start"]  = $conn_mysql->real_escape_string($billing_suspend_start);    
	    $vlast_upd["billing_suspend_stop"]   = $conn_mysql->real_escape_string($billing_suspend_stop);
    }
    else
    {
	    $vlast_upd["billing_suspend_status"] = 0;
	    $vlast_upd["billing_suspend_reason"] = NULL;
	    $vlast_upd["billing_suspend_start"]  = NULL;
	    $vlast_upd["billing_suspend_stop"] = NULL;
    }
     
    $vlast_id = array( "id_cloveka" => $update_id ); 		 	  
    $res = pg_update($db_ok2, 'vlastnici', $vlast_upd, $vlast_id);

     if($res){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
     else 
     { 
      echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n";
      echo pg_last_error($db_ok2); 
      
       $res1 = pg_get_result($db_ok2);
         echo pg_result_error($res1); 
      
      echo pg_last_notice($db_ok2);
      
     }
     
     require("vlastnici2-change-archiv-zmen-inc.php");
          
     $updated="true";
    }
    else
    {
    // rezim pridani

    if( (strlen($trvani_do) > 0) )
    {
      list($trvani_do_den,$trvani_do_mesic,$trvani_do_rok) = preg_split("/\./",$trvani_do);
      $trvani_do=$trvani_do_rok."-".$trvani_do_mesic."-".$trvani_do_den;
    }

    if( (strlen($datum_podpisu) > 0) )
    {
      list($datum_podpisu_den,$datum_podpisu_mesic,$datum_podpisu_rok) = preg_split("/\./",$datum_podpisu);
      $datum_podpisu=$datum_podpisu_rok."-".$datum_podpisu_mesic."-".$datum_podpisu_den;
    }
    
    
	$vlastnik_add = array( "nick" => $nick2 ,  "vs" => $vs, "k_platbe" => $k_platbe,
                    "jmeno" => $jmeno, "prijmeni" => $prijmeni, "ulice" => $ulice,
		    "mesto" => $mesto, "psc" => $psc, "ucetni_index" => $ucetni_index,
		    "fakturacni_skupina_id" => $fakt_skupina, "splatnost" => $splatnost,
		    "typ_smlouvy" => $typ_smlouvy, "sluzba_int" => $sluzba_int,
			 "sluzba_iptv" => $sluzba_iptv, "sluzba_voip" => $sluzba_voip,
			 "billing_freq" => $billing_freq );

	if ( (strlen($firma) > 0) ){ $vlastnik_add["firma"]=$firma; }
	if ( (strlen($email) > 0) ){ $vlastnik_add["mail"]=$email; }
	if ( $icq > 0 ){ $vlastnik_add["icq"]=$icq; }
	if ( (strlen($tel) > 0) ){ $vlastnik_add["telefon"]=$tel; }
	if ( $ucetni_index > 0 ){ $vlastnik_add["ucetni_index"]=$ucetni_index; }
	if ( (strlen($poznamka) > 0) ){ $vlastnik_add["poznamka"]=$poznamka; }	
	if ( (strlen($trvani_do) > 0) ){ $vlastnik_add["trvani_do"]=$trvani_do; }
        if ( (strlen($datum_podpisu) > 0 ) ){ $vlastnik_add["datum_podpisu"]=$datum_podpisu; }

	if( $sluzba_int == 1){ $vlast_add["sluzba_int_id_tarifu"] = $sluzba_int_id_tarifu; }
	if( $sluzba_iptv == 1){ $vlast_add["sluzba_iptv_id_tarifu"] = $sluzba_iptv_id_tarifu; }

	if($billing_suspend_status == 1)
	{
	    $vlastnik_add["billing_suspend_status"] = intval($billing_suspend_status);
	    $vlastnik_add["billing_suspend_reason"] = $conn_mysql->real_escape_string($billing_suspend_reason);
	    
	    list($b_s_s_den,$b_s_s_mesic,$b_s_s_rok) = preg_split("/\./",$billing_suspend_start);
	    $billing_suspend_start = $b_s_s_rok."-".$b_s_s_mesic."-".$b_s_s_den;

	    list($b_s_t_den,$b_s_t_mesic,$b_s_t_rok) = preg_split("/\./",$billing_suspend_stop);
	    $billing_suspend_stop = $b_s_t_rok."-".$b_s_t_mesic."-".$b_s_t_den;
	    
	    $vlastnik_add["billing_suspend_start"] = $conn_mysql->real_escape_string($billing_suspend_start);    
	    $vlastnik_addd["billing_suspend_stop"] = $conn_mysql->real_escape_string($billing_suspend_stop);
	}

        $res=pg_insert($db_ok2,'vlastnici', $vlastnik_add);
    
        if($res) { echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze vlastníků. </div></H3>\n"; } 
        else
        { 
    	    echo "<div style=\"color: red; \">Chyba! Data do databáze vlastníků nelze uložit. </div>".pg_last_error($db_ok2)."<br>\n"; 
        }	
	
     // pridame to do archivu zmen
     $pole="<b>akce: pridani vlastnika ; </b><br>";
    
    foreach($vlastnik_add as $key => $val)
    { $pole=$pole." [".$key."] => ".$val."\n"; }
        
    if ( $res == 1){ $vysledek_write=1; }
    else{
      $vysledek_write=0;
    }

    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$nick','$vysledek_write')");
     
    $writed = "true"; 
    
    // konec else - rezim pridani
    }

}else{} // konec else ( !(isset(fail) ), else tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

elseif ( isset($send) ): 
  $error = "<h4>Chybí povinné údaje !!! ( aktuálně jsou povinné:  nick, vs, k platbě, Fakturační skupina ) </H4>"; 
endif; 

if ($update_status==1){ echo '<h3 align="center">Úprava vlastníka</h3>'; } 
else { echo '<h3 align="center">Přidání nového vlastníka</h3>'; }

// jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
if ( (isset($error)) or (!isset($send)) ): 
echo $error; 

// vlozeni vlastniho formu
 require("vlastnici2-change-inc.php");

elseif ( ( isset($writed) or isset($updated) ) ):

$back=pg_query("SELECT * FROM vlastnici WHERE nick LIKE '$nick2' ");
$back_radku=pg_num_rows($back);

while ( $data_back=pg_fetch_array($back) ){ $firma_back=$data_back["firma"]; $archiv_back=$data_back["archiv"]; }

if ( $archiv_back == 1 ){ $stranka="vlastnici-archiv.php"; }
elseif ( $firma_back == 1 ){ $stranka="vlastnici2.php"; }
else { $stranka="vlastnici.php"; }

?>

<table border="0" width="50%" >
    <tr>
     <td align="right">Zpět na vlastníka </td>
     <td><form <?php echo 'action="'.$stranka.'"'; ?> method="GET" >
       <input type="hidden" <?php echo "value=\"".$nick2."\""; ?> name="find" >
     <input type="submit" value="ZDE" name="odeslat" > </form></td>

     <td align="right">Restart (all iptables ) </td>
     <td><form action="work.php" method="POST" ><input type="hidden" name="iptables" value="1" >
        <input type="submit" value="ZDE" name="odeslat" > </form> </td>
    </tr>
</table>

<br>
<?php

if ( $firma_back == 1 )
{
 echo "<div style=\"padding-top: 10px; padding-bottom: 20px; font-size: 18px; \">
    <span style=\"font-weight: bold; \">Upozornění!</span> Změny je nutné dát vědet účetní. </div>";
}
?>

Objekt byl přidán/upraven , zadané údaje:<br><br> 
<b>Nick</b>: <?php echo $nick2; ?> <br> 
<b>VS</b>: <?php echo $vs; ?> <br> 
<b>K_platbě</b>: <?php echo $k_platbe; ?> <br>

<br>

<b>Jméno</b>: <?php echo $jmeno; ?> <br>
<b>Příjmení</b>: <?php echo $prijmeni; ?> <br>
<b>Ulice</b>: <?php echo $ulice; ?><br>
<b>PSČ</b>: <?php echo $psc; ?><br>

<br>

<b>e-mail</b>: <?php echo $email; ?><br>
<b>icq</b>: <?php echo $icq; ?><br>
<b>telefon</b>: <?php echo $tel; ?><br> 
<br>

<b>firma</b>: 

<?php

if( $firma == 1 ){ echo "Vlastníci2 - Simelon, s.r.o."; }
else{ echo "Vlastníci - Fyzická"; } 

echo "<br>";
echo "<b>Archivovat: </b>";

if( $archiv == 1 ){ echo " Ano "; }
else{ echo " Ne "; } 

echo "<br><b>Fakturační skupina: </b> ".$fakt_skupina."<br>";

?>

<b>Typ smlouvy:</b>: 
  <?php

  if( $typ_smlouvy == 0 ){ echo "[nezvoleno]"; }
  elseif( $typ_smlouvy == 1 ){ echo "[na dobu neurčitou]"; }
  elseif( $typ_smlouvy == 2 )
  {
   echo "[na dobu určitou]";		       
   echo " ( doba trvání do: ";

   list($trvani_do_rok,$trvani_do_mesic,$trvani_do_den) = explode("-",$trvani_do);								       
   $trvani_do=$trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
								   
   echo $trvani_do." )";
  }
  else{ echo "[nelze zjistit]"; }
  
  ?><br>

<b>Datum podpisu</b>: 
<?php

 if( (strlen($datum_podpisu) > 0) )
 {
  list($datum_podpisu_rok,$datum_podpisu_mesic,$datum_podpisu_den) = explode("-",$datum_podpisu);
  $datum_podpisu=$datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
 }

echo $datum_podpisu; 
?> <br>

<br>

<b>Služba Internet:</b>
<?php
  if( $sluzba_inet == 0 ){ echo "Ne"; }
  elseif( $sluzba_inet == 1 ){ echo "Ano"; }
  else
  { echo "Nelze zjistit - hodnota: ".$sluzba_inet; }
  
?>
<br>

<b>Služba IPTV:</b>
<?php
  if( $sluzba_iptv == 0 ){ echo "Ne"; }
  elseif( $sluzba_iptv == 1 ){ echo "Ano"; }
  else
  { echo "Nelze zjistit - hodnota: ".$sluzba_iptv; }
  
?>
<br>

<b>Služba VoIP:</b>
<?php
  if( $sluzba_voip == 0 ){ echo "Ne"; }
  elseif( $sluzba_voip == 1 ){ echo "Ano"; }
  else
  { echo "Nelze zjistit - hodnota: ".$sluzba_voip; }
  
?>
<br><br>

<b>Pozastavené fakturace:</b>
<?php 

    if( $billing_suspend_status == 1)
    { echo "Ano"; }
    else
    { echo "Ne"; }
    
    echo "<br>";
    
    if( $billing_suspend_status == 1)
    {
        list($b_s_s_rok,$b_s_s_mesic,$b_s_s_den) = explode("-",$billing_suspend_start);
        $billing_suspend_start = $b_s_s_den.".".$b_s_s_mesic.".".$b_s_s_rok;

        list($b_s_t_rok,$b_s_t_mesic,$b_s_t_den) = explode("-",$billing_suspend_stop);
        $billing_suspend_stop = $b_s_t_den.".".$b_s_t_mesic.".".$b_s_t_rok;

        echo "<b>od kdy</b>: ".$billing_suspend_start."<br>\n";
        echo "<b>do kdy</b>: ".$billing_suspend_stop."<br>\n";
        
        echo "<b>důvod</b>: ".$billing_suspend_reason."<br>\n";
    }
?>
<br>

<br><br>
																			      
<?php endif; ?> 

 </td>
  </tr>
  
 </table>

</body> 
</html> 
