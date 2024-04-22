<?php

require("include/main.function.shared.php");
require_once("include/config.php");
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,50) ) )
{
 // neni level

 $stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html>
      <head> ';

include ("include/charset.php");

?>

<title>Adminator2 - platby</title>

<?php

 require("include/js.include.1.php");

 $windowtext = "Aktuální informace o platbách má účetní ( Ing. Alice Sekyrová, tel: 602 411 970 ). Zde jsou zpožděné informace/platby. ";

 // velikost okna
 $windowdelka = 300;
 $windowpadding = 40;

 // pozice okna
 $windowtop = 370;
 $windowleft = 350;

 require("include/window-main.php");
 
 ?>
  
</head> 

<body onload="showWindow()" > 

<?php require("head.php"); ?> 

<?php require("category.php"); ?> 

 <tr>
    <td colspan="2" ><?php require("platby-subcat-inc2.php"); ?></td>
 </tr>

 <tr>
  <td colspan="2">
  
  <?php
   // sem zbytek
    
    $id_vlastnika=$_GET["id_vlastnika"];
    $rok=$_GET["rok"];
    
    if( (strlen($rok) < 1) ){ $rok = strftime("%Y", time()); }
    
    $mesic_akt = strftime("%m", time());
    $rok_akt = strftime("%Y", time());

    $den_akt = strftime("%d", time());

  
  if( isset($id_vlastnika) )
  { 
    $id_check=ereg('^([[:digit:]]+)$',$id_vlastnika);
    
    if( !($id_check) )
    { 
	echo "Chyba! Vstupní data nejsou ve správném formátu! "; 
	exit;
    }
    		 
    $dotaz=pg_query("SELECT * FROM platby 
			WHERE ( id_cloveka='".intval($id_vlastnika)."' and zaplaceno_za LIKE '".intval($rok)."%') ");
    $dotaz_radku=pg_num_rows($dotaz);
    
	echo "<table border=\"0\" width=\"90%\" >";
	echo "<tr>";
	
	    echo "<td valign=\"top\" colspan=\"3\" width=\"25%\" align=\"\" >
	    <br><div style=\"font-size: 16px;  \">Výpis plateb za rok: <b>$rok</b> </div></td>";
	
	    echo "<td rowspan=\"3\" width=\"75%\" valign=\"top\" >";
	    //sem info o vlastnikovi
	    
	    $dotaz_vlastnik=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_vlastnika)."' ");
	    $dotaz_vlastnik_radku=pg_num_rows($dotaz_vlastnik);
	    
	    if ( $dotaz_vlastnik_radku <> 1)
	    { echo "Chyba! Nelze zjistit informace o vlastnikovi! "; }
	    else
	    {
		while($data_vlastnik=pg_fetch_array($dotaz_vlastnik))
		{
		 echo "<div style=\"font-size: 12px; padding-bottom: 10px; \">Informace o vlastníkovi: </div>";
		
		 echo "<span style=\"color: grey; \">";
		
		 echo $data_vlastnik["jmeno"]." ".$data_vlastnik["prijmeni"];
		 echo "<br>".$data_vlastnik["ulice"].", ".$data_vlastnik["mesto"]." ".$data_vlastnik["psc"];
		
		 echo "<br>id: [".$data_vlastnik["id_cloveka"]."], účetní index: ".$data_vlastnik["ucetni_index"];
		 echo "<br> vs: ".$data_vlastnik["vs"];
	    
		 echo "</span>";
		}
	    }
	    echo "</td>";
	    
	    echo "</tr>";
	    
	    echo "<tr><td colspan=\"3\"><br></td></tr>";
	
	    echo "<tr>";
	    
	    echo "<td>Zvolte rok: </td>";
	    
	    echo "<td>
	    	    
	    <form action=\"\" method=\"GET\" >
		<select name=\"rok\" size=\"1\">";
			
	    for($i = "2006"; $i <= date("Y"); $i++){
		echo "<option value=\"".$i."\" "; if($rok == $i) echo "selected"; echo " >".$i."</option>\n";
	    }
	    
	    echo "</select>
		
		<input type=\"hidden\" name=\"id_vlastnika\" value=\"".$id_vlastnika."\" >
		
		</td>
		<td>
		<input type=\"submit\" value=\"OK\" name=\"OK\" >
	    
	    </td>";
	    
	echo "</tr>";    
	
	echo "<tr><td colspan=\"4\" ><hr width=\"100%\" align=\"left\" ></td></tr>\n";
	
	echo "</table>\n";
	
	echo "<br>\n\n";
	
        // zde konci form pro vyber roku

	// zjitovani starich verzi, plus dokreslovani dalsich plateb 
	if ( $rok < "2007" )
	{ 
	    echo "<br><div style=\"font-size: 18px; color: teal; padding-bottom: 10px; \" >
	    V tomto roce jsou platby informativní, systém \"Adminator2 - platby\" nebyl v provozu. </div>\n"; 
	}	
          
  // tady zjistime jestli je clovek na s.r.o.
  
  $dotaz_sro=pg_query("SELECT * FROM vlastnici WHERE id_cloveka='".intval($id_vlastnika)."' ");
  $dotaz_sro_radku=pg_num_rows($dotaz_sro);
  
  $firma = "0";
  
  if ( $dotaz_sro_radku > 0 )
  {
   while( $data_sro=pg_fetch_array($dotaz_sro) ):
   
   if ( $data_sro["firma"] == 1 )
   { 
    
   echo "<table border=\"0\" width=\"\" ><tr>\n";
    echo "<td valign=\"center\" > <div style=\"font-size: 18px; \"><p> Zákazník má smlouvu na Simelon, s.r.o., aktuální informace o platbách má účetní. </p></td>\n"; 
    echo "<td valign=\"top\"><span style=\"padding-left: 20px; \">
	 <input type=\"button\" name=\"OKK\" value=\" Zobrazit dodatečné info \" onclick=\"showWindow()\" >
	 </div></td>\n";
    
    echo "</tr></table>\n\n";
    
    $firma = "1";
   }
   
   
   endwhile;
 
  }
  else
  {
  echo "<p> Chyba! Sekundarni select vlasníka se neprovedl</p>\n";
  }	    

	
	// popis policek (:
	echo "<table border=\"0\" width=\"100%\" >\n";
	
	echo "<tr><td class=\"tab-vypis-plateb-first\"><b>id platby: </b></td>\n";
	echo "<td colspan=\"2\" class=\"tab-vypis-plateb-first\" ><b>zaplaceno dne: </b></td>\n";
	echo "<td class=\"tab-vypis-plateb-first\" ><b>částka: </b></td>\n";
	echo "<td colspan=\"2\" class=\"tab-vypis-plateb-first\"  ><b>zaplaceno za: </b></td>\n";
	echo "<td class=\"tab-vypis-plateb-first\" ><b>z výpisu: </b></td>\n";
	echo "<td colspan=\"6\" class=\"tab-vypis-plateb-first\"><b>hotově: </b></td>\n";
	
	echo "</tr>\n\n";
    
	echo "<tr>\n";

	if($firma == 1)
	{
	
	echo "<td colspan=\"12\"  >
	<span style=\"color: #CC3333; font-weight: bold; \" >Detailní informace o neuhrazených fakturách</span>
	</td>
	
	</tr>
	<tr>
	
	<td colspan=\"12\" class=\"tab-vypis-plateb-second\" >
	    <table border=\"0\" width=\"100%\" cellspacing=\"2\" >
		<tr>
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Číslo faktury</span></td>
		
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Variabilní symbol: </span></td>
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Datum vytvoření f.: </span></td>
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Datum splatnosti f.: </span></td>
		
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Firma: </span></td>
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Jméno: </span></td>
		
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">IČO: </span></td>
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">DIČ: </span></td>
		
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Celková<br> částka: </span></td>
		
		<td bgcolor=\"#CC3333\" class=\"tab-platby-vypis-2\" ><span style=\"color: white; \">Neuhrazená<br> částka: </span></td>
		
		</tr>
	    </table>
	    
	</td>\n";
	}	
	else
	{
	 echo "<td colspan=\"12\" bgcolor=\"yellow\" class=\"tab-vypis-plateb-second\"><b>
	 detailní údaje z bankovního účtu ( datum připsání, protiúčet, ... )</b></td>\n";
	
	}
	echo "</tr>\n";
	
	echo "<tr><td colspan=\"12\"><br></td></tr>\n\n";
    
    if ( $firma != 1)
    { // platby na F.O.
    
	// while nahradime forem
	
	for($i=1;$i <= 12;$i++)
	{    
	
	 echo "<tr>";
	 
	 if ( $i < 10){ $pozadovane_obdobi = $rok."-"."0".$i; }
	 else { $pozadovane_obdobi = $rok."-".$i; }
	
	 $mesic_long = "0".$i;
	 
	$dotaz_platby=pg_query("SELECT * FROM platby WHERE ( id_cloveka='$id_vlastnika' and zaplaceno_za LIKE '$pozadovane_obdobi') ");
	$dotaz_platby_radku=pg_num_rows($dotaz_platby);

        // jestli platba je ci neni
	 
	 $dotaz_pridani=pg_query("SELECT * FROM vlastnici WHERE ( id_cloveka='$id_vlastnika' ) ");
	 $dotaz_pridani_radku=pg_num_rows($dotaz_pridani);
        
	 while( $data_pridani=pg_fetch_array($dotaz_pridani)){ $datum_pridani = $data_pridani["pridano"]; }
	 
	 list($a,$b) =explode (" ", $datum_pridani);
	 
	 list($rok_pridani,$mesic_pridani,$e) =explode ("-", $a);
	 $datum_pridani = $rok_pridani."-".$mesic_pridani;
 	
        if ( $dotaz_platby_radku > 1 )
	{ echo "<p> Chyba! Nalezeno vice plateb k jednomu platebnimu obdobi! </p>"; }
	elseif ( ( ($rok_pridani > $rok) and  ($rok < 2007) ) )
	{ 
	   echo "<td colspan=\"12\" class=\"tab-vypis-plateb-horni\" ><span style=\"color: grey; font-weight: bold; \">
	   V tomto období (roce) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif ( ( ($dotaz_platby_radku < 1) and ( $rok < 2007) ) )
	{ 
	    echo "<td colspan=\"12\" class=\"tab-vypis-plateb-horni\" >";
	    
	    if ($firma == 1 ){ echo "<span style=\"color: grey; font-weight: bold; \" >"; }
	    else { echo "<span style=\"color: grey; font-weight: bold; \" >"; }
	    
	    echo " Platba za období $pozadovane_obdobi nenalezena! </td></span>\n"; 
	}
	elseif( ($dotaz_platby_radku < 1) and ( $rok == $rok_akt ) and ( $mesic_long >= $mesic_akt ) )
	{ echo "<td colspan=\"12\" bgcolor=\"silver\" > Účtování za období $pozadovane_obdobi ještě neproběhlo! </td>\n"; }
	elseif( ($dotaz_platby_radku < 1) and ( $rok > $rok_akt ) )
	{ echo "<td colspan=\"12\" bgcolor=\"silver\" > Účtování za období $pozadovane_obdobi ještě neproběhlo! </td>\n"; }
	elseif( ($dotaz_platby_radku < 1) )
	{
	 // bud  nejsou zadny platby nebo jenom tuta
	 $dotaz_platby_sec=pg_query("SELECT * FROM platby WHERE ( zaplaceno_za LIKE '$pozadovane_obdobi' and hotove is NULL ) ");
	 $dotaz_platby_sec_radku=pg_num_rows($dotaz_platby_sec);
	 
         if ( $dotaz_platby_sec_radku < 50 )
	 {
	   if ( $firma == 1){ echo "<td colspan=\"12\" bgcolor=\"\" class=\"tab-vypis-plateb-horni\" >"; }
	   else { echo "<td colspan=\"12\" bgcolor=\"fuchsia\" class=\"tab-vypis-plateb-horni\" >"; }
	  
	   echo "Platbu za období $pozadovane_obdobi nelze ověřit, nejsou naimportované výpisy! </td>\n"; 
	 }
	elseif( ( ( $rok_pridani == $rok ) and ( $mesic_pridani > $mesic_long) ) )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#339999\" ><span style=\"color: white; \">
	  V tomto období ( $pozadovane_obdobi ) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif ( $rok_pridani > $rok )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#339999\" class=\"tab-vypis-plateb-horni\" ><span style=\"color: white; \">
	  V tomto období ( $pozadovane_obdobi ) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif ( $datum_pridani == $pozadovane_obdobi )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#D2B48C\" > Platba za toho období/měsíc není povinná ! </td>\n"; 
	}
	else
	{
	  if ( $firma == 1){ echo "<td colspan=\"12\" bgcolor=\"\" class=\"tab-vypis-plateb-horni\" >"; }
	  else { echo "<td colspan=\"12\" bgcolor=\"#FF6666\" class=\"tab-vypis-plateb-horni\" >"; }
	  
	  echo "<span style=\"color: \">
	  Platba za období $pozadovane_obdobi nenalezena! </span></td>\n"; 
	}

 
	} // konec elseif ( $dotaz_platby_radku < 1 )
	else
	{
	    while ( $data=pg_fetch_array($dotaz_platby) ):
	
		echo "<td colspan=\"2\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 12px; \">".$data["id"]."</span></td>\n";
	
		$orezano = split(':', $data["zaplaceno_dne"]);
		    
		echo "<td colspan=\"1\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 echo $orezano[0].":".$orezano[1]."</span></td>\n";
		      
		echo "<td colspan=\"1\" class=\"tab-vypis-plateb-horni\"><span style=\"font-size: 14px; \">";
		 echo $data["castka"]."</span></td>\n";
	  
		echo "<td colspan=\"2\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 echo $data["zaplaceno_za"]."</span></td>\n";

		echo "<td class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 if ( $data["zvypisu"] == "t" ){ echo " Ano "; }else { echo " Ne "; }
		echo "</span></td>\n";
		
		echo "<td class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 if ( $data["hotove"] == 1 ){ echo " Ano </td>"; }else { echo " Ne "; }
		echo "</span></td>\n";
		
		//echo "<td>";
		echo "</tr>\n\n";
		echo "<tr><td colspan=\"12\" class=\"\" >\n";
		
		$id=$data["id"];
	
		$dotaz_polozky=pg_query("SELECT * FROM platby_polozky WHERE id_platby = '$id' ");
		
		while( $data_polozky=pg_fetch_array($dotaz_polozky) ):

			echo "<table border=\"0\" width=\"100%\" >\n\n";	    
			echo "<tr>\n";
		
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["datum"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole2"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["ucet"]."</span></td>\n";	
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["castka"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["mena"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\"><span style=\"font-size: 12px; \"> ".$data_polozky["pole6"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\"><span style=\"font-size: 12px; \"> ".$data_polozky["pozn"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["jmeno"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole10"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole11"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["vs2"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["id_polozky"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["vs"]."</span></td>\n";
		
			echo "</tr>\n";
	    
			echo "</table>\n\n";
			
		    endwhile;
	
		 echo "</td>\n";	
		 echo "</tr>\n";
	
	    endwhile;
	
	  } // konec else kolik radku ma platba
	  
	} // konec foru asi
	
    } // konec if firma != 1
    else
    {
    //platby pro klienty sro
    
    // while nahradime forem
	
	for($i=1;$i <= 12;$i++)
	{    
	
	 echo "<tr>\n";
	 
	 if ( $i < 10){ $pozadovane_obdobi = $rok."-"."0".$i; }
	 else { $pozadovane_obdobi = $rok."-".$i; }
	
	 $mesic_long = "0".$i;
	 
	$dotaz_platby=pg_query("SELECT * FROM platby WHERE ( id_cloveka='$id_vlastnika' and zaplaceno_za LIKE '$pozadovane_obdobi') ");
	$dotaz_platby_radku=pg_num_rows($dotaz_platby);

        // zjisteni datumu pridani vlastnika do systemu
	 $dotaz_pridani=pg_query("SELECT * FROM vlastnici WHERE ( id_cloveka='$id_vlastnika' ) ");
	 $dotaz_pridani_radku=pg_num_rows($dotaz_pridani);
        
	 while( $data_pridani=pg_fetch_array($dotaz_pridani))
	 { $datum_pridani = $data_pridani["pridano"]; $splatnost_klient=$data_pridani["splatnost"]; }
	 
	 list($a,$b) =explode (" ", $datum_pridani);
	 
	 list($rok_pridani,$mesic_pridani,$e) =explode ("-", $a);
	 $datum_pridani = $rok_pridani."-".$mesic_pridani;
 	
        if ( $dotaz_platby_radku > 1 )
	{ echo "<p> Chyba! Nalezeno vice plateb k jednomu platebnimu obdobi! </p>\n"; }
	elseif ( ( ($rok_pridani > $rok) and  ($rok < 2007) ) )
	{ 
	   echo "<td colspan=\"12\" class=\"tab-vypis-plateb-horni\" ><span style=\"color: grey; font-weight: bold; \">
	   V tomto období (roce) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif( ($dotaz_platby_radku < 1) and ( $rok == $rok_akt ) and ( $mesic_long > $mesic_akt ) )
	{ echo "<td colspan=\"12\" bgcolor=\"silver\" > Účtování za období $pozadovane_obdobi ještě neproběhlo! </td>\n"; }
	elseif( ($dotaz_platby_radku < 1) and ( $rok == $rok_akt ) and ( $mesic_long == $mesic_akt ) and ( $den_akt  <= $splatnost_klient ) )
	{ echo "<td colspan=\"12\" bgcolor=\"silver\" > Faktura za účtováné za období $pozadovane_obdobi ještě není po splatnosti! </td>\n"; }	
	elseif( ($dotaz_platby_radku < 1) and ( $rok > $rok_akt ) )
	{ echo "<td colspan=\"12\" bgcolor=\"silver\" > Účtování za období $pozadovane_obdobi ještě neproběhlo! </td>\n"; }
	elseif( ( ($dotaz_platby_radku < 1) and ($rok_pridani == $rok ) and ( $mesic_pridani > $mesic_long) ) )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#339999\" ><span style=\"color: white; \">
	  V tomto období ( $pozadovane_obdobi ) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif( ( ($dotaz_platby_radku < 1) and ( $rok_pridani > $rok ) ) )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#339999\" class=\"tab-vypis-plateb-horni\" ><span style=\"color: white; \">
	  V tomto období ( $pozadovane_obdobi ) platba nebyla účtována, zákazník ještě nebyl naším klientem! </span></td>\n"; 
	}
	elseif ( ( ( $dotaz_platby_radku < 1) and ( $datum_pridani == $pozadovane_obdobi ) ) )
	{ 
	  echo "<td colspan=\"12\" bgcolor=\"#D2B48C\" > Platba za toho období/měsíc není povinná ! </td>\n"; 
	}
	elseif ( ( ($dotaz_platby_radku < 1) and ( $rok < 2007) ) )
	{ 
	    echo "<td colspan=\"12\" class=\"tab-vypis-plateb-horni\" >";
	    
	    if ($firma == 1){ echo "<span style=\"color: grey; font-weight: bold; \" >"; }
	    else { echo "<span style=\"color: grey; font-weight: bold; \" >"; }
	    echo " Platba toto období ( $pozadovane_obdobi ) byla za provedena přes účet, popř. faktura nebyla vystavena.</td></span>\n"; 
	}
	elseif( $dotaz_platby_radku < 1 )
	{
	
	 //ze zjistime jestli neni zaznam v neuhrazenych fakturach
	 $dotaz_fn=mysql_query("SELECT * FROM faktury_neuhrazene WHERE ( par_id_vlastnika = '$id_vlastnika' 
				and ( EXTRACT(YEAR FROM Datum) = '$rok') and ( EXTRACT(MONTH FROM Datum) = '$mesic_long' ) ) ");
	 $dotaz_fn_radku=mysql_num_rows($dotaz_fn);
	 
	  if ($dotaz_fn_radku == 0)
	  {
	   
	    echo "<td colspan=\"12\" bgcolor=\"\" class=\"tab-vypis-plateb-horni\" >	  
		<span style=\"color: \">Platba toto období ( $pozadovane_obdobi ) byla provedena přes účet, popř. faktura nebyla vystavena. </span></td>\n"; 
	   	   
	  }
	  elseif ( $dotaz_fn_radku != 1 )
	  {
	  echo "<td colspan=\"12\" bgcolor=\"\" class=\"tab-vypis-plateb-horni\" >	  
		<span style=\"color: \">Chyba! Spatny pocet radku ve faktury_neuhrazene! </span></td>\n"; 
	  }
	  else
	  {
	  
	    while( $data_fn=mysql_fetch_array($dotaz_fn) )
	    {
		$datum_fn = $data_fn["Datum"];
		
		// list($rok_datum_fn,$mesic_datum_fn,$den_datum_fn) =explode ("-", $datum_fn);
		 
		  echo "<td colspan=\"12\" class=\"tab-vypis-plateb-horni\" style=\"color: #CC3333; font-weight: bold; \">
			Nalezena neuhrazená faktura za období: ".$pozadovane_obdobi.". ";
		  echo "</td>\n";
		
		echo "</tr>\n\n";
		echo "<tr><td colspan=\"12\" class=\"\" >\n";
		
			echo "\n<table border=\"0\" width=\"100%\" >\n\n\n";	    
			echo "<tr>\n";
		
			$barva1="#CC3333";
			$barva_pisma1="white";
			
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["Cislo"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["VarSym"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["Datum"]."</span></td>\n";	
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["DatSplat"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["Firma"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["Jmeno"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["ICO"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["DIC"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["KcCelkem"]."</span></td>\n";
			echo "<td bgcolor=\"".$barva1."\" ><span style=\"font-size: 12px; color: ".$barva_pisma1."; \"> ".$data_fn["KcLikv"]."</span></td>\n";
			// echo "<td bgcolor=\"".$barva."\" ><span style=\"font-size: 12px; \"> ".$data_fn["vs2"]."</span></td>\n";
			// echo "<td bgcolor=\"".$barva."\" ><span style=\"font-size: 12px; \"> ".$data_fn["id_polozky"]."</span></td>\n";
			// echo "<td bgcolor=\"".$barva."\" ><span style=\"font-size: 12px; \"> ".$data_fn["vs"]."</span></td>\n";
		
			echo "</tr>\n";
	    
			echo "</table>\n\n";
	
		 echo "</td>\n";	
		 echo "</tr>\n";
			
	    } // konec while
	
	  } // konec else dotaz_fn_radku == 0 
	  
	} // konec elseif dotaz_platby_radku < 1
	else
	{
	    while( $data=pg_fetch_array($dotaz_platby) ):
	
		echo "<td colspan=\"2\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 12px; \">".$data["id"]."</span></td>\n";
	
		$orezano = split(':', $data["zaplaceno_dne"]);
		    
		echo "<td colspan=\"1\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 echo $orezano[0].":".$orezano[1]."</span></td>\n";
		      
		echo "<td colspan=\"1\" class=\"tab-vypis-plateb-horni\"><span style=\"font-size: 14px; \">";
		 echo $data["castka"]."</span></td>\n";
	  
		echo "<td colspan=\"2\" class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 echo $data["zaplaceno_za"]."</span></td>\n";

		echo "<td class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 if ( $data["zvypisu"] == "t" ){ echo " Ano "; }else { echo " Ne "; }
		echo "</span></td>\n";
		
		echo "<td class=\"tab-vypis-plateb-horni\" ><span style=\"font-size: 14px; \">";
		 if ( $data["hotove"] == 1 ){ echo " Ano </td>"; }else { echo " Ne "; }
		echo "</span></td>\n";
		
		//echo "<td>";
		echo "</tr>\n\n";
		echo "<tr><td colspan=\"12\" class=\"\" >\n";
		
		$id=$data["id"];
	
		$dotaz_polozky=pg_query("SELECT * FROM platby_polozky WHERE id_platby = '$id' ");
		
		while( $data_polozky=pg_fetch_array($dotaz_polozky) ):

			echo "\n<table border=\"0\" width=\"100%\" >\n\n\n";	    
			echo "<tr>\n";
		
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["datum"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole2"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["ucet"]."</span></td>\n";	
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["castka"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["mena"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\"><span style=\"font-size: 12px; \"> ".$data_polozky["pole6"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\"><span style=\"font-size: 12px; \"> ".$data_polozky["pozn"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["jmeno"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole10"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["pole11"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["vs2"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["id_polozky"]."</span></td>\n";
			echo "<td bgcolor=\"yellow\" ><span style=\"font-size: 12px; \"> ".$data_polozky["vs"]."</span></td>\n";
		
			echo "</tr>\n";
	    
			echo "</table>\n\n";
			
		    endwhile;
	
		 echo "</td>\n";	
		 echo "</tr>\n";
	
	    endwhile;

	} // konec else

    } // konec foru
    
   } // konec else jestli se jedna o klienta sro	
	
	echo "</table>\n";
	
	// kvuli pop-up oknu
	if ( $firma == 1){ echo '<div id="windowPlaceholder"></div>'; }
	    
  } // konec else is set id_vlastnika
  else
  {
  // zobrazime form pro zvoleni vlastnika
  echo "dodelat";
  
  }
   
//  echo "</td></tr>\n</table>\n";
 
 echo "</form>\n";
 
  ?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>

