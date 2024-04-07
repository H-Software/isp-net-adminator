<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,52) ) )
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

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 <tr>
  <td colspan="2" height="50" bgcolor="silver">
  <? include("admin-subcat2-inc.php"); ?>
  </td>       
 </tr>
 
 <tr>
  <td colspan="2">
  
   <?
  
  $dotaz=pg_query("SELECT * FROM vlastnici WHERE ( (archiv IS NULL) OR (archiv = 0) )order by id_cloveka ASC");	      
  $dotaz_radku=pg_num_rows($dotaz);
  
  if ( $dotaz_radku == 0)
  {
    echo "Žádné údaje v databázi! ";
  }
  else
  {
	global $ma_platit;
	global $garant;
	
	echo "<table border=\"1\" width=\"100%\" >";
	
	echo "<tr><td><b>Jmeno Příjmení</b></td>";
	echo "<td><b>VS </b></td>";
	echo "<td><b>k_platbe</b></td>";
	
	echo "<td><b>kontrola: </b></td>";
	echo "<td><b>má platit: </b></td>";
	
	echo "<td><b>okrádá nás: </b></td>";
	
	while ( $data = pg_fetch_array($dotaz) )
	{
	
		
	    $id_cloveka=$data["id_cloveka"];
	    
	    $dotaz2=pg_query("SELECT * FROM objekty where id_cloveka='$id_cloveka' ");
	    $dotaz_radku2=pg_num_rows($dotaz2);
	
	    if ( $dotaz_radku2 > 0)
	    {
	    
	      $ma_platit = 0;
	      $error = 0;
	      $garant = 0;
	      $poplach = 0;
	    
		while ( $data2=pg_fetch_array($dotaz2) )
		{
		    //pokud je objekt verejna a NO-free, tak pridat k platbe
		    if( ( ($data2["verejna"] != 99) AND ( $data2["typ"] == 1 ) ) ){ $ma_platit = $ma_platit + 99.2; }
		    	
		    if( $data2["typ"] == 3 ) { $ma_platit = $ma_platit; } //ap-cko
		    elseif( $data2["typ"] == 2 ){ $ma_platit = $ma_platit; } //free objekt
		    elseif( $data2["tunnelling_ip"] == 1 ){ $ma_platit = $ma_platit; } //tunelovaná ip, jako objekt neplatit, jako priplatek viz vyse
		    elseif( $data2["dov_net"] == "n" ){ $ma_platit = $ma_platit; }  //netN
		    else 
		    { 
			//dle tarifu
			$rs_tarify = mysql_query("SELECT cena_bez_dph FROM tarify_int WHERE id_tarifu = '".intval($data2["id_tarifu"])."'");
			$rs_tarify_num = mysql_num_rows($rs_tarify);
			
			if( $rs_tarify_num == 1)
			{
			    while($data_tarify = mysql_fetch_array($rs_tarify))
			    { $ma_platit = $ma_platit + intval($data_tarify["cena_bez_dph"]); }
			}
			else
			{ /* nelze najit tarif - CHYBA */ }
		    }
		
		
		} //konec while
			    
	      //okrada nas
	      if ( $data["k_platbe"] < $ma_platit )
	      { 
	        
	        //sekundartni kontrola zda nás okrádá
	        if( $data["fakturacni_skupina_id"] > 0)
	        {
	          //nastavena FA. skupinu, muzem zjistovat dál
	            
	          //MP za cenu SC, sleva za napajeni
	          if( 
	              ($data["k_platbe"] == 248) 
	              and 
	              ($ma_platit = "416.5") 
	              and
	              ($data["fakturacni_skupina_id"] == 43)
	          )
	          { 
	            //má MP se slevou za napájení
	    	    $poplach = 0;    
	          }
	          else
	          { $poplach = 1; }
	        
	          //fakturacni skupiny - internet-zdarma
	    	  if( ($data["fakturacni_skupina_id"] == 60)
	    		or
	    		($data["fakturacni_skupina_id"] == 61)
	    	    )
	    	  {
	    	    //fs. internet-zdarma, tj. neplatí nic at se stav objektů jakojkoliv
	    	    $poplach = 0;	
	          }
	          
	        }
	        else
	        { 
	    	    //neni FS, tj. neni dle ceho druhotne overovat
	    	    $poplach= 1; 
	    	}
	        	        
	        //pokud okrada, tak poplach = 1
	        //$poplach= 1; 
	      
	      }
	      else
	      { 
	        //neokrádá
	        $poplach=0; 
	      }
	
	
	      flush(); 
	      ob_flush();
	       
	    }	
	
	if ( $poplach == 1)
	{
	
	 echo "<tr><td>".$data["jmeno"]."  ".$data["prijmeni"]."</td>";
	 
	 echo "<td> ".$data["vs"]."</td>";
	 echo "<td> ".$data["k_platbe"]."</td>";
	
	 if ( $dotaz_radku2 == 0)
	 { echo "<td> 0 objects </td>"; }
	 else
	 {
	 	     		 
	    // kontrola		 
	    echo "<td> Ano </td>";
		
	    // ma platit
	    echo "<td> ".$ma_platit." </td>";
	
	    if ($dotaz_radku2 > 0 )
	    {
		// okrada nas
		if ( $garant == 1 )
		{ echo "<td> nelze zjistit </td>"; }
		elseif ( $data["k_platbe"] < $ma_platit )
		{
		echo "<td><span style=\"color: red; \" > Ano </span></td>";
		}
		else
		{ echo "<td> Ne </td>"; }
																 
	    } // konec if
	
	  } // konec else	
	
	} // konec if poplach 1
	
	} // konec while
        
	echo "</table>";
	
  } // konec else radku == 0
    
  ?>

   </td>
  </tr>
  
 </table>

</body> 
</html> 

