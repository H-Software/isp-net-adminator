<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/class.php"); 
require("include/check_login.php");
require("include/check_level.php");

// require("include/c_listing-vlastnici.php");

if( !( check_level($level,13) ) )
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

include("include/charset.php");

?>

<title>Adminator2 - vlastníci</title>

</head>

<body>

<? include ("head.php"); ?>

<? include ("category.php"); ?>

 <tr>
  <td colspan="2" height="20" bgcolor="silver" >
   <? include("vlastnici-cat-inc.php"); ?>
  </td>
 </tr>

<tr>
   <td colspan="2" height="25" >
      <span style="font-weight: bold; font-size: 14px; " >Vlastníci - FO, Martin Lopušný</span></td>
</tr>

  <tr>
  <td colspan="2">

   <div style="margin-top: 2px; margin-left: 2px; " ><hr width="22%" align="left" ></div>
  
   <form method="GET" action="">
    
   <input type="radio" name="select" value="1" 
    <? if ( ( !( isset($GET["select"] ) ) or ($_GET["select"] == 1 ) ) ){ echo " checked "; } ?> 
    ><label>Všichni</label> |
    
    <input type="radio" name="select" value="2" 
    <? if ( $_GET["select"] == 2){ echo " checked "; }  ?> 
    ><label>Fakturační</label> |
    
    <input type="radio" name="select" value="3" 
    <? if ( $_GET["select"] == 3 ){ echo " checked "; } ?>
    ><label>Nefakturační</label> |

    <input type="radio" name="select" value="4" 
    <? if ( $_GET["select"] == 4 ){ echo " checked "; } ?>
    ><label> Neplatí (free) </label> |
       
    <input type="radio" name="select" value=5" 
    <? if ( $_GET["select"] == 5 ){ echo " checked "; } ?>
    ><label> Platí </label> |  
    
    <span style="padding-left: 5px; padding-right: 5px; ">Řadit dle:</span>
    
    <select name="razeni" size="1" >
    
	<option value="1" <? if ( ( $_GET["razeni"] == 1) or !isset($_GET["razeni"]) ) { echo " selected "; } ?> > id klienta  </option>
	<option value="3" <? if ($_GET["razeni"] == 3) { echo " selected "; } ?> > jména  </option>
	<option value="4" <? if ($_GET["razeni"] == 4) { echo " selected "; } ?> > Příjmení  </option>
	<option value="5" <? if ($_GET["razeni"] == 5) { echo " selected "; } ?> > Ulice  </option>
	<option value="6" <? if ($_GET["razeni"] == 6) { echo " selected "; } ?> > Město  </option>
	<option value="14" <? if ($_GET["razeni"] == 14) { echo " selected "; } ?> > Var. symbol  </option>
	<option value="15" <? if ($_GET["razeni"] == 15) { echo " selected "; } ?> > K platbě  </option>

    </select>
    <span style="padding-left: 7px; "></span>
    <select name="razeni2" size="1" >
    
	<option value="1" <? if ($_GET["razeni2"] == 1) { echo " selected "; } ?> > vzestupně  </option>
	<option value="2" <? if ($_GET["razeni2"] == 2) { echo " selected "; } ?>  > sestupně  </option>
	
    </select>

  <div style="margin-top: 2px; margin-left: 2px; " ><hr width="22%" align="left" ></div>
   
  <div style="padding-left: 2px; ">   
    <span style="padding-left: 5px; " ><input type="submit" value="NAJDI" name="najdi"></span>
    <span style="padding-left: 40px; " ><label>Hledání : </label><input type="text" name="find" 
	<? 
	echo 'value="';
	
	if( (strlen($_GET["find"]) < 1 ) ){ echo "%"; }
	else{ echo $_GET["find"]; }
	
	echo '" >'; 
	?>

    </form>
    </span>
    
  </div>

  <div style="margin-top: 2px; margin-left: 2px; " ><hr width="22%" align="left" ></div>
	
   <?  
     echo "<div style=\"padding-bottom: 5px; \">";
	    
        echo "<span style=\"padding-left: 20px; \" >";
	
	 if ( check_level($level,40) ) { echo '<a href="vlastnici2-change.php?firma_add=2">Přidání vlastníka</a>'; }
	 else { echo "<div style=\"color: grey; font-style: italic\">Přidání vlastníka</div>"; }
    
	echo "</span>\n\n";

    if ( check_level($level,64) ){ $export_povolen="true"; }

    // tafy generovani exportu

    if ( $export_povolen)
    {

    $fp=fopen("export/vlastnici.xls","w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor

    fputs($fp,"<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky

    fputs($fp,"<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

    $vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
    // Vybereme z databáze názvy polí tabulky tabulka a postupně je zapíšeme do souboru

    // echo "vysledek_pole: $vysledek_pole ";

     while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
     {
        fputs($fp,"<td><b> ".$vysledek_array_pole[0]." </b></td> \n");
     }

       fputs($fp,"<td><b> id_f </b></td> \n");
       fputs($fp,"<td><b> f. jméno </b></td> \n");
       fputs($fp,"<td><b> f. ulice </b></td> \n");
       fputs($fp,"<td><b> f. mesto </b></td> \n");
       fputs($fp,"<td><b> f. PSČ </b></td> \n");
       fputs($fp,"<td><b> f. ičo </b></td> \n");
       fputs($fp,"<td><b> f. dič </b></td> \n");
       fputs($fp,"<td><b> f. účet </b></td> \n");
       fputs($fp,"<td><b> f. splatnost </b></td> \n");
       fputs($fp,"<td><b> f. cetnost </b></td> \n");

        fputs($fp,"</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

        $vysledek = pg_query("SELECT * FROM vlastnici WHERE ( (firma is null) AND ( archiv = 0 OR archiv is null ) ) ORDER BY id_cloveka ASC ");

        while ( $data=pg_fetch_array($vysledek) )
        {
          fputs($fp,"\n <tr>");

          fputs($fp,"<td> ".$data["id_cloveka"]."</td> ");
          fputs($fp,"<td> ".$data["nick"]."</td> ");
          fputs($fp,"<td> ".$data["jmeno"]."</td> ");
          fputs($fp,"<td> ".$data["prijmeni"]."</td> ");
          fputs($fp,"<td> ".$data["ulice"]."</td> ");
          fputs($fp,"<td> ".$data["mesto"]."</td> ");
          fputs($fp,"<td> ".$data["psc"]."</td> ");
          fputs($fp,"<td> ".$data["icq"]."</td> ");
          fputs($fp,"<td> ".$data["mail"]."</td> ");
          fputs($fp,"<td> ".$data["telefon"]."</td> ");
          fputs($fp,"<td> ".$data["poznamka"]."</td> ");
          fputs($fp,"<td> ".$data["zaplaceno"]."</td> ");
          fputs($fp,"<td> ".$data["fakturacni"]."</td> ");
          fputs($fp,"<td> ".$data["vs"]."</td> ");
          fputs($fp,"<td> ".$data["k_platbe"]."</td> ");
          fputs($fp,"<td> ".$data["firma"]."</td> ");

          fputs($fp,"<td> ".$data["pridano"]."</td> ");
          fputs($fp,"<td> ".$data["ucetni_index"]."</td> ");
          fputs($fp,"<td> ".$data["archiv"]."</td> ");
          fputs($fp,"<td> ".$data["fakturacni_skupina_id"]."</td> ");
          fputs($fp,"<td> ".$data["splatnost"]."</td> ");
          fputs($fp,"<td> ".$data["typ_smlouvy"]."</td> ");
          fputs($fp,"<td> ".$data["trvani_do"]."</td> ");
          fputs($fp,"<td> ".$data["datum_podpisu"]."</td> ");

          fputs($fp,"<td> ".$data["sluzba_int"]."</td> ");
          fputs($fp,"<td> ".$data["sluzba_iptv"]."</td> ");
          fputs($fp,"<td> ".$data["sluzba_voip"]."</td> ");
          fputs($fp,"<td> ".$data["sluzba_int_id_tarifu"]."</td> ");
//          fputs($fp,"<td> ".$data["datum_podpisu"]."</td> ");

	  if ( $data["fakturacni"] > 0 )
          {
            $id_f=$data["fakturacni"];

            $vysl_f=pg_query("SELECT * FROM fakturacni WHERE id = '$id_f' ");

            while ( $data_f=pg_fetch_array($vysl_f) )
            {

            fputs($fp,"<td> ".$data_f["id"]."</td> ");
            fputs($fp,"<td> ".$data_f["ftitle"]."</td> ");
            fputs($fp,"<td> ".$data_f["fulice"]."</td> ");
            fputs($fp,"<td> ".$data_f["fmesto"]."</td> ");
            fputs($fp,"<td> ".$data_f["fpsc"]."</td> ");
            fputs($fp,"<td> ".$data_f["ico"]."</td> ");
            fputs($fp,"<td> ".$data_f["dic"]."</td> ");
            fputs($fp,"<td> ".$data_f["ucet"]."</td> ");
            fputs($fp,"<td> ".$data_f["splatnost"]."</td> ");
            fputs($fp,"<td> ".$data_f["cetnost"]."</td> ");

            }

          }
	
	  fputs($fp,"</tr> \n ");
          // echo "vysledek_array: ".$vysledek_array[$i];

        } //konec prvniho while

        fputs($fp,"</table>");   // Zapíšeme do souboru konec tabulky

        fclose($fp);   // Zavřeme soubor

	echo "<span style=\"padding-left: 20px; \" >";
          echo '<a href="export\vlastnici.xls" >export dat zde</a>';
	echo "</span>";
	
        echo "</div>\n\n";

	}
	

$find_id=$_GET["find_id"];
$find=$_GET["find"];

if( ( strlen($find_id) > 0 ) )
{ $co=3; /* hledani podle id_cloveka */	$sql=$find_id;	}
elseif ( ( strlen($find) > 0 ) )
{ $co=1; /* hledani podle cehokoli */  $sql=$find;  }
else
{ /* cokoli dalsiho */ }

   //promena pro update objektu
 
   if( check_level($level,29) ) { $update_povolen="true"; }
      
   if( check_level($level,33) ) { $mazani_povoleno="true"; }
	    
   if( check_level($level,34) ) { $garant_akce="true"; }
		  
// promene pro upravu vlastniku
    if( check_level($level,45) ) { $vlastnici_erase_povolen="true"; }
    
    if( check_level($level,30) ) { $vlastnici_update_povolen="true"; }

// odendani povoleno
    if( check_level($level,49) ){ $odendani_povoleno="true"; }
    		  

  // co - co hledat, 1- podle dns, 2-podle ip , 3 - dle id_vlastnika
  
  if ( $co==1 )
  {
	  
      $sql="%".$sql."%";
      $select1 = " WHERE firma is NULL AND ( archiv = 0 or archiv is null ) AND ";
      $select1 .= " ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' ";
      $select1 .= " OR ulice LIKE '$sql' OR mesto LIKE '$sql' OR poznamka LIKE '$sql' ";
      
      $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' ";
      $select2 .= "OR vs LIKE '$sql') ";

      if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
      if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
      if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
      if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }
      
    if ( $_GET["razeni"] == 1){ $select4=" order by id_cloveka "; }
    if ( $_GET["razeni"] == 3){ $select4=" order by jmeno "; }
    if ( $_GET["razeni"] == 4){ $select4=" order by prijmeni "; }
    if ( $_GET["razeni"] == 5){ $select4=" order by ulice "; }
    if ( $_GET["razeni"] == 6){ $select4=" order by mesto "; }
    if ( $_GET["razeni"] == 14){ $select4=" order by vs "; }
    if ( $_GET["razeni"] == 15){ $select4=" order by k_platbe "; }
    
    if ( $_GET["razeni2"] == 1){ $select5=" ASC "; }
    if ( $_GET["razeni2"] == 2){ $select5=" DESC "; }
                
    if ( (strlen($select4) > 1 ) ){ $select4=$select4.$select5; }
    
      $dotaz_source = " SELECT * FROM vlastnici ".$select1.$select2.$select3.$select4;
						  
  }
  elseif ( $co ==3)
  {  $dotaz_source= "SELECT * FROM vlastnici WHERE id_cloveka = '" . intval($sql) ."' AND firma is null AND ( archiv = 0 or archiv is null )"; }
  else  
  { echo "<div style=\"padding-top: 20px; padding-bottom: 20px; \">Zadejte výraz k vyhledání.... </div>"; exit; }
										      
     
    global $list;
       
    $list=$_GET["list"];
	 
    $poradek="find=".$find."&find_id=".$find_id."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".$_GET["razeni"]."&razeni2=".$_GET["razeni2"];
    
    //vytvoreni objektu
    $listovani = new c_listing_vlastnici("./vlastnici.php?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $dotaz_source);
       
   if (($list == "")||($list == "1")){ $bude_chybet = 0; }
   else{ $bude_chybet = (($list-1) * $listovani->interval); }
					   
   $interval=$listovani->interval;
					       
   $dotaz_final=$dotaz_source." LIMIT ".$interval." OFFSET ".$bude_chybet." ";
						   			   
   $listovani->listInterval();
						     
  $vlastnik = new vlastnik;
  $vlastnik->conn_mysql = $conn_mysql;
  $vlastnik->conn_pgsql = $db_ok2;

  $vlastnik->vypis_tab(1);

  $vlastnik->vypis($sql,$co,0,$dotaz_final);
  
  $vlastnik->vypis_tab(2);

 $listovani->listInterval();    


?>
    
    <!-- konec hlavni tabulky -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

