<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/class.php"); 
require("include/check_login.php");
require("include/check_level.php");
// require("include/c_listing-vlastnici2.php");

if ( !( check_level($level,82) ) )
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

<title>Adminator 2 - archiv vlastníků</title> 

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
 <td colspan="2"><span style="font-weight: bold; " > Výpis odpojených klientů </span></td>
  </tr>
  
  <tr>
  <td colspan="2">
  
 
     <form method="GET" <? echo 'action="'.$_SERVER["PHP_SELF"].'">'; ?>

      <table width="80%" height="80" border="0">


     <tr>
         <td colspan="2"> <hr width="30%" align="left" > </td>
    </tr>

   <tr>
    <td colspan="2">

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
    ><label> Neplatí(free) </label> |

    <input type="radio" name="select" value="5"
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

    </td>
   </tr>


     <tr>
         <td colspan="2"> <hr width="30%" align="left" > </td>
    </tr>


          <tr>
            <td> <input type="submit" value="NAJDI" name="najdi"> </td>
            <td>  <label>Hledání : </label><input type="text" name="find"
            <?
            if (empty($_GET["find"]) )  { echo 'value="%"'; }
            else {  echo 'value="'.$_GET["find"].'" >'; }
            ?>
            </td>
          </tr>

          <tr>
            <td colspan="2">
             <hr width="30%" align="left">

                <table border="0" width="50%" >
                 <tr><td>
        <?

            echo "</td>";

    if ( check_level($level,82) ){ $export_povolen="true"; }

    // tafy generovani exportu

    if ( $export_povolen)
    {

    $fp=fopen("export/vlastnici-archiv.xls","w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor

    fputs($fp,"<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky

    fputs($fp,"<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

    $vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
    // Vybereme z databáze názvy polí tabulky tabulka a postupně je zapíšeme do souboru

    // echo "vysledek_pole: $vysledek_pole ";

     while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
      {  fputs($fp,"<td><b> ".$vysledek_array_pole[0]." </b></td> \n"); }

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

        // $vysledek=pg_query("select * from platby where hotove='1' ");
        // Vybereme z databáze všechny záznamy v tabulce tabulka a postupě je zapíšeme do souboru

        $vysledek = pg_query("SELECT * FROM vlastnici WHERE archiv='1' ORDER BY id_cloveka ASC");

        while ($data=pg_fetch_array($vysledek) )
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


    if ( $data["fakturacni"] > 0 )
          {
            $id_f=$data["fakturacni"];

            $vysl_f=pg_query("SELECT * FROM fakturacni WHERE id = '".intval($id_f)."' ");

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

          }

        fputs($fp,"</table>");   // Zapíšeme do souboru konec tabulky

        fclose($fp);   // Zavřeme soubor

        echo "<td>";

        echo '<a href="export\vlastnici-archiv.xls" >export dat zde</a>';

        echo "</td><td>";

        // include("include/export-ucetni.php");


        echo "</td></tr></table>";

    }


        ?>

          </td>
         </tr>

         </table>
        </form>

        <?

        $find_id=$_GET["find_id"];
        $find=$_GET["find"];

        // $delka_find_id=strlen($find_id);
        if ( ( strlen($find_id) > 0 ) )
        { $co=3; /* hledani podle id_cloveka */   $sql=$_GET["find_id"];  }

        elseif ( ( strlen($find) > 0 ) )
        { $co=1;  /* hledani podle cehokoli */  $sql=$_GET["find"];  }

        else
        { /* cokoli dalsiho */ }

        //promena pro update objektu
         if ( check_level($level,29) ) { $update_povolen="true"; }
         if ( check_level($level,33) ) { $mazani_povoleno="true"; }
         if ( check_level($level,34) ) { $garant_akce="true"; }

        // promeny pro mazani, zmenu vlastniku
         if ( check_level($level,45) ) { $vlastnici_erase_povolen="true"; }
         if ( check_level($level,30) ) { $vlastnici_update_povolen="true"; }

        // odendani objektu od vlastnika
        if ( check_level($level,49) ){ $odendani_povoleno="true"; }

        vlastnikarchiv::vypis_tab(1);

         if ( $co==1)
         {

                $sql="%".$sql."%";
                $select1=" WHERE archiv = '1' AND ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' OR mesto LIKE '$sql' ";
                $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' OR vs LIKE '$sql' ) ";

                if ( $_GET["select"] == 2){ $select3=" AND fakturacni > 0 "; }
                if ( $_GET["select"] == 3){ $select3=" AND fakturacni is NULL "; }
                if ( $_GET["select"] == 4){ $select3=" AND k_platbe = 0 "; }
                if ( $_GET["select"] == 5){ $select3=" AND k_platbe > 0 "; }

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
          {
            $dotaz_source="SELECT * FROM vlastnici WHERE archiv = '1' AND id_cloveka = '$sql' ";
          }
          else  { 
            echo "Zadejte výraz k vyhledání.... <br>";
            exit; 
          }

          global $list;
          $list=$_GET["list"];

          $poradek="find=".$find."&find_id=".$find_id."&najdi=".$_GET["najdi"]."&select=".$_GET["select"]."&razeni=".$_GET["razeni"]."&razeni2=".$_GET["razeni2"];

          //vytvoreni objektu
          $listovani = new c_listing_vlastnici2("./vlastnici-archiv.php?".$poradek."&menu=1", 30, $list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $dotaz_source);

          if (($list == "")||($list == "1")){    //pokud není list zadán nebo je první
            $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
          }
          else
          {
            $bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
          }

          $interval=$listovani->interval;

          $dotaz_final=$dotaz_source." LIMIT ".$interval." OFFSET ".$bude_chybet." ";


          $listovani->listInterval();

          $ip = new vlastnikarchiv;
          // $vlastnik->conn_mysql = $conn_mysql;
          $ip->conn_pgsql = $db_ok2;

          $ip->vypis($sql,$co,$dotaz_final);

          vlastnikarchiv::vypis_tab(2);

          $listovani->listInterval();

        ?>

<!-- konec obsahu -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 
