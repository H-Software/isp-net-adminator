<?php

include ("include/config.php");
include ("include/check_login.php");

include ("include/check_level.php");

if( !( check_level($level,72) ) )
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

<?php include ("head.php"); ?>

<?php include ("category.php"); ?>

 <tr>
   <td colspan="2" ><? include("platby-subcat-inc2.php"); ?></td>
 </tr>
    
 <tr>
  <td colspan="2">
  
<?php
  
    // zacatek vlastniho obsahu
    
    $rok=$_POST["rok"];
    $start=$_POST["start"];
    $stop=$_POST["stop"];
    $odeslano=$_POST["odeslano"];
    $typ=$_POST["typ"];
    $rezim=$_POST["rezim"];
    
    // include ("include/config.pg.php");
    
    echo "<form method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\" >";
    
    echo "<table width=\"80%\" border=\"1\" > <tr>";
    
	echo "<td> Vyberte rok: </td>";
    
	echo "<td>";
	    echo "<select size=\"1\" name=\"rok\" >";
    
		echo "<option value=\"2005\" "; if ($rok == 2005){ echo " selected "; } echo " >2005</option>";
		echo "<option value=\"2006\" "; if ($rok == 2006){ echo " selected "; } echo " >2006</option>";
		echo "<option value=\"2007\" "; if ($rok == 2007){ echo " selected "; } echo " >2007</option>";
		echo "<option value=\"2008\" "; if ($rok == 2008){ echo " selected "; } echo " >2008</option>";

		echo "<option value=\"2009\" "; if ($rok == 2009){ echo " selected "; } echo " >2009</option>";
		echo "<option value=\"2010\" "; if ($rok == 2010){ echo " selected "; } echo " >2010</option>";
		echo "<option value=\"2011\" "; if ($rok == 2011){ echo " selected "; } echo " >2011</option>";
		echo "<option value=\"2012\" "; if ($rok == 2012){ echo " selected "; } echo " >2012</option>";

		// echo "<option value=\"\"></option>";
	    echo "</select>";
	    
	echo "</td>";
	
	echo "<td> měsíc/e: </td>";
    
	echo "<td> Od: </td>";
	
	echo "<td>";
	
	echo "<select size=\"1\" name=\"start\" >";
	
	for ($i=1;$i<13;$i++)
	{
	    echo "<option value=\"".$i."\" "; 
	    if ( $start == $i){ echo " selected "; }
	    echo " >".$i."</option>";
	}
	
	echo "</select>";
	
	echo "</td>";
	
	echo "<td> Do: </td>";
	
	echo "<td>";
	
	echo "<select size=\"1\" name=\"stop\" >";
	
	for ($i=1;$i<13;$i++)
	{
	    echo "<option value=\"".$i."\" ";
	    if ( $stop == $i){ echo " selected "; }
	    echo " >".$i."</option>";
	}
	
	echo "</select>";
	
	echo "</td>";
	
	echo "<td> Položky: </td>";
	
	echo "<td>";
	
	    echo "<select name=\"typ\" size=\"1\" >";
	    
		echo "<option value=\"0\" "; if ($typ == 0){ echo " selected "; } echo " > všechny </option>";
		echo "<option value=\"1\" "; if ($typ == 1){ echo " selected "; } echo " > nepřiřazené </option>";
		echo "<option value=\"2\" "; if ($typ == 2){ echo " selected "; } echo " > přiřazené </option>";
	    
	    echo "</select>";
	    
	echo "</td>";
	
	echo "<td>";
	
	echo "režim: ";
	
	echo "</td>";
	
	echo "<td>";
	
	    echo "<select size=\"1\" name=\"rezim\" >";
	
		echo "<option value=\"1\" "; if ($rezim == 1){ echo " selected  "; } echo "> Párování </option>";
		echo "<option value=\"2\" "; if ($rezim == 2){ echo " selected  "; } echo "> Od-párování </option>";
	    
	    echo "</select>";
	echo "</td>";
	
	echo "<td><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></td>";
	
    echo "</tr></table>";
    
    echo "</form>";
    
    if ( isset($odeslano) )
    {
    // sem vlastni vypis
	
	if ( $start > $stop)
	{ echo " debile "; }
	
	for ( $i=$start;$i <= $stop; $i++)
	{
	    $datum=$i.".".$rok;
	    if ( $i == $start)
	    { $select .= " WHERE  ( ( datum LIKE '%$datum' "; }
	    else
	    { $select .= " OR datum LIKE '%$datum' "; }
	       
	    if ( $i == $stop)
	    { $select .= " ) "; }
	
	}
	
	if ($typ == 1)
	{ $select .= " AND ( id_platby IS NULL ) )"; }
	elseif( $typ == 2 )
	{ $select .= " AND ( id_platby IS NOT NULL ) )"; }	
	else
	{ $select .= " ) "; }
	
	if ( $typ == 1)
	{ $order =" order by vs "; }
		
	$dotaz=pg_query("SELECT * FROM platby_polozky ".$select.$order );
	$dotaz_radku=pg_num_rows($dotaz);
	
	if ( $dotaz_radku == 0)
	{
	echo "<br><H3><span style=\"color: red; \"> Žádné položky dle výběru nenalezeny! </span></H3> <br><br>";
	}
	else
	{
	
	    
	    echo "<br><b>počet řádků:</b> ".$dotaz_radku." <br><br><br>";
	    
	    if ( $rezim == 1)
	    { echo "<form method=\"POST\" action=\"platby-ucet-import.php\" >"; }
	    
	    echo "<table border=\"1\" width=\"\" >";
	    
		echo "<tr>";
		    
		    echo "<td><b> datum </b></td>";
		    echo "<td><b> pole2 </b></td>";
		    echo "<td><b> úcet </b></td>";
		    echo "<td><b> částka </b></td>";
		    echo "<td><b> měna </b></td>";
		    echo "<td><b> pole6 </b></td>";
		    echo "<td><b> pozn </b></td>";
		    echo "<td><b> jméno </b></td>";
		    echo "<td><b> vs </b></td>";
		    echo "<td><b> pole10 </b></td>";
		    echo "<td><b> pole11 </b></td>";
		    echo "<td><b> vs2 </b></td>";
		    echo "<td><b> id_polozky </b></td>";
		    echo "<td><b> id_platby </b></td>";
		    echo "<td><b> spárování </b></td>";
		    
		    echo "<td><b> Odpárování</b></td>";
		    
		echo "</tr>";
		
	    while ( $data=pg_fetch_array($dotaz) ):	
	
		// echo "select je: ".$select;
	    
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
		echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">".$data["id_polozky"]."</span></td>\n";
                echo "<td bgcolor=\"".$barva."\" ><span class=\"vypis-bunky\">";
		    echo "<a href=\"platby-ucet-vypis-detail.php?id_platby=".$data["id_platby"]."\" >".$data["id_platby"]."</a>";
		    
		echo "</span></td>\n";
		
		if ( ! ( $data["id_platby"] > 0 ) )
		{
		  echo "<td>";
		    
		    // predevem si datum
		    $pole_datum=explode(".",$data["datum"] );
		    
		    $mesic=$pole_datum["1"];
		    if ( $mesic < 10 ){ $mesic="0".$mesic; }
				    
		    $zaplaceno_za=$pole_datum["2"]."-".$mesic;
										    
		    if ( $rezim == 1 )
		    { echo "<input type=\"checkbox\" name=\"".$id_polozky_select."\" value=\"#66FF99*0*".$zaplaceno_za."\" >"; }
		    
		  echo " </td>";
		}
		else
		{ 
		    echo "<td><br></td>"; 
		    
		    if ( $rezim == 2)
		    {
		      echo "<td><form action=\"platby-ucet-zruseni.php\" method=\"POST\" >";
		    
		      echo "<input type=\"hidden\" name=\"".$id_polozky_select."\" value=\"".$data["id_platby"]."\" >";
		    
		      echo "<input type=\"submit\" name=\"odradit\" value=\"Odřadit\" >";
		    
		      echo "</td></form>";
		    }
		}
				
		echo "</tr>";

	    endwhile;
	    
	    echo "<tr><td colspan=\"15\"><br></td></tr>";
	
	    echo "<tr><td colspan=\"3\" align=\"center\" >";
	    if ( $rezim == 1 ) { echo "<input type=\"submit\" name=\"odeslano2\" value=\"Spárovat označené\" >"; }
	    echo "</td></tr>";
	    
	    echo "</table>";
	    
	    if ( $rezim == 1 ){ echo "</form>"; }
	    
	} // konec else dotaz_radku == 0
    
    
    } // konec if isset odeslano
    else
    { echo "<b><H3>Vyberte období ... </b></H3>"; }
    
  ?>
  
  </td>
  </tr>
    
 </table>

</body>
</html>
