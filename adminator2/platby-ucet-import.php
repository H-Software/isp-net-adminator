<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,71) ) )
{
 // neni level

 $stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}
	
set_time_limit(0);

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
    
    $odeslano1=$_POST["odeslano1"];
    $odeslano2=$_POST["odeslano2"];
    $odeslano3=$_POST["odeslano3"];
    
    $typ=$_POST["typ"];
    
    include ("include/config.pg.php");
    
    if ( ( !isset($odeslano1) and !isset($odeslano2) and !isset($odeslano3) ) )
    {
    // zobrazime form
    
    echo "<br><H3>Importování dat z bankovního výpisu</H3> <br><br>";
    echo '
	    <FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'" ENCTYPE="multipart/form-data"> 
	    
	    Vyberte soubor s daty: <br><br>
	    
	    <INPUT TYPE="FILE" NAME="soubor" VALUE="" >

	    <br><br>
	     <label>Režim rozpoznávání plateb: </label>
	     <br>
	    
	    <select size="1" name="typ" >
		<option value="1" > Striktní ( musí sedět vs i částka ) </option>
		<option value="2" > Maximální ( RISKANTNÍ - pouze na základě VS ) </option>
	    </select>
	    
	    <br><br>
	    <hr width="100px" align="left" >
	    <INPUT TYPE="submit" name="odeslano1" VALUE=" Další "> 
    </FORM>';
    
    }
    elseif ( isset($odeslano1) )
    {
	echo "<form method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\" >";
	
    $datum = strftime("%d-%m-%Y-%H-%M-%S", time());
    
    $jmeno_souboru="vypis-".$datum.".csv";
    
    $rs = move_uploaded_file($_FILES["soubor"]["tmp_name"],"vypisy/".$jmeno_souboru);
    
    if ( $rs == 1 )
    { echo "<span style=\"color: green; font-weight: bold; \" >Soubor úspěšně uploadován / uložen</span> pod názvem: ".$jmeno_souboru." <br><br>"; }
    else 
    { 
	echo "<span style=\"color: red; \" >Soubor nelze uložit. / Soubor nebyl vybrán. </span> <br><br>"; 
	exit; 
    }
    
    if ( $typ == 1 )
    { echo " Režim rozpoznávání - <span style=\"color: green; font-weight: bold; \">Striktní</span> "; }
    elseif ( $typ == 2 )
    { echo " Režim rozpoznávání - <span style=\"color: red; \">Maximální</span> "; }
    else
    { echo " Režim rozpoznávání - Nelze zjistit  "; }
    
    echo "<br><br>";
    
    // zde tabulka barev
    echo "<table border=\"1\" width=\"90%\" >";
    
    echo "<tr><td> Legenda barev: </td></tr>";
    
	echo "<tr bgcolor=\"green\" ><td> Tmavě zelená</td><td> koretní platby</td></tr>";
	echo "<tr bgcolor=\"lime\" ><td> Světle zelená</td><td> platby s větší částkou </td></tr>";
	echo "<tr><td bgcolor=\"orange\" > Oranžová </td><td> platby s menší částkou </td></tr>";
	echo "<tr><td bgcolor=\"aqua\" > Světle modrá (aqua)</td><td> platby s jinou částkou </td></tr>";
	echo "<tr><td bgcolor=\"yellow\" > Žlutá </td><td> Duplicitní záznamy </td></tr>";
	echo "<tr><td bgcolor=\"red\" > červené </td><td> Duplicitní variabilní symboly </td></tr>";
	echo "<tr><td> bez barvy </td><td> Nerozpoznané platby </td></tr>";
    
    echo "</table>";
    
    echo "<br><br>";
    
         //otevření souboru 
        $file_csv = fopen ("vypisy/".$jmeno_souboru, "r"); 
        //začátek tabulky 	
	echo "<table border='1' align='center'>"; 
         
        //cyklus který postupně přečte všechny řádky tabulky ... 
        //... řádek potom rozdělí na jednotlivé hodnoty do pole $data 
        while ($data = fgetcsv ($file_csv, 1000, ";") )  
        { 
                 
    	if ( ( ( strlen($data["0"]) > 0 ) and ( strlen($data["3"]) > 0 ) ) ) 
	{ // prazdny radky a zaporny castky nebudem ukladat
		

	    //prvne to nandáme pro promenych
	    $datum=iconv("CP1250","UTF-8",$data["0"] ); // datum
	    $pole2=iconv("CP1250","UTF-8",$data["1"] ); // položka
	    $ucet=iconv("CP1250","UTF-8",$data["2"] );  // cislo protiuctu
	    $castka=iconv("CP1250","UTF-8",$data["3"] );  // obrat
	    $mena=iconv("CP1250","UTF-8",$data["4"] );   // mena
	    $pole6=iconv("CP1250","UTF-8",$data["5"] );	 // datum odpisu
	    $pozn=iconv("CP1250","UTF-8",$data["6"] );   // info o platbe
	    $jmeno=iconv("CP1250","UTF-8",$data["7"] );  // 
	    $vs=iconv("CP1250","UTF-8",$data["8"] );
	    $pole10=iconv("CP1250","UTF-8",$data["9"] );
	    $pole11=iconv("CP1250","UTF-8",$data["10"] );
	    $vs2=iconv("CP1250","UTF-8",$data["11"] );
	    
	    //prevadeni castky
	    
	    //prvne mezery
	    
	    // lepe - trim()
	    
	     $castka=trim($castka);
	    
	    
	    list( $pred,$po)=explode(" ", $castka);
	    $castka=$pred.$po;
	
	    //  desetine carky
	    $pole_castka=explode(",",$castka);
	    
	    $castka2=$pole_castka["0"];
	    
	    if ( isset($pole_castka["1"]) )
	    { $castka=$pole_castka["0"].".".$pole_castka["1"]; }
	    else
	    { $castka=$pole_castka["0"]; }
				    
	    // prvne zjistime jestli udaj neni v db
	    $dotaz=pg_query("SELECT id_polozky FROM platby_polozky 
		WHERE ( datum LIKE '$datum' AND castka = '$castka' AND ucet LIKE '$ucet' 
		AND pozn LIKE '$pozn' AND vs = '$vs' AND pozn LIKE '$pozn' ) 
		");
		
	    $dotaz_radku=pg_num_rows($dotaz);
	    if ( $dotaz_radku > 0){ $duplicitni_zaznam="true"; }    
	    
	    // ted zjistime jestli se da platba priradit
	    
	    if ( ! ( $duplicitni_zaznam == "true") )
	    {
		$dotaz_vlastnici=pg_query("SELECT id_cloveka,k_platbe FROM vlastnici WHERE ( firma IS NULL AND vs LIKE '$vs' ) ");    
	        $dotaz_vlastnici_radku=pg_num_rows($dotaz_vlastnici);
	    
		if ( $dotaz_vlastnici_radku > 1 )
		{ 
		    $duplicitni_vs="true"; 
		}
		elseif ( $dotaz_vlastnici_radku == 1)
		{ 
		    while ( $data_vlastnici=pg_fetch_array($dotaz_vlastnici) ):
	    
	        	$id_cloveka=$data_vlastnici["id_cloveka"];
			$k_platbe=$data_vlastnici["k_platbe"];
		
		    endwhile;
		
		    $k_platbe=$k_platbe*1.19;
		
		    $vysledek = $castka - $k_platbe;
		
		    // jestli sedi castka
		    if ( ( ($vysledek > -5) and ($vysledek < 5) ) )
		    { 
			$plati="presne"; 
			$sparovano=$id_cloveka;
		    }
		    elseif ( ( ( $vysledek > 5 ) and ( $vysledek < 330 ) ) )
		    { 
			$plati="vic"; 
			if ( $typ == 2){ $sparovano=$id_cloveka; }
		    }
		    elseif ( ( ($vysledek < -5) and ( $vysledek > -250 ) ) )
		    { 
			$plati="min"; 
			if ($typ == 2 ){ $sparovano=$id_cloveka; }
		    }
		    else
		    { $plati="jinak"; }
		
		    // echo "vysledek: $vysledek <br>";
		} // konec if dotaz_vlastnici_radku == 1
		else
		{ $nelze_priradit="true"; }
	    
		// ted to ulozime
	    
		// predevem si datum
		$pole_datum=explode(".",$datum);
	    
		$mesic=$pole_datum["1"];
	        if ( $mesic < 10 ){ $mesic="0".$mesic; }
	    
		$datum_pridani=$pole_datum["2"]."-".$mesic;
	        
		//prvne do plateb
		if ( $sparovano > 0 )
		{	
		    
		    $platby=array( "id_cloveka" => $sparovano , "castka" => $castka2, 
		    "zaplaceno_za" => $datum_pridani, "zvypisu" => "1" );
		    
		    $platby_rs=pg_insert($db_ok2,'platby',$platby);
		    
		    if ( $platby_rs == 1)
		    {
			// zjistime id platby
			$dotaz_platby=pg_query("SELECT * FROM platby 
						WHERE ( id_cloveka = '$sparovano' AND castka = '$castka2'
						        AND zaplaceno_za LIKE '$datum_pridani' ) ");
			
			$dotaz_platby_radku=pg_num_rows($dotaz_platby);
		    
			if ( $dotaz_platby_radku == 1 )
			{
			    while( $data_platby=pg_fetch_array($dotaz_platby) ):
			
				$id_platby=$data_platby["id"];
			    
			    endwhile;
			}
			else
			{ $id_platby=""; }
		      
		      }
		      else
		      { $id_platby=""; }
		      
		  } // konec if sparovano > 0
		    
		    $platby_polozky=array( "datum" => $datum , "pole2" => $pole2 , "ucet" => $ucet, 
		    "castka" => $castka, "mena" => $mena, "pole6" => $pole6, "pozn" => $pozn,
		    "jmeno" => $jmeno, "pole10" => $pole10, "pole11" => $pole11, "vs2" => $vs2, "vs" => $vs );
		
		    if ( ( ( $sparovano > 0 ) and ( $id_platby > 0 ) ) )
		    { $platby_polozky["id_platby"]=$id_platby; }
		    		    
		    $platby_polozky_rs=pg_insert($db_ok2,'platby_polozky',$platby_polozky);	
		    
		    if ( $platby_polozky_rs == 1)
		    {
			$dotaz_id=pg_query("SELECT id_polozky from platby_polozky order by id_polozky");
			
			while( $data_id=pg_fetch_array($dotaz_id)):
			
			$id_polozky=$data_id["id_polozky"];
			
			endwhile;
		    }
		    else
		    { $id_polozky=""; }
		    
	    } // konef if duplicitni_zaznam == true
	    
	    // sem vypis
	    
	    // prirazeny platby tmave zelene - green
	    // vetsi platby svetle zelenou - lime
	    // mensi platby oranzovou
	    // jine platby - aqua 
	    // duplicitni zaznamy zlutou
	    // duplicitni vs cervenou
	    
	    if ( $duplicitni_zaznam == "true" ){ $barva="yellow"; }
	    else
	    {
		if ( $duplicitni_vs == "true" ){ $barva="red"; }
		if ( $sparovano > 0){ $barva="green"; }
	    
		if ( $plati == "vic" ){ $barva="lime"; }
		elseif ( $plati == "min" ){ $barva="orange"; }
		elseif ( $plati == "jinak" ){ $barva="aqua"; }
	    
	    }
	    // $barva="red";
	    
	    echo "<tr> \n\n";
	    
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$datum."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$pole2."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$ucet."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$castka."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$mena."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$pole6."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" width=\"100px\" ><span class=\"vypis-bunky\" >".$pozn."</spam></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$jmeno."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$vs."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$pole10."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$pole11."</span></td>\n";
	    echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$vs2."</span></td>\n";
	    
	    // echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$datum."</span></td> \n";
	    
	    if ( !($duplicitni_zaznam == "true") )
	    {
		if ( $platby_polozky_rs == 1)
		{	echo "<td bgcolor=\"teal\" ><span class=\"vypis-bunky\" > OK </span></td>\n"; }
		else
		{  echo "<td bgcolor=\"red\" ><span class=\"vypis-bunky\" > E </span></td>\n"; }
	    
		// echo "<td> $datum_pridani </td>";
	    
		$promenna=$barva."*".$id_cloveka."*".$datum_pridani;
		
		//tady checkbox
		if ( ( ( ! ( $sparovano > 0 ) ) and ( !($duplicitni_vs == "true") ) and ( !( $nelze_priradit == "true" ) ) ) )
		{ 
		    // $promenna=$barva."*".$id_cloveka;
		    
		    echo "<td bgcolor=\"".$barva."\" ><input type=\"checkbox\" name=\"".$id_polozky."\" value=\"".$promenna."\" ></td>\n"; 
		}
	    
		// tady hidden data k duplicitni vs
		if ( $duplicitni_vs == "true" )
		{ 
		    echo "<td bgcolor=\"".$barva."\" ><input type=\"hidden\" name=\"".$id_polozky."\" value=\"".$promenna."\"></td>\n"; 
		}
		
	    }
	    echo "</tr>";
	    echo "<tr>";	
	    
	    // zde přiřazování plateb
	    if ( $sparovano > 0 )
	    { 
		echo "<td bgcolor=\"".$barva."\" colspan=\"12\" >";
		echo "<span style=\"color: white; font-weight: bold; \"> OK  - spárováno </span>";
		
		if ( $platby_rs == 1)
		{ echo ",<span style=\"color: white; \" > platba uložena </span>"; }
		else
		{ 
		    echo ",<span style=\"color: #FF9933; font-weight: bold; \" > platbu nelze uložit </span>";
		    echo ", ".pg_last_error($db_ok2);
		}
		
		echo "</td>\n"; 
	    }
	    elseif ( $duplicitni_zaznam == "true" )
	    { 
	      echo "<td colspan=\"3\" >";
	     //duplicitni zaznam - nevklada se 
	      
	     echo " Duplicitní záznam - ignoruji... ";
	     
	      echo "</td>\n";    
	    } // konec else
	    
	    echo "</tr>\n";
	    
        } //konec slozityho if
	 
	// nulovani promenych
	$duplicitni_zaznam="";
	$duplicitni_vs="";
	$sparovano="";
	$plati="";
	$vysledek="";
	$id_cloveka="";
	$barva="";
	$nelze_priradit="";
	$platby_polozky_rs="";
	$promenna="";
	
	} // konec while
    
	echo "<tr><td colspan=\"13\" ><br></td></tr>";
	
	echo "<tr><td colpsan=\"13\" ><hr width=\"100px\" align=\"left\"></td></tr>";
	
	echo "<tr><td colspan=\"3\" ><input type=\"submit\" name=\"odeslano2\" value=\"Další\" ></td></tr>";
	    
	echo "</table>"; 
        
	fclose ($file_csv);            //uzavře soubor
	
	echo "</form>";
	
    } // konec elseif  jestli se odeslalo1
    elseif ( isset($odeslano2) )
    {
    // druhotne parovani plateb
    
    echo "<br><H3> Ruční párování plateb </H3><br><br>";
    
    // echo "vypis promennych: <br><br>";
    echo "<form method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\" >";
    
    echo "<table width=\"100%\" border=\"1\" >";
    
    // nejdriv si pripravime pole s vlastnikama
		$dotaz_vlastnici=pg_query("SELECT * FROM vlastnici order by prijmeni ");
		$dotaz_vlastnici_radku=pg_num_rows($dotaz_vlastnici);
		
		if ( $dotaz_vlastnici_radku > 0 )
		{
		  while ( $data_vlastnici=pg_fetch_array($dotaz_vlastnici) ):
		
		    $id_cloveka=$data_vlastnici["id_cloveka"];
		    
		    $text .= $data_vlastnici["prijmeni"]." ".$data_vlastnici["jmeno"].", ".$data_vlastnici["ulice"];
		    $text .= ", VS: ".$data_vlastnici["vs"].", k_platbe: ".$data_vlastnici["k_platbe"].", id: ".$id_cloveka;
		    		    
		    $vlastnici[$id_cloveka]= $text;
		
		    $text="";
		    
		  endwhile;
		}
	    // echo "<tr>";

    // vypis vlastniku
    
    echo "<tr><td colspan=\"12\" >";
	 echo "<select name=\"\" size=\"5\" >";

    	 // echo "<option value=\"0\" > Vyberte vlastníka </optioně>";

    	    while ( list($id_vlastnika, $text) = each($vlastnici) ):

        	echo "<option value=\"".$id_vlastnika."\" >".$text."</option> \n";

	    endwhile;

  	 echo "</select>";

    echo "</td></tr>";

    echo "<tr><td colspan=\"12\" ><br></td></tr>";
        
    while ( list($id_polozky, $promenna) = each($_POST) ):
	    
	    unset($pole_promenna);
	    
	    $pole_promenna=explode("*",$promenna);
	        
	    $barva=$pole_promenna["0"];
	    $id_cloveka=$pole_promenna["1"];
	    $zaplaceno_za=$pole_promenna["2"];
	    
	    if ( ! ( $id_polozky == "odeslano2") )
	    {
	    	    
	     $dotaz=pg_query("SELECT * FROM platby_polozky WHERE id_polozky = '$id_polozky' ");
	     $dotaz_radku=pg_num_rows($dotaz);
	    
	     if ( $dotaz_radku == 0)
	     { echo "<tr><td> Záznam s id: ".$id_polozky." nenalezen! </td></tr>"; }
	     elseif ( $dotaz_radku != 1 )
	     { echo "<tr><td> Záznam s id: ".$id_polozky." nelze jednoznačne identifikovat! </td></tr>"; }
	     else
	     {
		while ($data=pg_fetch_array($dotaz) ):
			    
		echo "<tr>";
		$id_polozky=$data["id_polozky"];
		
	        echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["datum"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole2"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["ucet"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["castka"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["mena"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole6"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" width=\"100px\" ><span class=\"vypis-bunky\" >".$data["pozn"]."</spam></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["jmeno"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["vs"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole10"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole11"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["vs2"]."</span></td>\n";
		
		echo "</tr>";
		
		echo "<tr>";
		
		$vs=$data["vs"];
		
		// echo "<td><br></td>";
		
		/*
		$dotaz_id=pg_query("SELECT id_cloveka FROM vlastnici WHEDE vs = '$vs' ");
		$dotaz_id_radku=pg_num_rows($dotaz_id);
		
		if ( $dotaz_id_radku == 1)
		{
		    while ($data_id=pg_fetch_array($dotaz_id) ):
		        
		    endwhile;
		}
		
		*/
		
		echo "<td colspan=\"4\" >";
		
		if ( ! ( $id_cloveka > 0 ) )
		{ $id_cloveka=0; }
		
		echo " Pro spárování zadajte ID vlastníka ... ";
		    echo "<input type=\"text\" size=\"5\" name=\"".$id_polozky."\" value=\"".$id_cloveka."\" >";    
		
		echo "</td>";
		
		echo "<td colspan=\"5\" >";
		
		echo " zadejte období: ( RRRR-MM )  ";
		
		echo "<input type=\"text\" name=\"".$id_polozky."*\" value=\"".$zaplaceno_za."\" >";
		
		echo "</td>";
		
		echo "</tr>";
		
		endwhile;
		
	     } // konec else dotaz_raku == 0    
	        
	    // echo "</tr>";
	    
	    } // konec if ! id_platby == true
    
    endwhile;
    
    echo "<tr><td colspan=\"12\" ><br></td></tr>";
    
    echo "<tr><td colspan=\"13\" ><input type=\"submit\" name=\"odeslano3\" value=\"Další\" ></td></tr>";
    
    echo "</table>";
    
    echo "</form>";
    
    } // konec elseif isset(odeslano2)
    elseif( $odeslano3)
    {
	echo "<br><h3> Výsledky ručního párování </h3><br><br>";
	
	while ( list($id_polozky, $id_vlastnika) = each($_POST) ):
	    			 
	     $zaplaceno_za=$_POST[$id_polozky."*"];
	     
	     // $id_cloveka=$pole_promenna["1"];
	     // $zaplaceno_za=$pole_promenna["2"];
							     
	    if ( (  ( ! ( $id_polozky == "odeslano3") ) and ( $id_vlastnika > 0 ) and ( gettype($id_polozky) == "integer" ) ) )
	    {
	    
	    // echo "data: $id_polozky , $id_vlastnika <br>";
	    
	    $dotaz=pg_query("SELECT * FROM platby_polozky WHERE id_polozky = '$id_polozky' ");
	    $dotaz_radku=pg_num_rows($dotaz);
	    
	    echo "<table width=\"100%\" border=\"1\" >";
	     
	    if ( $dotaz_radku == 0)
	    { echo "<tr><td> Záznam s id: ".$id_polozky." nenalezen! </td></tr>"; }
	    elseif ( $dotaz_radku != 1 )
	    { echo "<tr><td> Záznam s id: ".$id_polozky." nelze jednoznačne identifikovat! </td></tr>"; }
	    else
	    {
		while ($data=pg_fetch_array($dotaz) ):
			    
		echo "<tr>";
		$id_polozky_select=$data["id_polozky"];
		
	        echo "<td bgcolor=\"".$barva."\" width=\"10%\" ><span class=\"vypis-bunky\">".$data["datum"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole2"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["ucet"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["castka"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["mena"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole6"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" width=\"100px\" ><span class=\"vypis-bunky\" >".$data["pozn"]."</spam></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["jmeno"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["vs"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole10"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["pole11"]."</span></td>\n";
        	echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["vs2"]."</span></td>\n";
		
		echo "<td> $zaplaceno_za </td>";
		
		echo "</tr>";
		
		echo "<tr>";
		
		
		//prvne konverze promennych
		
		$pole_castka=explode(".",$data["castka"] );
	    
	        $castka2=$pole_castka["0"];
		
		// predevem si datum
		$datum_pridani=$zaplaceno_za;
		
		// zde párování a vypsání výsledku
		$platby=array( "id_cloveka" => $id_vlastnika, "castka" => $castka2, 
		    "zaplaceno_za" => $datum_pridani, "zvypisu" => "1" );
		    
		$platby_rs=pg_insert($db_ok2,'platby',$platby);
		
		if ( $platby_rs == 1)
		{
		    // zjistime id platby
		    $dotaz_platby=pg_query("SELECT * FROM platby 
					    WHERE ( id_cloveka = '$id_vlastnika' AND castka = '$castka2' 
					    AND zaplaceno_za LIKE '$zaplaceno_za' ) ");
					    
		    $dotaz_platby_radku=pg_num_rows($dotaz_platby);
		    
		    if ( $dotaz_platby_radku != 1 )
		    { echo "<td> Platbu nelze najít / určit</td>"; }
		    else
		    {
			while( $data_platby=pg_fetch_array($dotaz_platby) ):
			
			    $id_platby=$data_platby["id"];
			    
			endwhile;
			
			// upravime polozku    
			$polozky_update = array ( "id_platby" => $id_platby );
		
			$polozky_update_id = array ( "id_polozky" => $id_polozky);
		
			$res=pg_update($db_ok2,'platby_polozky', $polozky_update, $polozky_update_id);
		
			echo "<td colspan=\"12\" bgcolor=\"green\"><span style=\"color: white; font-weight: bold; \">OK - spárováno</span>";
		    
			if ( $platby_rs == 1)
			{ echo ",<span style=\"color: white; \" >, platba uložena </span>"; }
			else 
			{ echo ",<span style=\"color: white; \" >, platbu nelze uložit </span>"; }
			
			if ( $res == 1)
			{ echo ",<span style=\"color: white; \" >, Položka platby upravena </span>"; }
			else 
			{ echo ",<span style=\"color: white; \" >, Položka platby nelze upravit </span>"; }
					    
			echo "</td>";
		     } // konec else dotaz_platby_radku > 0 
		
		} // konec if platby_rs == 1
		else
		{ 
		    echo "<td colspan=\"12\" >";
		    echo "<span style=\"color: red; font-weight: bold; \" >Položku se nepodařilo spárovat s platbou </span>";
		
		    // echo "castka: $castka2 ";
		    
		    $error = pg_last_error($db_ok2);
		    echo $error;
		     
		    echo "</td>"; 
		}
		
		echo "</tr>";
		
		endwhile;
		
	    } // konec else
	    	
	    echo "</table>";
	    
	    } // konec if ! id_polozky == odeslano3
	    endwhile;
	    			
	    echo "<br><br><h4> Konec </h4>";
    
	    echo "Děkujeme za použití modulu plateb ze systému \"ADMINATOR II\". Případné dotazy směřujte do /dev/null.   :)";
	    
    } // konec elseif odeslano3
    
  ?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>

