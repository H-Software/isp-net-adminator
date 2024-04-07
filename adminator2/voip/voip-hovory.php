<?php

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if( !( check_level($level,118) ) )
{
// neni level

 $stranka=$cesta.'nolevelpage.php';
 header("Location: ".$stranka);
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - VoIP Hovory</title> 

</head> 

<body> 

<?php include ($cesta."head.php"); ?> 

<?php include ($cesta."category.php"); ?>
 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><?php include("voip-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
 <?php
 
  $item=$_GET["item"];
 
  if ( ( strlen($item) < 1) ){ $item=$_POST["item"]; }
 
  echo "<div style=\"padding-top: 10px; font-size: 18px; \">
	<span style=\" font-weight: bold;\" >Voip systém</span>
	<span style=\"\" > - Výpis hovorů </span>
       </div>";

 //include('../include/class.fileimporter.php'); 
 //include("../include/class.voip.main.php");
  
 if( $item == 1 )
 {

  $rok = $_GET["rok"];
  $mesic = $_GET["mesic"];
  $cislo = $_GET["cislo"];
  $typ_systemu = $_GET["typ_systemu"];
  
  if( (strlen($typ_systemu) < 1 ) ){ $typ_systemu = 2; }
  
  echo "<div style=\"padding-top: 0px; padding-left: 5px; padding-bottom: 20px; \">";
 
  //filtrace
    echo "
    <div style=\"padding-top: 20px; padding-bottom: 20px; \">
        <form action=\"\" method=\"GET\" name=\"form4\" >
        <input type=\"hidden\" name=\"item\" value=\"".$item."\" >

        <span style=\"padding-right: 10px; color: grey; font-weight: bold; \">filtr: </span>

        <span style=\"padding-right: 10px;\" >období (rok): </span>
        <span style=\"padding-right: 10px;\" >
          <select name=\"rok\" size=\"1\">
            <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>

            <option value=\"2008\" "; if($rok == 2008 ){ echo " selected "; } echo ">2008</option>
            <option value=\"2009\" "; if($rok == 2009 ){ echo " selected "; } echo " >2009</option>
            <option value=\"2010\" "; if($rok == 2010 ){ echo " selected "; } echo " >2010</option>
            <option value=\"2011\" "; if($rok == 2011 ){ echo " selected "; } echo " >2011</option>

          </select>
        </span>

        <span style=\"padding-right: 10px;\" >období (měsíc): </span>
    
        <span style=\"padding-right: 10px;\" >
          <select name=\"mesic\" size=\"1\">	  
          <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>
          <option value=\"1\" "; if($mesic == "1" ){ echo " selected "; } echo ">1</option>
            <option value=\"2\" "; if($mesic == "2" ){ echo " selected "; } echo ">2</option>
            <option value=\"3\" "; if($mesic == "3" ){ echo " selected "; } echo ">3</option>
            <option value=\"4\" "; if($mesic == "4" ){ echo " selected "; } echo ">4</option>
            <option value=\"5\" "; if($mesic == "5" ){ echo " selected "; } echo ">5</option>
            <option value=\"6\" "; if($mesic == "6" ){ echo " selected "; } echo ">6</option>
            <option value=\"7\" "; if($mesic == "7" ){ echo " selected "; } echo ">7</option>

            <option value=\"8\" "; if($mesic == "8" ){ echo " selected "; } echo ">8</option>
            <option value=\"9\" "; if($mesic == "9" ){ echo " selected "; } echo ">9</option>
            <option value=\"10\" "; if($mesic == "10" ){ echo " selected "; } echo ">10</option>
            <option value=\"11\" "; if($mesic == "11" ){ echo " selected "; } echo ">11</option>
            <option value=\"12\" "; if($mesic == "12" ){ echo " selected "; } echo ">12</option>

          </select>
        </span>

        <span style=\"padding-right: 10px;\" >Číslo: </span>

        <span style=\"padding-right: 10px;\" >
          <select  name=\"cislo\" size=\"1\" >
          ";
	
	if( $typ_systemu == 2)
	{
	  echo "<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>";
          // echo "<option value=\"".$data_cislo."\" >work in progress</option>";
	   
	   ob_flush();
	   flush();
	   
	   system("/var/www/cgi-bin/cgi-adm2/list_account_for_select.pl ".$cislo);
	}
	else
	{
	 echo "<option value=\"0\" class=\"select-nevybrano\" >Není dostupné</option>";  
	}
	
   echo " </select>
        </span>

	<span style=\"padding-right: 10px;\" >typ systému: </span>
        
        <span style=\"padding-right: 10px;\" >
          <select name=\"typ_systemu\" size=\"1\" onChange=\"self.document.forms.form4.submit()\" >
	  ";
	    
	  echo " <option value=\"2\" "; if ( $typ_systemu == 2){ echo " selected "; } echo " >DialTelecom(PortaOne)</option>
	  </select>
	</span>
	  
        <span style=\"padding-right: 10px;\" >

        <input type=\"submit\" name=\"odeslano\" value=\"OK\" >

        </span>

        </form>
    </div>
    ";

  $dotaz_sql = "SELECT *,DATE_FORMAT(datum_vlozeni, '%d.%m.%Y %H:%i:%s') as datum_vlozeni_format,DATE_FORMAT(connect_time, '%d.%m.%Y %H:%i:%s') as connect_time_format FROM voip_dial ";
 
  // if ( ( is_int($rok) and is_int($mesic) ) )
  if( ( $cislo > 0 and $typ_systemu > 0 ) )
  { 
    if($typ_systemu == 1){ $dotaz_sql .= " WHERE ( cislo_volajiciho = '$cislo' "; }
    elseif( $typ_systemu == 2 ){ $dotaz_sql .= " WHERE ( from2 = '$cislo' "; }    
  }
  else{ $dotaz_sql .= " WHERE ( "; }

  if ( $rok > 0 )
  {
    if ( $cislo > 0 ){ $dotaz_sql .= " AND "; }
    
    if ( $typ_systemu == 1){ $dotaz_sql .= " EXTRACT(YEAR FROM zacatek) = '$rok' "; }
    elseif ( $typ_systemu == 2 ){ $dotaz_sql .= " EXTRACT(YEAR FROM connect_time) = '$rok' "; }
  }

  if ( $mesic > 0)
  {
    if ( ( $cislo > 0 ) or ( $rok > 0) ){ $dotaz_sql .= " AND "; }
    
    if ( $typ_systemu == 1){ $dotaz_sql .= " EXTRACT(MONTH FROM zacatek) = '$mesic' "; }
    elseif ( $typ_systemu == 2 ){ $dotaz_sql .= " EXTRACT(MONTH FROM connect_time) = '$mesic' "; }
  
  }

  if ( $typ_zaznamu == 1 )
  { $dotaz_sql .= " ) order by id_zaznamu "; }
  elseif( $typ_zaznamu == 2 )
  { $dotaz_sql .= " ) order by id_zaznamu2 "; }
  else
  { $dotaz_sql .= " ) "; }
 
 if( ( $rok > 0 or $mesic > 0 or $cislo > 0 ) )
 {
   
   if( $typ_systemu == 2 )
   {
    $dotaz = mysql_query($dotaz_sql);
    $dotaz_radku = mysql_num_rows($dotaz); 
   }
   else
   { echo "<div style=\"font-weight: bold; \" >Musíte vybrat \"Typ systému\"! </div>"; }
   
 } // konec is isset ...
 else
 { echo "<div style=\"font-weight: bold; \" >Musíte vybrat alespoň jeden upřesňující údaj! ( Rok, měsíc, tel. číslo )</div>"; }
 
  if ( $dotaz_radku == 0 )
  { echo "<div style=\"color: orange; padding-top: 20px; font-weight: bold; \">
    Žádné položky v databázi dle výběru.</div>";
  
   // echo "<div style=\"color: grey;\">debug: sql: ".$dotaz_sql."</div>";    
  }
  else
  {
   // echo "<div style=\"color: grey;\">debug: sql: ".$dotaz_sql."</div>";
    
    echo "<table border=\"1\" width=\"95%\" cellpadding=\"5\" class=\"voip-hovory-table\" >";

    $pocet_sloupcu = "11";

    //prvni radka
    
     echo "
     <tr>
       <td><b>Id položky</b></td>
       <td><b>Číslo položky</b></td>

       <td><b>Datum vložení</b></td>
       
       <td><b>Účet</b></td>
       
       <td><b>číslo volajícího</b></td>
       <td><b>číslo volaného</b></td>
       
       <td><b>Země</b></td>
       <td><b>Popis</b></td>

       <td><b>začátek hovoru</b></td>

       <td><b>délka hovoru (součet)</b></td>
       <td><b>délka hovoru (s)</b></td>
      
       <td><b>cena hovoru (Kč)</b></td>

     </tr>";
            
    echo "<tr><td colspan=\"".$pocet_sloupcu."\"><br></td></tr>";
    
    
    $delka_hovoru_soucet = "0";
//    $cena_hovoru_soucet = "0";

    if( $typ_systemu == 2 )
    {

     settype($cena_hovoru_soucet, "float");
     //$cena_hovoru_soucet = 0;
     
     $pocet_zaznamu = "1";
     
     while( $data = mysql_fetch_array($dotaz) )
     {
      echo "<tr>";

      echo "<td>".$data["id_zaznamu2"]."</td>";
      echo "<td>".$pocet_zaznamu."</td>";

      echo "<td>".$data["datum_vlozeni_format"]."</td>";
      echo "<td>".$data["account"]."</td>";
      echo "<td>".$data["from2"]."</td>";
      echo "<td>".$data["to2"]."</td>";
      echo "<td>".$data["country"]."</td>";
      echo "<td>".$data["description"]."</td>";
      echo "<td>".$data["connect_time_format"]."</td>";
      echo "<td>".$data["delka_hovoru"]."</td>";

      echo "<td>".$data["delka_hovoru2"]."</td>";
      echo "<td>".$data["charged_amount"]."</td>";

      echo "</tr>";

       $delka_hovoru_soucet = $delka_hovoru_soucet + $data["delka_hovoru2"];
       $cena_hovoru_soucet = $cena_hovoru_soucet + $data["charged_amount"];

       $pocet_zaznamu++;
    
     } // konec while
    
     echo "<tr><td colspan=\"".$pocet_sloupcu."\"><br></td></tr>";

     echo "<tr>";
          echo "<td colspan=\"10\" ><span style=\"font-weight: bold; \">Součet: </span></td>";

         echo "<td colspan=\"1\" >
                    <span style=\"font-weight: bold; \">".$delka_hovoru_soucet."</span>
                </td>";

         echo "<td colspan=\"1\" >
                    <span style=\"font-weight: bold; \">".round($cena_hovoru_soucet,2)."</span>
                </td>";
   }
         // echo "<td colspan=\"5\"><br></td>";

     echo "</tr>";

    echo "</table>";
  } // konec else if dotaz_radku == 0

  echo "</div>";

 } //konec elseif item == 1
 else
 {
   echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; font-size: 18px; \">
    Prosím vyberte si nějakou možnost ...</div>";
 } 
 
 ?> 
  <!-- konec vlastniho obsahu --> 
  
  </td>
  </tr>
  
 </table>

</body> 
</html>