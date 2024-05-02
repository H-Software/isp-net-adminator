<?php

class objekt_a2
{

    var $conn_pgsql;
    var $conn_mysql;

    var $logger;

    var $echo = true;

    var $csrf_html;

    var $listAllowedActionUpdate = false;

    var $listAllowedActionErase = false;

    var $allowedUnassignFromVlastnik = false;

    function vypis_tab($par)
    {
        $output = "";
        if($par == 1) { $output .= "<table border=\"0\" width=\"100%\" class=\"objekty-table\" >\n"; 
        }
        elseif ($par == 2) { $output .= "\n".'</table>'."\n";  
        }
        else  { $output .= "chybny vyber"; 
        }
   
        if($this->echo === true) {
            echo $output;
        }
        else{
            return $output;
        }
    }
   
    function vypis_tab_first_rows($mod_vypisu)
    {
        $output = "";

        $output .= '<tr>
      <td colspan="1"><b>dns </b></td>
      <td colspan="3"><b>ip adresa </b></td>
      <td><b>mac </b></td>
      <td><b>typ </b></td>';


        if($mod_vypisu == 2 ) { $output .= "<td align=\"center\" ><b>Číslo portu:</b></td>"; 
        }
        else
        { $output .= "<td><b>client ap </b></td>"; 
        } 
     
        $output .= '
          <td align="center" ><b>upravit</b></td>
          <td align="center" ><b>smazat</b></td>
          <td><b>třída </b></td>
      <td><b>Aktivní</b></td>
      <td><b>Test obj.</b></td>
      <td><b>Linka </b></td>
      <td><b>Omezení </b></td>';

        $output .= '</tr>';

        $styl = "border-bottom: 1px dashed black; ";

        $output .= "<tr style=\"color: grey; \"  >
          <td colspan=\"2\" style=\"".$styl."\" ><b>přípojný bod: </b></td>
          <td colspan=\"1\" style=\"".$styl."\" ><b>historie </b></td>
      <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>vlastník </b></td>
          <td colspan=\"2\" style=\"".$styl."\" ><b>mac klienta </b></td>
          <td colspan=\"1\" style=\"".$styl."\" ><b>ip rb </b></td>
          
          <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>přidal</b></td>
          <td colspan=\"1\" style=\"".$styl."\" align=\"center\" ><b>upravil </b></td>
      <td style=\"".$styl."\" >&nbsp;</td>
          <td colspan=\"3\" style=\"".$styl."\" ><b>Datum přidání </b></td>
          <td colspan=\"1\" style=\"".$styl."\" ><b>Reg. Form </b></td>
        
      </tr>";

        if($this->echo === true) {
            echo $output;
        }
        else{
            return $output;
        }
    }

    public static function select($es,$razeni)  
    {
        global $db_ok2;
        // co - co hledat, 1- podle dns, 2-podle ip
    
        $ds=pg_query(" SET DATESTYLE TO 'SQL, EUROPEAN' ");

        // prvne vyresime sekundarni select
        $se_id=$es;

        //global $se;
        global $order;

        if($se_id ==1 ) { $se=''; 
        }
        elseif($se_id==2 ) { $se=" AND typ LIKE '1' "; 
        }
        elseif($se_id==3 ) { $se=" AND typ LIKE '2' "; 
        }
        elseif($se_id==4 ) { $se=" AND typ LIKE '3' "; 
        }
        elseif($se_id==5 ) { $se=" AND id_tridy > 0 "; 
        }
        elseif($se_id==6 ) { $se=" AND verejna !=99 "; 
        }
        elseif($se_id==7 ) { $se=" AND id_cloveka is null "; 
        }
        elseif($se_id==8 ) { $se=" AND dov_net LIKE 'n' "; 
        }
        elseif($se_id==9 ) { $se=" AND sikana_status LIKE 'a' "; 
        }

        // tvoreni dotazu
        // $order=$_POST["razeni"];
 
        if ($razeni == 1 ) { $order=" order by dns_jmeno DESC"; 
        }
        elseif ($razeni == 2 ) { $order=" order by dns_jmeno ASC"; 
        }
        elseif ($razeni == 3 ) { $order=" order by ip DESC"; 
        }
        elseif ($razeni == 4 ) { $order=" order by ip ASC"; 
        }
        elseif ($razeni == 7 ) { $order=" order by mac DESC"; 
        }
        elseif ($razeni == 8 ) { $order=" order by mac ASC"; 
        }
        //# elseif ( $razeni == 9 ){ $order=" order by typ DESC"; }
        //# elseif ( $razeni == 10){ $order=" order by typ ASC"; }
        else { $order=" order by id_komplu ASC "; 
        }               
 
        $pole[]=$se;
        $pole[]=$order;
  
        return $pole;
  
    } //konec funkce select
 
    //zde funkce export
    public function export_vypis_odkaz()
    {

        $fp=fopen("export/objekty.xls", "w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor
        fputs($fp, "<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky
        fputs($fp, "<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

        $vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='objekty' ORDER BY ordinal_position ");

        while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
        { fputs($fp, "<td><b> ".$vysledek_array_pole[0]." </b></td> \n"); 
        }

        fputs($fp, "</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

        $vysledek = pg_query("SELECT * FROM objekty ORDER BY id_komplu ASC");

        while ($data=pg_fetch_array($vysledek) )
        {
            fputs($fp, "\n <tr>");

            fputs($fp, "<td> ".$data["id_komplu"]."</td> ");
            fputs($fp, "<td> ".$data["id_tridy"]."</td> ");
            fputs($fp, "<td> ".$data["id_cloveka"]."</td> ");
            fputs($fp, "<td> ".$data["dns_jmeno"]."</td> ");
            fputs($fp, "<td> ".$data["ip"]."</td> ");
            fputs($fp, "<td> ".$data["mac"]."</td> ");
            fputs($fp, "<td> ".$data["rra"]."</td> ");
            fputs($fp, "<td> ".$data["vezeni"]."</td> ");
            fputs($fp, "<td> ".$data["dov_net"]."</td> ");
            fputs($fp, "<td> ".$data["swz"]."</td> ");
            //     fputs($fp,"<td> ".$data["sc"]."</td> ");
            fputs($fp, "<td> ".$data["typ"]."</td> ");
            fputs($fp, "<td> ".$data["poznamka"]."</td> ");
            fputs($fp, "<td> ".$data["verejna"]."</td> ");
            fputs($fp, "<td> ".$data["ftp_update"]."</td> ");
            fputs($fp, "<td> ".$data["pridano"]."</td> ");
            fputs($fp, "<td> ".$data["id_nodu"]."</td> ");
            fputs($fp, "<td> ".$data["rb_mac"]."</td> ");
            fputs($fp, "<td> ".$data["rb_ip"]."</td> ");
            fputs($fp, "<td> ".$data["pridal"]."</td> ");
            fputs($fp, "<td> ".$data["upravil"]."</td> ");
            fputs($fp, "<td> ".$data["sikana_status"]."</td> ");
            fputs($fp, "<td> ".$data["sikana_cas"]."</td> ");
            fputs($fp, "<td> ".$data["sikana_text"]."</td> ");
            fputs($fp, "<td> ".$data["vip_snat"]."</td> ");
            fputs($fp, "<td> ".$data["vip_snat_lip"]."</td> ");

            fputs($fp, "</tr> \n ");
            // echo "vysledek_array: ".$vysledek_array[$i];

        }

        fputs($fp, "</table>");   // Zapíšeme do souboru konec tabulky
        fclose($fp);   // Zavřeme soubor

        if($this->echo === true ) {
            echo "<span style=\"padding-left: 25px; padding-right: 20px; \" >";
            echo "<a href=\"export\objekty.xls\">export dat</a></span>";
        }
        else {
            $output .= "<a href=\"export\objekty.xls\">export dat</a>";
        }

        return $output;
    } //konec funkce vypis odkaz
 
    public static function vypis_razeni_a2()
    {
 
        $input_value="1";
        $input_value2="2";

        for ($i=1; $i < 6 ; $i++):

            //vnejsi tab
            echo "<td>";

            //vnitrni tab
            echo "\n <table border=\"0\"><tr><td>";

            if($i=="3" or $i=="4" ) { echo ""; 
            }
            else
            {

                echo "\n\n <input type=\"radio\" ";
                if (($_GET["razeni"]== $input_value) ) { echo " checked "; 
                }
                echo "name=\"razeni\" value=\"".$input_value."\" onClick=\"form1.submit();\" > ";

                // obr, prvni sestupne -descent
                echo "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";
                if ($i!=5) { echo " | "; 
                }
                echo "</td> \n\n <td>";

                echo "<input type=\"radio\" ";
                if (($_GET["razeni"]== $input_value2) ) { echo " checked "; 
                }
                echo " name=\"razeni\" value=\"".$input_value2."\" onClick=\"form1.submit();\"> \n";

                // obr, druhy vzestupne - asc
                echo "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

            }

            // vnitrni tab
            echo "\n </td></tr></table> \n\n";

            $input_value=$input_value+2;
            $input_value2=$input_value2+2;

            // konec vnitrni tab
            echo "</td>";

        endfor;
 
    }
 
    function zjistipocet($mod,$id)
    {
        if ($mod == 1 ) //wifi sit ...
        {
            //prvne vyberem wifi tarify...
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");
      
            if($dotaz_f->num_rows < 1) {
                 return 0;
            }

            $i = 0;
            while( $data_f = $dotaz_f->fetch_array() )
            {
                if($i == 0 ) { $tarif_sql .= " AND ( "; 
                }
                if($i > 0 ) { $tarif_sql .= " OR "; 
                }
                
                 $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
         
                 $i++;
            }
                      
            if($i > 0 ) { $tarif_sql .= " ) "; 
            }
        }
        elseif ($mod == 2 ) //fiber sit ...
        { 
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");
      
            if($dotaz_f->num_rows < 1) {
                return 0;
            }

            $i = 0;
            while( $data_f = $dotaz_f->fetch_array() )
            {
                if($i == 0 ) { $tarif_sql .= " AND ( "; 
                }
                if($i > 0 ) { $tarif_sql .= " OR "; 
                }
                
                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
         
                $i++;
            }
                      
            if($i > 0 ) { $tarif_sql .= " ) "; 
            }    
        }

        $dotaz = pg_query("SELECT id_cloveka FROM objekty WHERE ( id_cloveka = '".intval($id)."' ".$tarif_sql." ) ");     
        $radku = pg_num_rows($dotaz);
  
        return $radku;
  
    } //konec funkce zjistipocet
 
    public function vypis($sql,$co,$id,$dotaz_final = "")
    {
        $db_ok2 = $this->conn_pgsql;

        $output = "";

        if (!$db_ok2) {
            echo "An error occurred. The connection with pgsql does not exist.\n";
            exit;
        }

        if ($co==3 ) //wifi sit ...vypis u vlastniku (dalsi pouziti nevim)
        { 
            //prvne vyberem wifi tarify...
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");
      
            $i = 0;
      
            while( $data_f = $dotaz_f->fetch_array() )
            {
                if($i == 0 ) { $tarif_sql .= "AND ( "; 
                }
                if($i > 0 ) { $tarif_sql .= " OR "; 
                }
                
                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
         
                $i++;
            }
                      
            if($i > 0 ) { $tarif_sql .= " ) "; 
            }
                     
            $dotaz=pg_query($db_ok2, "SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql); 
    
        }
        elseif ($co==4 ) //fiber sit ...vypis pouze u vlastniku
        { 
            $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");
      
            $i = 0;
      
            while( $data_f = $dotaz_f->fetch_array() )
            {
                if($i == 0 ) { $tarif_sql .= "AND ( "; 
                }
                if($i > 0 ) { $tarif_sql .= " OR "; 
                }
                
                $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
         
                $i++;
            }
                      
            if($i > 0 ) { $tarif_sql .= " ) "; 
            }
                  
            $dotaz=pg_query($db_ok2, "SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql); 
    
        }
        else
        { 
            $dotaz= pg_query($db_ok2, $dotaz_final); 
        }

        if($dotaz !== false) {
            $radku=pg_num_rows($dotaz);
        }
        else{
            echo("<div style=\"color: red;\">Dotaz selhal! ". pg_last_error($db_ok2). "</div>");
        }
  
        if ($radku==0) {
 
            //if( ( ( $co == 3 ) or ( $co == 4 ) ) )
  
            if($co == 3 or $co == 4 ) {
                $output .= "<tr><td colspan=\"9\" >";
                $output .= "<span style=\"color: #555555; \">Žádný objekt není přiřazen. </span></td></tr>";
            }
            else
            {
                $output .= "<tr><td colspan=\"8\" ><span style=\"color: red; \">Nenalezeny žádné odpovídající data dle hledaného \"".htmlspecialchars($sql)."\" ";
                // $output .= " (dotaz: ".$dotaz_final.") ";
                $output .= "</td></tr>";
            }
 
        }
        else
        {
 
            while (  $data=pg_fetch_array($dotaz) ) 
            {
                // $output .= $data[sloupec1]." ".$data[sloupec2]; 
                // $output .= "<br />";
   
                //    if( $data["id_tridy"] > 0 ){ $garant=1; }
                if($data["verejna"] <> 99 ) { $verejna=1; 
                }
   
                /*
                if ( $garant==1)
                {
                $id_tridy=$data["id_tridy"];
                //zjistime sirku pasma
                $dotaz_g = pg_exec($db_ok2, "SELECT * FROM tridy WHERE id_tridy = '$id_tridy' ");
   
                while (  $data_g=pg_fetch_array($dotaz_g) ) { $sirka=$data_g["sirka"]; }
                }
                */
         
                //zacatek rady a prvni bunka
                $output .= "\n <tr>"."<td class=\"tab-objekty2\">".$data["dns_jmeno"]."</td> \n\n";

                $pridano=$data["pridano"];

                // treti bunka - ip adresa
                if ($verejna==1) { 
                    if ($data["vip_snat"] == 1) { $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"orange\" >".$data["ip"]." </td> \n"; 
                    }
                    elseif($data["tunnelling_ip"] == 1) { $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#00CC33\" >".$data["ip"]." </td> \n"; 
                    }
                    else
                    { $output .= "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#FFFF99\" >".$data["ip"]." </td> \n"; 
                    }
                }
                else
                { $output .= "<td colspan=\"2\" class=\"tab-objekty2\">".$data["ip"]."</td> \n"; 
                }
    
                // druha bunka - pozn
                $output .= "<td class=\"tab-objekty2\" align=\"center\" ><span class=\"pozn\"> <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
                $output .= " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." , Vytvořeno: ".$pridano." ');\" ></span></td> \n";
           
                // 4-ta bunka - mac
                $output .= "<td class=\"tab-objekty2\">".$data["mac"]."</td> \n";
               
                // 5-ta typ
                if ($data["typ"] == 1 ) { $output .= "<td class=\"tab-objekty\">"."daně"."</td> \n"; 
                }
                elseif ($data["typ"] ==2 ) { $output .= "<td class=\"tab-objekty\" bgcolor=\"#008000\" ><font color=\"#FFFFFF\">"." free "."</font></td> \n"; 
                }
                elseif ($data["typ"] ==3 ) { $output .= "<td class=\"tab-objekty\" bgcolor=\"yellow\" >"." ap "."</td> \n"; 
                }
                else { $output .= "<td class=\"tab-objekty\" >Error </td> \n"; 
                }
    
                // rra - client ip -- CISLO portu
                $output .= "<td class=\"tab-objekty2\" align=\"center\" ><span style=\"\"> ";
    
                global $mod_vypisu; 
    
                if($mod_vypisu == 2) {
                    $output .= "".$data["port_id"]."";
                }
                else
                { 
                    if(( strlen($data["client_ap_ip"]) < 1 ) ) { $output .= "&nbsp;"; 
                    }
                    else { $output .= $data["client_ap_ip"]; 
                    }
                }
    
                $output .= "</span></td> \n";

                //oprava a mazani

                $update_mod_vypisu = $_GET["mod_vypisu"];
      
                $id_tarifu = $data["id_tarifu"];
      
                $dotaz_update = $this->conn_mysql->query("SELECT typ_tarifu FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");

                if($dotaz_update === false) {
                    if(is_object($this->logger)) {
                        $this->logger->info("objekt_a2\\vypis: dump var dotaz_update: " . var_export($dotaz_update, true));
                    }

                    $output .= "Chyba! Nelze specifikovat tarif! (query failed)";
                }
                else{

                    $rs_update = $dotaz_update->num_rows;

                    if($rs_update == 1 ) {
                        while($data_update = $dotaz_update->fetch_array())
                        { 
                            if($data_update["typ_tarifu"] == 1 ) { $update_mod_vypisu = 2; 
                            }
                            else
                            { $update_mod_vypisu = 1; 
                            }
                        }
                    }
                    else { 
                        $output .= "Chyba! Nelze specifikovat tarif! (wrong num_rows)";
                        if(is_object($this->logger)) {
                               $this->logger->info("objekt_a2\\vypis: dump var rs_update: " . var_export($rs_update, true));
                        } 
                    }
                }
  
                // 6-ta update
                if ($this->listAllowedActionUpdate === false ) { $output .= "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Upravit</td> \n"; 
                }
                else
                {
                    $output .= "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"/objekty/action\" >";
                    $output .= "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_komplu"]."\" >";
      
                    if(strlen($this->csrf_html) > 0) {
                        $output .= $this->csrf_html;
                    }
      
                    $output .= "<input type=\"hidden\" name=\"mod_objektu\" value=\"".$update_mod_vypisu."\" >";
      
                    $output .= "<input class=\"\" type=\"submit\" value=\"update\" >";
        
                    $output .= "</td></form> \n";
                }
     
                // 7 smazat     
                if ($this->listAllowedActionErase === false ) { $output .= "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Smazat</td>"; 
                }
                else
                { 
                    $output .= "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"objekty-erase.php\" >";
                    $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_komplu"]."\" >";
                    $output .= "<input class=\"\" type=\"submit\" value=\"smazat\" >";
    
                    $output .= "</td> </form> \n";   
                }
     
                // 8-ma typ objektu :)
                $id=$data["id_komplu"];
                $class_id=$data["id_tridy"];
    
                global $garant_akce;
    
                // generovani tridy    
                if($data["typ"] == 3) { $output .= ""; 
                }
                else 
                { 
                    {    $output .= "<td class=\"tab-objekty2\"><font color=\"red\">"." peasant "."</font></td> \n"; }      
                }

                // prirava promennych pro tresty a odmeny
                if($data["dov_net"] =="a" ) { $dov_net="<font color=\"green\">NetA</font>"; 
                }
                else{ $dov_net="<font color=\"orange\">NetN</font> \n"; 
                }
    
                if(preg_match("/a/", $data["sikana_status"]) ) { 
                    $sikana_status_s = "<span class=\"obj-link-sikana\" >".
                    "<a href=\"http://damokles.adminator.net:8009/index.php".
                    "?sc=".intval($data["sikana_cas"])."&st=".urlencode($data["sikana_text"])."\" target=\"_new\" >".
                    "Sikana-A (".$data["sikana_cas"].")</a></span>\n"; 
    
                } 
                else{ $sikana_status_s="<span style=\"color: green;\" >Sikana-N</span>"; 
                }

                //tresty a odmeny - 6 bunek
                if($data["typ"] == 3 ) { $output .= "<td class=\"tab-objekty2\" colspan=\"5\" bgcolor=\"yellow\" align=\"center\"> ap-čko jaxvine </td> \n"; 
                }
                else 
                { 
                    $output .= "<td class=\"tab-objekty2\" >".$dov_net."</td>";
     
                    //test objetktu
                    $output .= "<td class=\"tab-objekty2\" >";
    
                    if($update_mod_vypisu == 2 ) {
                        $output .= "<a href=\"objekty-test.php?id_objektu=".$data["id_komplu"]."\" >test</a>";
                    }
                    else
                    { $output .= "<br>"; 
                    }
      
                    $output .= "</td> \n";   
                    //zde tarif 2 gen.
                    $output .= "<td class=\"tab-objekty2\" >";
                    $id_tarifu = $data["id_tarifu"];
       
                    //dodelat klikatko pro sc
                    //{ $tarif="<span class=\"tarifsc\"><a href=\"https://trinity.simelon.net/monitoring/data/cat_sc.php?ip=".$data["ip"]."\" target=\"_blank\" >sc</a></span>"; } 
    
                    $tarif_f = $this->conn_mysql->query("SELECT barva, id_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");
                    $tarif_f_r = $tarif_f->num_rows;
              
                    if($tarif_f_r <> 1) { $output .= "<span style=\"font-weight: bold; color: red;\" >E</span>"; 
                    }
                    else
                    {
                        while($data_f = $tarif_f->fetch_array())
                        { 
                            $output .= "<span style=\"color: ".$data_f["barva"]."; \" >";
                            $output .= "<a href=\"/admin/tarify?id_tarifu=".$data_f["id_tarifu"]."\" >".$data_f["zkratka_tarifu"]."</a>";
    
                            $output .= "</span>\n";
                        }     
                    }
                    $output .= "</td>\n"; 
      
                    $output .= "<td class=\"tab-objekty2\" colspan=\"2\" >".$sikana_status_s."</td>\n";
                }
    
                $output .= "</tr>\n<tr>\n";
    
                // tady uz asi druhej radek :) 
                $output .= "<td class=\"tab-objekty\" colspan=\"2\" >"; 
    
                $id_nodu=$data["id_nodu"];
          
                $vysledek_bod = $this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id='".intval($id_nodu)."' ");
                $radku_bod = $vysledek_bod->num_rows;
                      
                if($radku_bod==0) { $output .= "<span style=\"color: gray; \">přípojný bod nelze zjistit </span>";
                } else
                {
                    while ($zaznam_bod=$vysledek_bod->fetch_array() )
                    { 
                        //pouze text 
                        //$output .= "<span class=\"objekty-2radka\">NOD: ".$zaznam_bod["jmeno"]."</span> "; 

                        $output .= "<span class=\"objekty-2radka objekty-odkaz\">NOD: ".
                        "<a href=\"/topology/node-list?find=".urlencode($zaznam_bod["jmeno"])."\" >".
                        $zaznam_bod["jmeno"]."</a></span> "; 
                    }
                }
    
                $output .= "</td>";
    
                // sem historii
                $output .= "<td class=\"tab-objekty\" ><span class=\"objekty-2radka\" style=\"\" > H: ";
                $output .= "<a href=\"/archiv-zmen?id=".$id."\" >".$id."</a>";
                $output .= " </span>";
    
                $output .= "</td> \n";
    
                // id vlastnika
                $output .= "<td class=\"tab-objekty\" align=\"center\" ><span class=\"objekty-2radka\" > \n";
     
                $id_cloveka=$data["id_cloveka"];
    
                $vlastnik_dotaz=pg_query("SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."'");
                $vlastnik_radku=pg_num_rows($vlastnik_dotaz);
                while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
                { $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
                }
    
                if ($archiv_vlastnik == 1) { $output .= "V: <a href=\"vlastnici-archiv.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span> </td> \n"; 
                }
                else
                { $output .= "V: <a href=\"/vlastnici2?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span></td> \n"; 
                }            
    
                if($update_mod_vypisu == 2 ) { $output .= "<td class=\"tab-objekty\" colspan=\"3\" > <br></td>";  
                }
                else
                {
                    if (!($co==3 ) ) {
                        $output .= "<td class=\"tab-objekty\" colspan=\"2\" > <span class=\"objekty-2radka\" >";
                        //if( (strlen($data["rb_mac"]) > 0) ){ $output .= $data["rb_mac"]; }
                        $output .= "&nbsp;";
                        $output .= "</span></td> \n";
    
                        //$output .= "<td><br>b</td>";
    
                        $output .= "<td class=\"tab-objekty\" colspan=\"1\" ><span class=\"objekty-2radka\" >";
                        //if( (strlen($data["rb_ip"]) > 0) ){ $output .= $data["rb_ip"]; }
                        $output .= "&nbsp;";
                        $output .= "</span></td> \n";
                    }
    
                }
                // kdo pridal a kdo naposledy upravil 
                $output .= "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
                if((strlen($data["pridal"]) > 0) ) { $output .= $data["pridal"]; 
                }
                else{ $output .= "<span style=\"color: #CC3366;\" >nezadáno</span>"; 
                }
                $output .= "</span></td> \n";
     
                $output .= "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
                if((strlen($data["upravil"]) > 0) ) { $output .= $data["upravil"]; 
                }
                else{ $output .= "<span style=\"color: #CC3366;\" >nezadáno</span>"; 
                }
                $output .= "</span></td> \n";
    
                $output .= "<td class=\"tab-objekty\" >&nbsp;</td> \n";
    
                // kdy se objekty pridal
                //prvne to orezem
                $orez= $pridano; 
                $orezano = explode(':', $orez); 
                $pridano_orez=$orezano[0].":".$orezano[1];
    
                $output .= "<td class=\"tab-objekty\" colspan=\"3\" ><span class=\"objekty-2radka\" >".$pridano_orez."</span></td>
    <td class=\"tab-objekty\" >
     <form method=\"POST\" action=\"/print/reg-form-pdf.php\" >
        <input type=\"hidden\" name=\"id_objektu\" value=\"".intval($data["id_komplu"])."\" >
	<input type=\"submit\" name=\"odeslano_form\" value=\"R.F.\">
     </form>
    </td>\n";
          
                //sem odendat
                if ($co==3 ) { 
    
                    if ($this->allowedUnassignFromVlastnik === true ) {
                        $output .= "<td colspan=\"4\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
                    }
                    else
                    {
                        $output .= "<td colspan=\"4\" style=\"font-size: 10px; font-family: arial; color: gray; \">
	<div style=\"text-align: center; \">odendat</div> </td> \n"; 
                    }
    
                }
                elseif($co==4 ) //opticky rezim
                {

                    if ($this->allowedUnassignFromVlastnik === true ) {
                        $output .= "<td colspan=\"\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
                    }
                    else
                    {
                        $output .= "<td colspan=\"\" style=\"font-size: 10px; font-family: arial; color: gray; \">
	<div style=\"text-align: center; \">odendat</div> </td> \n"; 
                    }
                }
     
                $output .= "</span>";
                // konec druhyho radku
                $output .= "</tr> \n";
     
                $verejna=0; 
                $garant=0;
   
            } // konec while
  
        } //konec else
   
        if($this->echo === true) {
            echo $output;
        }
        else{
            return $output;
        }

    } // konec funkce
   
}