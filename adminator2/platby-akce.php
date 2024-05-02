<?php

require("include/main.function.shared.php");
include ("include/config.php"); 
include ("include/check_login.php");
include ("include/check_level.php");

if( !( check_level($level,37) ) )
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
       
    $send=$_POST["send"];
    if ( ( strlen($send) <= 0) )
    { $send=$_GET["send"]; }
    
    $tisk=$_GET["tisk"];
    
    set_time_limit(0);
     
    // $id_klienta=$_POT["id"];
    $klient=$_POST["klient"];
    $firma=$_POST["firma"];
    $castka=$_POST["castka"];
    $zaplaceno_za=$_POST["zaplaceno_za"];
    $zaplaceno_do=$_POST["zaplaceno_do"];	
    $rok=$_POST["rok"];
    
    $dalsi=$_POST["dalsi"];
    
    $dan="20";
    
    $datum = strftime("%m", time() );
    $datum2 = strftime ("%Y", time() );
	
    if ( ( strlen($zaplaceno_za) <= 0 ) ){ $zaplaceno_za=$datum; }
    if ( ( strlen($zaplaceno_do) <= 0 ) ){ $zaplaceno_do=$datum; }
    if ( ( strlen($rok) <= 0 ) ){ $rok=$datum2; }
	
    //2. odkaz - pridani hotovostni platby
    
    if( ( $castka < 1 ) or ( ! ereg("^[1234567890]+$",$castka) ) )
    { 
	unset($send); 
	$error .= "<br> Zadejte částku! ( v celočíselném formátu ) <br>";
    }
    
    if( ( strlen($zaplaceno_za) != 2 ) )
    {
	unset($send);
	$error .= "<br> Období není ve správném formátu! ( MM ) <br>";
    }
    
    if( ( ( $dalsi == 1) and (  ( strlen($zaplaceno_do) != 2 ) ) ) )
    {
	    unset($send);
	    $error .= "<br> Koncové období není ve správném formátu! ( formát: MM ) <br>";
    }
    
    // prevod do promennejch
    // list($za_rok, $za_mesic)=explode("-",$zaplaceno_za);
    
    // list($do_rok, $do_mesic)=explode("-",$zaplaceno_do);
    
    if( ( ( $dalsi == 1 ) and ( $zaplaceno_za > $zaplaceno_do )  ) )
    { 
	unset($send);
	$error .= "<br> Koncové období musí větší než počáteční! <br>";
    }
    
    //dopredna platba nejde moc :)
    
    if( ($rok == "2012") and ($zaplaceno_do > "03") ){    
	unset($send);
        $error .= "<br> Platba maximálně na Čtvrtletí (kvartál), více nejde! <br>";                
    }
        
    //dopredna platba nejde moc :)
                                
    if( ($rok == "2012") and ($zaplaceno_za >= "03") ){

	unset($send);
	$error .= "<br> Hotovostní platba za březen 2012 a dále není možná. (buď přes účet nebo v dubnu přes Pohodu)<br>";
    
    
    }

    // echo "prvotni : ".$za_rok."  mesic: ".$za_mesic.", konc: ".$do_rok." mesic: ".$do_mesic;
    
	if( isset($send) )
	{
	//budeme ukladat	    
	    echo "<span style=\"color: grey; \"> debug: klient: $klient, firma: $firma , castka: $castka , 
		    zaplaceno_za: $zaplaceno_za , zaplaceno_do: $zaplaceno_do , dalsi: $dalsi <br></span>";
	        
	    if( $dalsi == 1)
	    {
	    
		for ($i=$zaplaceno_za;$i<=$zaplaceno_do;$i++)
		{
		    if ( ($i < 10) and ( strlen($i) < 2) ){ $i="0".$i; }
		    // ted mame v promeny i spranej format mesicu
		
		    $obdobi=$rok."-".$i;
		    
		    $platba_add = array( "id_cloveka" => $klient, "castka" => $castka, 
				    "zaplaceno_za" => $obdobi , "hotove" => "1" );
	    
	    	    if ( $firma == 2 ) { $platba_add["firma"] = "1"; }	 
	    
		    //include("include/config.pg.php");
	    
		    $res = pg_insert($db_ok2, 'platby', $platba_add);
	    
		    if ($res) { echo "<br><H3><div style=\"color: green; \" >Platba za období: $obdobi úspěšně uložena do databáze.</div></H3>\n"; }
		    else { echo "<div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div><br>\n"; }
	
		    $pole="<b>akce: pridani hotovostni platby ;</b><br>";
	        
		    foreach ($platba_add as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }

		    if ( $res == 1){ $vysledek_write=1; }
		
		    $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");	
		
    		} // konec foru
		
		echo "<a href=\"platby-akce-tisk.php";
		echo "?tisk=ok&send=ok&klient=".$klient."&firma=".$firma."&castka=".$castka;
		echo "&dalsi=".$dalsi."&zaplaceno_za=".$zaplaceno_za."&zaplaceno_do=".$zaplaceno_do."&rok=".$rok."\">TISK ÚDAJŮ</A>";
								
	    } // kone if dalsi == 1
	    else
	    {	
		$zaplaceno_za=$rok."-".$zaplaceno_za;
		
		$platba_add = array( "id_cloveka" => $klient, "castka" => $castka, 
				    "zaplaceno_za" => $zaplaceno_za , "hotove" => "1" );
	    
	        if ( $firma == 2 ) { $platba_add["firma"] = "1"; }	 
	    
		//include("include/config.pg.php");
	    
		$res = pg_insert($db_ok2, 'platby', $platba_add);
	    
		if ($res) { echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; }
		else { echo "<div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div><br>".pg_last_error($db_ok2)."<br><br>\n"; }
	
		$pole="<b>akce: pridani hotovostni platby; </b><br>";
	        
		foreach ($platba_add as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }
		// $pole=$pole.",<br> akci provedl: ".\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email.", vysledek akce dle postgre: ".$res.", datum akce: ".$datum2;
		     
		if ( $res == 1){ $vysledek_write=1; }
		
	        $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");
	
		echo "<a href=\"platby-akce-tisk.php";
		echo "?tisk=ok&send=ok&klient=".$klient."&firma=".$firma."&castka=".$castka."&zaplaceno_za=".$zaplaceno_za."\">TISK ÚDAJŮ</A>";
	
	    } // konec else dalsi == 1    	    
	
	} // konec if isset == 1
	else
	{
    
	echo "<form method=\"POST\" action=\"\" name=\"form2\" > \n";
	
	// echo "<input type=\"hidden\" name=\"mod\" value=\"".$mod."\" >";
	
	echo "<table border=\"0\" width=\"85%\" > \n ";
	
	echo "<tr>";
	echo "<td colspan=\"3\" >";
	
	    echo "Přidání hotovostní platby: ";
	
	    echo "<br><br><span style=\"color: red; \">".$error."</span><br> ";
	    
	echo "</td></tr> \n ";
	
	echo "<tr><td colspan=\"3\" > <br> </td></tr> \n ";
	
	echo "<tr><td colspan=\"1\" >"." Vyberte firmu: "."</td> \n ";
	
	echo "<td colspan=\"2\"> <select size=\"1\" name=\"firma\" onChange=\"self.document.forms.form2.submit()\" >";
	
	echo "<option value=\"1\" "; 
	if ( ( ( $_POST["firma"] == 1 ) or ( !isset($_POST["firma"] ) ) ) ){ echo " SELECTED "; }
	echo " > Vlastníci ( Fyzická )</option> \n ";
	
	
	echo "<option value=\"2\" ";
	if ( ( $_POST["firma"] == 2 ) ){ echo " SELECTED "; }
	echo " > Vlastníci2 ( Simelon, s.r.o. ) </option> \n ";
	
	
	echo "</select></td></tr> \n ";
	
	echo "<tr><td colspan=\"3\" ><br></td></tr> \n";
	
	echo "<tr><td>"." Klient: "."</td> \n";
	
	print ' <td colspan="2" > <select size="1" name="klient" > '." \n ";
	
	if( ( ($firma == 1) or ( !isset($firma) ) ) )
	{
	    $dotaz=" SELECT * FROM vlastnici WHERE firma is NULL ORDER BY prijmeni";     
	}
	else
	{ 
	     $dotaz="SELECT * FROM vlastnici WHERE firma = '1' ORDER BY prijmeni "; 
	    //echo "<div style=\"\">Platby klientů na SIMELON, s.r.o. lze přijímat pouze přes program \"Pohoda SQL\"</div>";
	}
	
	$dotaz_klienti = pg_query($dotaz);
	$radku_klienti = pg_num_rows($dotaz_klienti);
	    
	        
	if( $radku_klienti == 0 )
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
	
	echo "</select> </td> </tr>";
	
	echo "<tr><td colspan=\"3\" > <br> <input type=\"hidden\" name=\"jmeno\" value=\"\" ></td></tr> \n ";
	
	echo "<tr><td > Částka (<b>měsíční</b>)<br> s DPH : </td> \n ";
	echo "<td colspan=\"1\" width=\"75%\" >"." <input type=\"text\" name=\"castka\" value=\"".$castka."\" >"." v Kč ( <b>Nutno zaokrouhlovat na koruny ) </b></td>";
	
	echo "<td colspan=\"2\" >více plateb: ";
	
		echo "  <select name=\"dalsi\" size=\"1\" onChange=\"self.document.forms.form2.submit()\" >";
		
		    echo "<option value=\"0\" "; if ( $dalsi == 0) { echo " selected "; } echo " > Ne </option>";
		    echo "<option value=\"1\" "; if ( $dalsi == 1) { echo " selected "; } echo " > Ano </option>";
		    
		echo "</select>";
	
	echo "</td> </tr> \n ";
	    
	echo "<tr><td colspan=\"3\" > <br> </td></tr> \n ";
	
	echo "<tr><td> ";
	
	if ( $dalsi == 1 )
	{ echo " Počáteční odobí: "; }
	else
	{ echo " Rok - měsíc: "; }
	
	echo "</td> \n ";
	
	echo "<td colspan=\"1\" > ";
	
	// echo "<td>";
	    echo "<select size=\"1\" name=\"rok\" onChange=\"self.document.forms.form2.submit()\" >";
	
		echo "<option value=\"2005\" "; if ( $rok == 2005){ echo " selected "; } echo " > 2005 </option>";
		echo "<option value=\"2006\" "; if ( $rok == 2006){ echo " selected "; } echo " > 2006 </option>";
		echo "<option value=\"2007\" "; if ( $rok == 2007){ echo " selected "; } echo " > 2007 </option>";
		echo "<option value=\"2008\" "; if ( $rok == 2008){ echo " selected "; } echo " > 2008 </option>";
	
		echo "<option value=\"2009\" "; if ( $rok == 2009){ echo " selected "; } echo " > 2009 </option>";
		echo "<option value=\"2010\" "; if ( $rok == 2010){ echo " selected "; } echo " > 2010 </option>";
		echo "<option value=\"2011\" "; if ( $rok == 2011){ echo " selected "; } echo " > 2011 </option>";
	
		echo "<option value=\"2012\" "; if ( $rok == 2012){ echo " selected "; } echo " > 2012 </option>";
	        
	    echo "</select>";
	    
	// echo "</td>";
	
	echo "  <input size=\"6\" type=\"text\" name=\"zaplaceno_za\" value=\"".$zaplaceno_za."\" > ";
	
	echo "</td>";
	
	echo "<td> ";
	
	if ( $dalsi == 1 )
	{
	  echo "Koncové období: $rok-";
	
	  echo "<input type=\"text\" name=\"zaplaceno_do\" value=\"".$zaplaceno_do."\" >";
	}
	
	echo "</td>";
	
	echo "</tr>";
	
	echo "<tr><td colspan=\"3\" ><br></td></tr> \n ";
	
	 echo '<tr>
	         <td colspan="3" align="left">
		 
	         <hr>
	         <input name="send" type="submit" value="OK">
	         </td>
	     
	     </tr>';
					     
	
	
	echo "</table>";
	
	echo "</form>";
	
	}
        
  ?>
  
  </td>
  </tr>

 </table>
 
</body>
</html>
