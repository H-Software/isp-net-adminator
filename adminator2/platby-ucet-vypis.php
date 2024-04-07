<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,69) ) )
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

</head>

<body>

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
   <td colspan="2" ><? include("platby-subcat-inc2.php"); ?></td>
 </tr>
    
 <tr>
  <td colspan="2">
  
  <?
    // sem zbytek
    echo "<div style=\"padding-top: 8px; padding-bottom: 8px; padding-left: 5px; font-family: arial; font-size: 20px; \" >
    Výpis plateb - roční - pro vlastníky ( klienty na F.O.): </div>";
    
    $rok = $_GET["rok"];
    $firma = $_GET["firma"];
    
    echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" name=\"form2\" >";
    
    echo "<table border=\"1\" width=\"60%\" ><tr>";
    
    echo "<td> platby za rok: </td>";
    
    echo "<td>";
	echo "<select name=\"rok\" size=\"1\" >";
    
	    echo "<option value=\"2005\" "; if ($rok == "2005"){ echo " selected "; } echo "> 2005 </option>";
	    echo "<option value=\"2006\" "; if ($rok == "2006"){ echo " selected "; } echo "> 2006 </option>";
	    echo "<option value=\"2007\" "; if ($rok == "2007"){ echo " selected "; } echo "> 2007 </option>";
	    echo "<option value=\"2008\" "; if ($rok == "2008"){ echo " selected "; } echo "> 2008 </option>";
	    
	    echo "<option value=\"2009\" "; if ($rok == "2009"){ echo " selected "; } echo "> 2009 </option>";
	    echo "<option value=\"2010\" "; if ($rok == "2010"){ echo " selected "; } echo "> 2010 </option>";
	    
	echo "</select>";
    
    echo "</td>";
    
    echo "<td>vyberte firmu: </td>";
    
    echo "<td>";
    
	echo "<select name=\"firma\" size=\"1\" >";
	
	    //echo "<option value=\"1\" "; if ($firma == "1"){ echo " selected "; } echo "> všechny </option>";
	    echo "<option value=\"2\" "; if ($firma == "2"){ echo " selected "; } echo "> F.O. - vlastníci </option>";
	    //echo "<option value=\"3\" "; if ($firma == "3"){ echo " selected "; } echo "> S.r.o. - vlastníci2 </option>";
				    
	    
	echo "</select>";
	
    echo "</td>";
    
    echo "<td>";
	echo "<input type=\"submit\" name=\"odeslano\" value=\"OK\" >";
    echo "</td>";
    
    echo "</tr></table>";
    
    echo "</form>";
  
  if ( $rok > 2000)
  {
  // zde zobrazeni plateb
    
    //nejdriv zjistime co mame vybrat
    if ( $firma == 1)
    { $dotaz=pg_query("SELECT * FROM vlastnici WHERE k_platbe > 0 order by id_cloveka "); }
    elseif ( $firma == 2)
    { $dotaz=pg_query("SELECT * FROM vlastnici WHERE ( firma IS NULL AND k_platbe > 0 ) order by id_cloveka "); }
    elseif ( $firma == 3)
    { $dotaz=pg_query("SELECT * FROM vlastnici WHERE ( firma IS NOT NULL AND k_platbe > 0 ) order by id_cloveka "); }
    else 
    {  $dotaz=pg_query("SELECT * FROM vlastnici WHERE k_platbe > 0 order by id_cloveka "); }
  
    $dotaz_radku=pg_num_rows($dotaz);
  
    echo " Počet vlastníků: ".$dotaz_radku." <br><br>";
  
    // prvni radka
    echo "<table border=\"1\" width=\"100%\" >";
    
    echo "<tr>";
    echo "<td><b>id</b></td>";
    echo "<td><b>jméno</b></td>";
    echo "<td><b><div style=\"font-size: 12px\">k platbě <br>(bez DPH) </div></b></td>";
    
    $mesice = array ( "led", "un", "bře","dub", "kvě", "čer", "červ", "srp", "zář", "říj", "list", "pros" );
    
    foreach ($mesice as $my_mesice ){
       echo "<td> $my_mesice </td>\n";
       }
       
    echo "</tr>";
    
    if ( $dotaz_radku > 0 )
    {
	$id=1;
	
	while( $data=pg_fetch_array($dotaz) ):
    
	$fakturacni=$data["fakturacni"];
	$id_cloveka=$data["id_cloveka"];
	
	if ( $fakturacni > 0)
	{ $barva="green";}
	else
	{ $barva=""; }
	
	echo "<tr>";
	echo "<td bgcolor=\"".$barva."\" >".$id."</td>";	
    
	echo "<td bgcolor=\"".$barva."\" ><b>".$data["nick"]." ( vs: ".$data["vs"]." )</b><br> ";
	echo " ".$data["prijmeni"]." ".$data["jmeno"].", ".$data["ulice"].", ".$data["psc"]." ".$data["mesto"];
	
	echo "</td>";
	
	echo "<td bgcolor=\"".$barva."\" >".$data["k_platbe"]."</td>";
	
	for ($i=1; $i<=12; $i++)
	{
	    if ( $i < 10 ){ $i="0".$i; }
	    
	    $zaplaceno_za=$rok."-".$i;
	    
	    $dotaz_platby=pg_query("SELECT * from platby where ( id_cloveka = '$id_cloveka' AND zaplaceno_za LIKE '$zaplaceno_za' )");	
	    $dotaz_platby_radku=pg_num_rows($dotaz_platby);

	    if ( $dotaz_platby_radku == 0 )
	    { // neni platba
	      
	      // nejdriv zjistime, jestli uz byl clovek pripojenej 
	      $dotaz_datum=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
	      $dotaz_datum_radku=pg_num_rows($dotaz_datum);
	     
	      while( $data_datum=pg_fetch_array($dotaz_datum) )
	      { $datum_pridani_src=$data_datum["pridano"]; }
	    
	       $datum_orezano = split('-', $datum_pridani_src);
	       $rok_orezano = $datum_orezano[0];
	       $mesic_orezano = $datum_orezano[1];
			   	      
	     if ( ( $rok_orezano == $rok) )
	     { // nebyl, tj. platba nechybi
	     
	        if ( ( $mesic_orezano == $i ) ){ echo "<td bgcolor=\"yellow\" >N/E</td>"; }
		elseif ( ( $mesic_orezano > $i ) ){ echo "<td bgcolor=\"aqua\">N/E</td>"; }
		else { echo "<td bgcolor=\"grey\" > <br> </td>"; }
	     }
	     else
	     { // nebyl, tj. chybi platba
		echo "<td bgcolor=\"grey\" > <br> </td>"; 
	     }
	    
	    } // konec if platby_radku == 0
	    else
	    {
		while($data_platby=pg_fetch_array($dotaz_platby) ):
	    
		echo "<td bgcolor=\"\yellow\" >";
		echo "<a href=\"platby-ucet-vypis-detail.php?id_platby=".$data_platby["id"]."\" >OK</a>";
		echo "</td>";
		
		endwhile;
	    }


	}
	
	echo "</tr>";
	$id++;
	
	endwhile;
  
  
    } // konec if dotaz_radku > 0
    // else{}
    
    echo "</table>";
    
  }  // konec if rok > 2000
  else
  {
  // nevybran rok, nic nedelat
  echo " Vyberte rok ... ";
  }
 
  ?>
  
  </td>
  </tr>
 
 </table>

</body>
</html>

