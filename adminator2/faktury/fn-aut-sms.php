<?php 

set_time_limit(0);

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 
require_once($cesta."include/check_login.php");
require_once($cesta."include/check_level.php");

if( ($_GET["no_login"] != "yes" ) )
{
  if ( !( check_level($level,110) ) ) 
  {
   $stranka=$cesta.'nolevelpage.php'; 
   header("Location: ".$stranka);
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";  
   exit;
  }
}
 
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ($cesta."include/charset.php"); 

?>

<title>Adminator2 - Neuhrazené faktury </title> 

</head> 

<body> 

<?php require($cesta."head.php"); ?> 

<?php require($cesta."category.php"); ?> 

<tr>
  <td colspan="2" height="20" bgcolor="silver" ><?php require($cesta."fn-cat.php"); ?> </td>
</tr>
    
<tr>
  <td colspan="2">
    <!-- zacatek vlastniho obsahu -->

<?php

echo "<div style=\"font-size: 20px; font-weight: bold; padding-top: 20px; padding-bottom: 20px; \" >
	Automatické odesílání SMS o neuhrazených fakturách </div>";

$odeslano=$_GET["odeslano"];
$typ=$_GET["typ"];

if ( !isset($odeslano) )
{
  echo "<form action=\"\" method=\"GET\" >";

    echo "<div >Opravdu odeslat SMS o neuhrazených fakturách ? </div>";

    echo "<div style=\"padding-top: 20px; \" >
	    <span style=\"font-weight: bold; margin-right: 20px; \">Režim odesílání:</span>
    	    <select size=\"1\" name=\"typ\" >
		<option value=\"0\" class=\"fn-select-nevybrano\" >Nevybráno</option>
		<option value=\"1\" >Odeslat všem, kterým se ještě SMS o neuhr. fa. neodesílala</option>
	    </select>	    
	  </div>";

  echo "<div style=\"padding-top: 20px; padding-left: 120px; \" >
      
    <input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>";

  echo "</form>";
   
} // konec if !isset odeslano
elseif( $typ == 0)
{

    echo "<div style=\"padding-top: 20px; \" >Musíte vybrat nějakou možnost! </div>";

    echo "<div style=\"padding-top: 20px; \" ><a href=\"fn-aut-sms.php\" >Zpět</a></div>";

}
else
{

  //normal dotaz 
 $dotaz=$conn_mysql->query("SELECT *,DATE_FORMAT(Datum, '%m-%Y') as datum2 FROM faktury_neuhrazene WHERE 
			( ignorovat = '0' AND par_id_vlastnika > 0 AND aut_sms_stav = 0 AND po_splatnosti_vlastnik = '1' ) ");	
  

// $dotaz=$conn_mysql->query("SELECT * FROM faktury_neuhrazene WHERE 
//			( ignorovat = '0' AND par_id_vlastnika > 0 and po_splatnosti_vlastnik = '1' ) ");

 $dotaz_radku= $dotaz->num_rows;
 
 if ( $dotaz_radku == 0)
 { echo "<div>Žádné neuhrazené faktury v databázi.</div>"; }
 else
 {

  while( $data= $dotaz->fetch_array() )
  {
      $id_cloveka=$data["par_id_vlastnika"];
      $id_faktury=$data["id"];
      
      $odesilat="ano";
    
      //zjistime vlastnika
      $dotaz_vlastnik=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
      $dotaz_vlastnik_radku=pg_num_rows($dotaz_vlastnik);
      
      if( $dotaz_vlastnik_radku == 1)
      {
        while( ( $data_vlastnik=pg_fetch_array($dotaz_vlastnik) ) )
        { $vlastnik_tel=$data_vlastnik["telefon"]; } 
      }
      else
      { // nelze zjistit vlastnik   
        $aut_sms_stav=9; //nelze zjistit vlastnik
        $odesilat="ne";      
      } // konec else if dotaz_vlastniku_radku <> 1
    
     // test jestli je cislo spravne udane
     if ( (strlen($vlastnik_tel) == 9) )
     { }
     elseif( (strlen($vlastnik_tel) == 13) )
     { }
     else
     {
      $aut_sms_stav=8; // tel cislo neni ve spravnem formatu
      
      $odesilat="ne";
     }
    
    if( $odesilat == "ano" )
    {
     $text = "Dobry den. Nemate uhrazenou fakturu c. ".$data["Cislo"]." v obdobi ".$data["datum2"].". ";
     $text .= "Prosim uhradte ji co nejdrive, aby nedoslo k omezeni sluzeb. SIMELON, s.r.o.";
     
     $vlastnik_sms_send=$conn_mysql->query("insert into QUEUE (PHONE, MSG,SCRIPT) VALUES ('$vlastnik_tel', '$text','Mobilem') "); 
     
     sleep(2);
     
     $last_id=mysql_insert_id();
     $dotaz_se=$conn_mysql->query("SELECT * FROM QUEUE WHERE ID = '$last_id' ");
     
      while( $data_se=mysql_fetch_array($dotaz_se) )
      { $last_status=$data_se["STATUS"]; } 

    //echo "test 1<br>";
    
    if( $last_status == 1 ){ $aut_sms_stav = 1; } // uspesne odeslano
    elseif( $last_status == 0){ $aut_sms_stav = 2; } // nelze odeslat, chyba pri odesilani
    else{ } //ted este nevim
    
    //zde to vypisem
     echo "<div style=\"\">
	    <span style=\"padding-left: 10px; padding-right: 10px; \">číslo faktury: ".$data["Cislo"]."</span>	    
	    <span style=\"padding-left: 10px; padding-right: 10px; \">tel číslo: ".$vlastnik_tel."</span>
	    <span style=\"padding-left: 10px; padding-right: 10px; \">aut sms stav: ".$aut_sms_stav."</span>	
	    <span style=\"padding-left: 10px; padding-right: 10px; \">par id_vlastnika: ".$id_cloveka."</span>
	    <div style=\"padding-left: 10px; padding-right: 10px; color: grey; \">text sms: ".$text."</div>";

    }
    else
    {
    // pouze vypis
         echo "<div style=\"font-weight: bold; \">
	    <span style=\"padding-left: 10px; padding-right: 10px; \">číslo faktury: ".$data["Cislo"]."</span>	    
	    <span style=\"padding-left: 10px; padding-right: 10px; \">tel číslo: ".$vlastnik_tel."</span>
	    <span style=\"padding-left: 10px; padding-right: 10px; \">aut sms stav: ".$aut_sms_stav."</span>
	
	    <span style=\"padding-left: 10px; padding-right: 10px; \">par id_vlastnika: ".$id_cloveka."</span>
	    <div style=\"padding-left: 10px; padding-right: 10px; color: grey; \">neposílala se sms</div>";
    }
        
    //zde ulozime do db vysledky
    $uprava_zpetna=$conn_mysql->query("UPDATE faktury_neuhrazene SET aut_sms_stav='$aut_sms_stav', aut_sms_datum=Now() WHERE id=".$id_faktury." Limit 1 ");
    
//  if ($odesilat == "ano" )
    {
     echo "<div style=\"padding-left: 10px; padding-right: 10px; color: grey; \">debug: last_id: ".$last_id." aut_sms_stav: ";
     echo $aut_sms_stav.", vlastnik_sms_send: ".$vlastnik_sms_send.", uprava_zpetna: ".$uprava_zpetna.", id: ".$id_faktury;
     
     if( !(is_int($aut_sms_stav) ) ){ echo ", aut_sms_stav wrong type"; }
     
     echo ", mysql result: ".mysql_error();
        echo "</div>";

     echo "</div>";
    }
    
    //anulace promennych
    
    $aut_sms_stav="";
    
    $vlastnik_tel="";
      
    $last_status="";
    
    $vysl="";
    
    $text="";
    
  } // konec while
 
 } // konec else if dotaz_radku == 0

} // konec else ! isset odeslano

?>

   <!-- konec vlastniho obsahu -->
 </td>
  </tr>
  
 </table>

</body> 
</html> 
