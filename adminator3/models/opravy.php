<?php

class opravy
{
    var $conn_mysql;

    var $logger;

    function __construct($conn_mysql, $logger) {
        $this->conn_mysql = $conn_mysql;
        $this->logger = $logger;
    }

    function vypis_opravy()
    {

        $sql="SELECT * FROM opravy WHERE ( id_opravy > 0 ";

        $order=" ORDER BY datum_vlozeni DESC ";

        $sf = $sql." ) ".$order;

        try {
          $dotaz = $this->conn_mysql->query($sf);
        } catch (Exception $e) {
            $this->logger->addError("opravy\vypis_opravy mysql_query dotaz failed! Caught exception: " . $e->getMessage());
            return false;
        }

        $dotaz_radku = $dotaz->num_rows;

        if ( $dotaz_radku == 0){ echo "<tr><td colspan=\"".$pocet_bunek."\" >Žádné opravy v databázi neuloženy. </tr>"; }
        else
        {

            $zobrazeno_limit="0";

            while($data=$dotaz->fetch_array() )
            {

            if( $zobrazeno_limit >= $limit)
            {
              // prozatimni reseni limitu

              $exit ="ano";

            }

            $zobrazovat="ne";

            $zobrazeno="ne";
            $sekundarni_show="ne";
              
            $id_opravy=$data["id_opravy"];

            try {
              $dotaz_S1=$this->conn_mysql->query("SELECT * FROM opravy WHERE id_predchozi_opravy = '" . intval($id_opravy) . "' "); 
              $dotaz_radku_S1=$dotaz_S1->num_rows;
            } catch (Exception $e) {
                $this->logger->addError("opravy\vypis_opravy mysql_query dotaz_S1 failed! Caught exception: " . $e->getMessage());
                return false;
            }

            // zde zjistit jestli uz se zobrazilo
            for ($p = 0; $p < count($zobrazene_polozky); ++$p)
            {
              if( $zobrazene_polozky[$p] == $id_opravy){ $zobrazeno = "ano"; }
              else{ $zobrazeno = "ne"; }
            }

          if( $v_reseni_filtr == 1 )
          {  	    
            if ( ( ($data["v_reseni"] == 1) and ( $dotaz_radku_S1 == 0 ) ) )
            { $zobrazovat="ano"; }
            elseif ( $dotaz_radku_S1 > 0 )
            { 
              while($data_S1=$dotaz_S1->fetch_array() )
              { if ( $data_S1["v_reseni"] == 1 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } }
            } 	
          } // konec if v_reseni_filtr == 1
          elseif( $v_reseni_filtr == 0 ) 
          {          	    
            if( ( ($data["v_reseni"] == 0 ) and ( $dotaz_radku_S1 == 0 ) ) )
            { $zobrazovat="ano"; }
            elseif ( $dotaz_radku_S1 > 0 )
            { 
              while($data_S1=$dotaz_S1->fetch_array() )
              { 
                if ( $data_S1["v_reseni"] == 0 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } 
                else{ $zobrazovat="ne"; $sekundarni_show="ne"; }
              }
            }
            else
            { $zobrazovat="ne"; }
                
          } // konec elseif v_reseni_filtr == 0


          if( $vyreseno_filtr == 1)
          {
            // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
            if ( ( ($data["vyreseno"] == 1) and ( $dotaz_radku_S1 == 0 ) ) )
            { $zobrazovat="ano"; }
            elseif( $dotaz_radku_S1 > 0 )
            {
              while($data_S1=$dotaz_S1->fetch_array() )
              { if ( $data_S1["vyreseno"] == 1 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } }
            
            }
            else
            { $zobrazovat="ne"; }

          } // konec if vyreseno_filtr == 0
          elseif ( $vyreseno_filtr == 0 )
          {
            // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
            if ( ( ($data["vyreseno"] == 0) and ( $dotaz_radku_S1 == 0 ) ) )
            { $zobrazovat="ano"; }
            elseif( $dotaz_radku_S1 > 0 )
            {
              while($data_S1=$dotaz_S1->fetch_array() )
              { 
              if ( $data_S1["vyreseno"] == 1 ){ $zobrazovat="ne"; $sekundarni_show="ne"; } 
              else
              { $zobrazovat="ano"; $sekundarni_show="ano"; }
              
              }// konec while
            
            }// konec elseif dotaz_radku_S1

          } // konec elseif vyreseno_filrt == 0

              
          if ( ( $v_reseni_filtr == 99 and $vyreseno_filtr == 99 ) )
          { $zobrazovat="ano"; }

          if( ($zobrazovat == "ano" and $zobrazeno == "ne" and $exit != "ano" ) )
          {

          $zobrazene_polozky[]=$data["id_opravy"];

          $zobrazeno_limit++;

            $class="opravy-tab-line4";

          // zde zjistit jestli uz se vyresilo
          if( $dotaz_radku_S1 == 0 )
          { // rezim singl problemu
            if ( $data["vyreseno"] == 1 ){ $barva="green"; }
            elseif ( $data["v_reseni"] == 1 ){ $barva="orange"; }
            else{ $barva="red"; }

          } // if dotaz_radku_S1 == 0
          else
          { 
            while($data_S1=$dotaz_S1->fetch_array() )
            {
              if( $data_S1["vyreseno"] == 1 ){ $barva="green"; }
              elseif( $data_S1["v_reseni"] == 1 ){ $barva="orange"; } 
              else{ $barva="red"; }
            } 
          }

          //    $barva="red";
            
            echo "<tr>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_opravy"]."</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_predchozi_opravy"]."</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                $id_cloveka=$data["id_vlastnika"];

                $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
                $vlastnik_radku=pg_num_rows($vlastnik_dotaz);

                while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
                { 
                  $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
                  $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
                  $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
                  $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
              }

                if ( $archiv_vlastnik == 1 )
                { echo "<a href=\"vlastnici-archiv.php?find_id=".$data["id_vlastnika"]."\" "; }
                elseif ($firma_vlastnik == 1 )
                { echo "<a href=\"vlastnici2.php?find_id=".$data["id_vlastnika"]."\" "; }
                else
                { echo "<a href=\"vlastnici.php?find_id=".$data["id_vlastnika"]."\" "; }

          echo "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data["id_vlastnika"]."</a> \n\n";
                
          echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["datum_vlozeni"]."</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                if ( $data["priorita"] == 0) echo "Nízká";
                elseif ( $data["priorita"] == 1) echo "Normální";
                elseif ( $data["priorita"] == 2) echo "Vysoká";
                else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                    if ( $data["v_reseni"] == 0 ) echo "Ne";
                    elseif ( $data["v_reseni"] == 1 ) echo "Ano (".$data["v_reseni_kym"].") ";
                    else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                    if ( $data["vyreseno"] == 0 ) echo "Ne";
                    elseif ( $data["vyreseno"] == 1 ) echo "Ano (".$data["vyreseno_kym"].") ";
                    else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                if ( ( strlen($data["vlozil"]) > 1 ) ){ echo $data["vlozil"]; }
                else { echo "<br>"; }

                echo "</td>";

          echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
            <a href=\"opravy-zacit-resit.php?id_opravy=".$data["id_opravy"]."\" >začít řešit</a></td>";

          echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"opravy-index.php?typ=1&id_vlastnika=".$data["id_vlastnika"];

          if( $data["id_predchozi_opravy"] == 0){ echo "&id_predchozi_opravy=".$data["id_opravy"]; }
          else{ echo "&id_predchozi_opravy=".$data["id_predchozi_opravy"]; }

          echo "\" >vložit odpověď</a></td>";

            echo "</tr>";

            echo "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data["text"]."</td></tr>";

          } // konec if zobrazovat == ano

          if( ( $sekundarni_show == "ano" and $zobrazeno == "ne" ) )
          {

            // $zobrazene_polozky[]=$id_opravy;

            $dotaz_S2=mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_opravy' ");
            
            while($data_S2=mysql_fetch_array($dotaz_S2) )
            {
            
            // zde zjistit jestli uz se zobrazilo
          for ($p = 0; $p < count($zobrazene_polozky); ++$p)
          {
            if( $zobrazene_polozky[$p] == $id_opravy){ $zobrazeno = "ano"; }
            else{ $zobrazeno = "ne"; }
          }

            $zobrazene_polozky[]=$data_S2["id_opravy"];

            $id_opravy_S3=$data_S2["id_opravy"];
            
            $dotaz_S3=mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_opravy_S3' "); 
            $dotaz_radku_S3=mysql_num_rows($dotaz_S3);

            // neni jiste jestli barveni ma bejt zde

            // zde zjistit jestli uz se vyresilo
            if( $dotaz_radku_S3 == 0 )
            { // rezim singl problemu
              if ( $data_S2["vyreseno"] == 1 ){ $barva="green"; }
              elseif ( $data_S2["v_reseni"] == 1 ){ $barva="orange"; }
              else{ $barva="red"; }

            } // if dotaz_radku_S1 == 0
            else
            { 
              while($data_S3=mysql_fetch_array($dotaz_S3) )
              {
                if( $data_S3["vyreseno"] == 1 ){ $barva="green"; }
                elseif( $data_S3["v_reseni"] == 1 ){ $barva="orange"; } 
                else{ $barva="red"; }
              } 
            }

            if ( $zobrazeno == "ne" and $exit != "ano" )
            {    

              $zobrazeno_limit++;

              echo "<tr>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_opravy"]."</td>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_predchozi_opravy"]."</td>
                  <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

              $id_cloveka=$data["id_vlastnika"];

              $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
              $vlastnik_radku=pg_num_rows($vlastnik_dotaz);

              while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
              { 
                $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
                $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
                $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
                $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
              }

              if ( $archiv_vlastnik == 1 )
              { echo "<a href=\"vlastnici-archiv.php?find_id=".$data_S2["id_vlastnika"]."\" "; }
              elseif ($firma_vlastnik == 1 )
              { echo "<a href=\"vlastnici2.php?find_id=".$data_S2["id_vlastnika"]."\" "; }
              else
              { echo "<a href=\"vlastnici.php?find_id=".$data_S2["id_vlastnika"]."\" "; }

              echo "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data_S2["id_vlastnika"]."</a> \n\n";
                    
              echo "</td>";
                // echo "<td class=\"".$class."\" >".$data_S2["text"]."</td>";
                echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["datum_vlozeni"]."</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

                if ( $data_S2["priorita"] == 0) echo "Nízká";
                elseif ( $data_S2["priorita"] == 1) echo "Normální";
                elseif ( $data_S2["priorita"] == 2) echo "Vysoká";
                else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                    if ( $data_S2["v_reseni"] == 0 ) echo "Ne";
                    elseif ( $data_S2["v_reseni"] == 1 ) echo "Ano (".$data_S2["v_reseni_kym"].") ";
                    else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                    if ( $data_S2["vyreseno"] == 0 ) echo "Ne";
                    elseif ( $data_S2["vyreseno"] == 1 ) echo "Ano (".$data_S2["vyreseno_kym"].") ";
                    else echo "Nelze zjistit";

                echo "</td>
                <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
                if ( ( strlen($data_S2["vlozil"]) > 1 ) ){ echo $data_S2["vlozil"]; }
                else { echo "<br>"; }

                echo "</td>";

              echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
                <a href=\"opravy-zacit-resit.php?id_opravy=".$data_S2["id_opravy"]."\" >začít řešit</a></td>";

              echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"opravy-index.php?typ=1&id_vlastnika=".$data_S2["id_vlastnika"];

              if( $data_S2["id_predchozi_opravy"] == 0){ echo "&id_predchozi_opravy=".$data_S2["id_opravy"]; }
              else{ echo "&id_predchozi_opravy=".$data_S2["id_predchozi_opravy"]; }

              echo "\" >vložit odpověď</a></td>";
                    
              //	echo "<tr><td class=\"".$class."\" colspan=\"\" >".$data_S2["text"]."</td></tr>";

              echo "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data_S2["text"]."</td></tr>";

              echo "</tr>";
            
            } // konec if zobrazeno ne
            
          } // konec while2
            
          } // konec if sekundar == 1 

          } // konec while 1

        } // konec else radku == 0

    }//konec funkce vypis_opravy

} // konec tridy opravy
