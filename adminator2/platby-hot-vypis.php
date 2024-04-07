<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if( !( check_level($level,44) ) )
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

<title>Adminator2 - platby výpis</title> 

</head>

<body>

<? include ("head.php"); ?>

<? include ("category.php"); ?>

 <tr>
   <td colspan="2" ><? include("platby-subcat-inc2.php"); ?></td>
 </tr>
     
 <tr>
  <td colspan="2" >
  
  <?
    // sem zbytek
    
    include "./include/c_listing_platby.php";    //předpokládáme třídu uloženou v externím souboru
    
//    include ("include/config.pg.php");

    $list=$_GET["list"];
        
    //vytvoreni objektu
    $listovani = new c_Listing("./platby-hot-vypis.php?menu=1", 30, $list, "<center><div class=\"text-listing\">\n", "</div></center>\n", 
                    "SELECT * FROM platby WHERE hotove='1' ORDER BY id ; ");
		    
    if (($list == "")||($list == "1")){    //pokud není list zadán nebo je první
    $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
    }
    else{
    $bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
        }
				    

    //provedení sql dotazu a výběr záznamů
    $vyber = pg_query("SELECT t1.id, t1.zaplaceno_dne, t2.prijmeni, t2.jmeno, t2.id_cloveka,
    t1.firma, t1.zaplaceno_za, t1.castka, t1.id_cloveka 
    FROM (platby AS t1 LEFT JOIN vlastnici AS t2 
    ON t1.id_cloveka=t2.id_cloveka) WHERE hotove='1' ORDER BY id LIMIT ".$listovani->interval." OFFSET ".$bude_chybet." ");
    
    //pro mysql:
    // LIMIT ".$bude_chybet.",".$listovani->interval." ");
					     
     $listovani->listInterval();    //zobrazení stránkovače
      
        
     echo "<table border=\"1\" width=\"100%\" >
     <tr>
     
     <td><b>id platby: </b></td>
     <td><b>zaplaceno za: </b></td>
     <td><b>částka: </b></td>
     <td><b>datum placení: </b></td> 
         
     <td><b>id vlastníka: </b></td>
     <td><b>firma</b></td>
     
     <td><b>Příjmení: </b></td>
     <td><b>Jméno: </b></td>
     </tr>
     ";
      
     //výpis záznamů dokud nějaké jsou
     while ( $zaznam = pg_fetch_array($vyber) )
     {
	 $orez= $zaznam["zaplaceno_dne"];
	 $orezano = split(':', $orez);
	 $pridano_orez=$orezano[0].":".$orezano[1];
		 
	// $id_cloveka=$id_cloveka=["id_cloveka"];
	echo "<tr>";
	
	echo "<td> ".$zaznam["id"]."</td>"."<td>".$zaznam["zaplaceno_za"]."</td>"."<td>".$zaznam["castka"]."</td> ";
	echo "<td>".$pridano_orez." </td>";
	
	echo "<td>".$zaznam["id_cloveka"]."</td>";
	echo "<td>".$zaznam["firma"]." </td> "."<td> ".$zaznam["prijmeni"]." </td> "." <td>".$zaznam["jmeno"]." </td> ";
	
	// , ".$zaznam["t1.zaplaceno_dne"]."<br><br>\n";
	
    	echo "</tr>";
	
     }
	
    echo "</table>";
    
    $listovani->listInterval();    //zobrazení stránkovače
 
  ?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>

