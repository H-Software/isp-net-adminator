<?php


include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,79) ) )
{
// neni level

$stranka='nolevelpage.php';
header("Location: ".$stranka);
 
   echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
   Exit;
      
}
	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="20" bgcolor="silver" >

    <span style="margin-left: 40px; "><a href="vlastnici-cat.php" class="odkaz-uroven-vys" >| O úrověn výš |</a></span>
      
    <span style="margin-left: 40px; "><a href="vypovedi.php" >Výpis výpovědí </a></span>
    
    <span style="margin-left: 40px; "><a href="vypovedi-vlozeni.php" > Vložení výpovědi </a></span>
	 
    <span style="margin-left: 40px; "><a href="vypovedi-plaintisk.php" > Tisk nevyplněné žádosti </a></span>
 
 
 </td>
  </tr> 
  
  <tr>
  <td colspan="2">
  
  <!-- zacatek vlastniho obsahu -->
  
  <?
	$send=$_POST["send"];
	$firma=$_POST["firma"];
	$datum_uzavreni=$_POST["datum_uzavreni"];
	$datum_vypovedi=$_POST["datum_vypovedi"];
	$duvod_vypovedi=$_POST["duvod_vypovedi"];
	$vypovedni_lhuta=$_POST["vypovedni_lhuta"];
	$uhrazeni_vypovedni_lhuty=$_POST["uhrazeni_vypovedni_lhuty"];

	$klient=$_POST["klient"];
	
	$tisk=$_GET["tisk"];
	
	echo "<br>";
	// if ()
	{
	    // kontrola data uzavreni
	    if ( ( strlen($datum_uzavreni) < 2 ) )
	    { 
	    $error="true"; 
	    $log .= " <div style=\"color: red; font-weight: bold; \"> Není vyplněné pole: \"Datum uzavření \". </div>";
	    }
	    else
	    {
		$d_u_format=ereg("^(([0-2]?[0-9])|(3[01])).(((0?[0-9]))|(1[012])).[0-9]{4}$", $datum_uzavreni);
		if ( !($d_u_format) )
		{
		$error="true";
		$log .= "<div style=\"color: red; font-weight: bold; \"> Pole \"Datum uzavření\" není ve správném formátu.</span>";
		
		} // konec if
	    
	    } // konec else - strlen datum < 2
	
	    // kontrola data vypovedi
	    if ( ( strlen($datum_vypovedi) < 2 ) )
	    { 
	    $error="true"; 
	    $log .= " <div style=\"color: red; font-weight: bold; \"> Není vyplněné pole: \"Výpověď ke dni\". </div>";
	    }
	    else
	    {
		$d_v_format=ereg("^(([0-2]?[0-9])|(3[01])).(((0?[0-9]))|(1[012])).[0-9]{4}$", $datum_vypovedi);
		if ( !($d_v_format) )
		{
		$error="true";
		$log .= "<div style=\"color: red; font-weight: bold; \"> Pole \"Výpověď ke dni\" není ve správném formátu.</span>";
		
		} // konec if
	    
	    } // konec else - strlen datum < 2
	
	
	
	}
	
	if ( ( ( !isset($send) ) or (isset($error) )  ) )
	{
	
	echo $log;
	
	echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" name=\"form2\" >";
	
	echo "<table border=\"0\" width=\"100%\" >";
  
	echo "<tr><td colspan=\"2\"><br></td></tr>";

	echo "<tr><td colspan=\"1\" >"." Vyberte firmu: "."</td> \n ";

        echo "<td colspan=\"1\"> <select size=\"1\" name=\"firma\" onChange=\"self.document.forms.form2.submit()\" >";

        echo "<option value=\"1\" "; if ( ( ( $_POST["firma"] == 1 ) or ( !isset($_POST["firma"] ) ) ) ){ echo " SELECTED "; } echo " > Vlastníci ( Fyzická )</option> \n ";

        echo "<option value=\"2\" "; if ( ( $_POST["firma"] == 2 ) ){ echo " SELECTED "; } echo " > Vlastníci2 ( Simelon, s.r.o. ) </option> \n ";

        echo "</select></td></tr> \n ";

        echo "<tr><td colspan=\"2\" ><br></td></tr> \n";

	echo "<tr><td>"." Vyberte klienta: "."</td> \n";

        print ' <td colspan="1" > <select size="1" name="klient" > '." \n ";

        if ( ( ($firma == 1) or ( !isset($firma) ) ) )
        {       $dotaz=" SELECT * FROM vlastnici ORDER BY prijmeni"; }
        else
        {        $dotaz="SELECT * FROM vlastnici ORDER BY prijmeni "; }

         $dotaz_klienti = pg_query($dotaz);
         $radku_klienti = pg_num_rows($dotaz_klienti);

         if ( $radku_klienti == 0 )
             { echo "CHYBA! Zadni klienti v databazi! <br>"; }
         else
             {

              while( $data2=pg_fetch_array($dotaz_klienti) ):

              echo "<option value=\"".$data2["id_cloveka"]."\" ";

              if ( $klient == $data2["id_cloveka"] )
              { echo " selected  "; }

              echo " >";

              echo " ".$data2["prijmeni"].", ".$data2["jmeno"]."  ( ".$data2["nick"]."  )  ";
              echo " ".$data2["ulice"].", Kč (bez dph): ".$data2["k_platbe"];

              echo "</option> \n ";

              endwhile;

             }

        echo "</select></td></tr>";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";

	echo "<tr><td colspan=\"1\" >datum uzavření smlouvy: </td>
		    <td><input type=\"text\" name=\"datum_uzavreni\" value=\"".$datum_uzavreni."\" > ( formát: dd.mm.rrrr ) </td></tr> \n";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";

	echo "<tr><td colspan=\"1\" > Výpověď ke dni: </td>
		    <td><input type=\"text\" name=\"datum_vypovedi\" value=\"".$datum_vypovedi."\" > ( formát: dd.mm.rrrr ) </td></tr> \n";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";

	echo "<tr><td colspan=\"1\" > Výpovědní lhůta 3 měsíce: </td>
		    <td>
			<select size=\"1\" name=\"vypovedni_lhuta\" >
		
			    <option value=\"1\" "; 
				if ( ( ($vypovedni_lhuta == 1) or ( !isset($vypovedni_lhuta) ) ) ) { echo " selected "; } 
				echo " > Ano </option>
			    <option value=\"2\" ";
			    if ( $vypovedni_lhuta == 2 ){ echo " selected "; } echo " > Ne </option>
			</select>	
		    </td></tr> \n";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";

	echo "<tr><td colspan=\"1\" > Uhrazení poplatků <br>za dobu výpovědní lhůty: </td>
		    <td>
			<select size=\"1\" name=\"uhrazeni_vypovedni_lhuty\" >
		
			    <option value=\"1\" "; if ( $uhrazeni_vypovedni_lhuty == 1 ){ echo " selected "; } echo " > Hotově </option>
			    <option value=\"2\" "; if ( $uhrazeni_vypovedni_lhuty == 2 ){ echo " selected "; } echo " > Převodem </option>
			    <option value=\"3\" "; if ( ( ($uhrazeni_vypovedni_lhuty == 3) or ( !isset($uhrazeni_vypovedni_lhuty) ) ) ){ echo " selected "; } echo "> Doběhnutím trvalého příkazu </option>
			</select>	
		    </td></tr> \n";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";
	
	
	echo "<tr><td colspan=\"1\" > Důvod výpovědi: </td>
		    <td><textarea name=\"duvod_vypovedi\" cols=\"40\" rows=\"5\">".$duvod_vypovedi."</textarea></td></tr> \n";

	echo "<tr><td colspan=\"2\" ><br></td></tr> \n";
	
	echo "<tr><td colspan=\"1\" ><br></td><td><input type=\"submit\" name=\"send\" value=\"Vložit\"></td></tr> \n";

	echo "</table>";
	echo "</form>";
	
	}
	else
	{
	// vlozeni zadosti o db
	
	$datum_vlozeni = strftime("%d/%m/%Y", time());
	
	list ($den1, $mesic1, $rok1 ) = explode ("/", $datum_vlozeni);
	list ($den2, $mesic2, $rok2 ) = explode (".", $datum_uzavreni);
	list ($den3, $mesic3, $rok3 ) = explode (".", $datum_vypovedi);
	
	$datum_vlozeni = $rok1."-".$mesic1."-".$den1;
	$datum_uzavreni = $rok2."-".$mesic2."-".$den2;
	$datum_vypovedi = $rok3."-".$mesic3."-".$den3;
	
	// $datum_vypovedi = str_replace (".", "-", $datum_vypovedi );
	// $datum_uzavreni = str_replace (".", "-", $datum_uzavreni );
	
	echo "<br><h4> Zadané údaje: </h4><br>";
	echo "<b>id vlastníka: </b>".$klient."<br>";
	echo "<b>datum vložení : </b>".$datum_vlozeni."<br>";
	echo "<b>datum uzavření smlouvy: </b>".$datum_uzavreni."<br>";
	echo "<b>datum výpovědi smlouvy: </b>".$datum_vypovedi."<br>";
	echo "<b>výpovědní lhůta: </b>".$vypovedni_lhuta."<br>";
	echo "<b>uhrazení výpovědní lhůty: </b>".$uhrazeni_vypovedni_lhuty."<br>";
	echo "<b>důvod výpovědi: </b>".$duvod_vypovedi."<br>";
	
	$dotaz_klient=mysql_query("SELECT * FROM vypovedi WHERE id_vlastnika LIKE '$klient' ");
	$dotaz_klient_radku=mysql_num_rows($dotaz_klient);
	
	if ( $dotaz_klient_radku > 0 )
	{
	 // zadost uz je vlozena
	 
	 echo "<br><div style=\"font-size: 16px; color: red; font-weight: bold; \"> Žádost nelze uložit. Vybraný klient již má žádost v databázi. </div>";
	}
	else
	{
	    $res=mysql_query("INSERT INTO vypovedi (id_vlastnika,datum_vlozeni,datum_uzavreni,datum_vypovedi,vypovedni_lhuta,uhrazeni_vypovedni_lhuty,duvod) 
	    VALUES ('$klient','$datum_vlozeni','$datum_uzavreni','$datum_vypovedi','$vypovedni_lhuta', '$uhrazeni_vypovedni_lhuty','$duvod_vypovedi')");
	 
	    if($res) { echo "<br><H3><div style=\"color: green; \" >Žádost úspěšně vložena.</div></H3>\n"; }
	    else { echo "<H3><div style=\"color: red; \">Chyba! Žádost nelze vložit. </div></H3><br>\n".mysql_error($res); }
	    
	}
	
	echo "<br>";
	
	echo "<a href=\"vypovedi-tisk.php?tisk=1&id_vlastnika=".$klient."&datum_uzavreni=".$datum_uzavreni."&datum_vypovedi=".$datum_vypovedi."&duvod=";
	echo $duvod_vypovedi."&datum_vlozeni=".$datum_vlozeni."&firma=".$firma."&vypovedni_lhuta=".$vypovedni_lhuta."&uhrazeni_vypovedni_lhuty=";
	echo $uhrazeni_vypovedni_lhuty."\" > TISKNOUT ŽÁDOST </a>";
	
	echo "<br><br>";
	}
	  
  ?>
  
  <!-- konec vlastniho obsahu -->
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 

