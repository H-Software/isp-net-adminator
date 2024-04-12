<?php

class objekt
{

    var $conn_mysql;

	function __construct($conn_mysql)
    {
		$this->conn_mysql = $conn_mysql;
	}

    public function stbPrepareRender()
    {
        $odeslano = $_GET["odeslano"];
        $par_vlastnik = intval($_GET["par_vlastnik"]);
        $id_nodu = intval($_GET["id_nodu"]);
        $search = $_GET["search"];
        $order = intval($_GET["order"]);
        
        if( (strlen($_GET["list"]) > 0) )
        { $list = intval($_GET["list"]); }
        
        if( (strlen($_GET["id_stb"]) > 0) )
        { $id_stb = intval($_GET["id_stb"]); }
        
        $get_odkazy = "".urlencode("&par_vlastnik")."=".urlencode($par_vlastnik).
                "".urlencode("&id_nodu")."=".urlencode($id_nodu).
                "".urlencode("&search")."=".urlencode($search).
                "".urlencode("&list")."=".urlencode($list).
                "".urlencode("&odeslano")."=".urlencode($odeslano).
                "".urlencode("&id_stb")."=".urlencode($id_stb);
        
        //vytvoreni objektu
        $stb = new stb($this->conn_mysql);
        
        $stb->level = intval($level);
        
        if( $order > 0 ){
            $stb->order = $order;
        }
        
        if($id_nodu > 0){
            $stb->find_id_nodu = $id_nodu;
        }
        
        if( $par_vlastnik > 0 ){
            $stb->find_par_vlastnik = $par_vlastnik;
        }
        
        if( (strlen($search) > 0) ){
            $stb->find_search_string = $search;
        }
        
        if( $id_stb > 0 ){
            $stb->id_stb = $id_stb;
        }
        
         $stb->vypis_pocet_sloupcu = 8;
         
         echo "<div style=\"padding-top: 15px; padding-bottom: 15px; \" >
            <span style=\" padding-left: 5px; 
            font-size: 16px; font-weight: bold; \" >
            .:: Výpis Set-Top-Boxů ::. </span>
            
            <span style=\"padding-left: 25px; \" >
              <a href=\"objekty-stb-add.php\" >přidání nového stb objektu</a>
            </span>
               
                <span style=\"\" >
                  <a href=\"objekty-stb-add-portal.php\" >
                    <img src=\"/adminator2/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >
                  </a>
            </span>
            
               <span style=\"padding-left: 25px; \" >
                 <a href=\"#\" onclick=\"visible_change(objekty_stb_filter)\" >filtr/hledání</a>
               </span>
            
               <span style=\"padding-left: 25px; \" >
                 <a href=\"admin-login-iptv.php\" target=\"_new\" >aktivace funkcí IPTV portálu (přihlašení)</a>
               </span>
               
              </div>\n";
        
        $rs_select_nod = $stb->filter_select_nods();
        
        if( isset($rs_select_nod["error"]) ){
            
            echo "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" hlásí chybu: ";
            
            foreach ($rs_select_nod["error"] as $key => $val) {
                echo "#".$key.":<br> ".$val;
            }
            
            echo "</div>\n";
        }
        
        if( !is_array($rs_select_nod["data"]) ){
        
            echo "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" nevrací žádné relevatní data</div>\n";
        }
        
        echo "<form method=\"GET\" action=\"\" >";
        
        //filtr - hlavni okno
        if( $_GET["odeslano"] == "OK" ){
            $display = "visible";
        }
        else{
            $display = "none";
        }
        
         $stb->generate_sql_query();
        
         $paging_url = "?".urlencode("order")."=".$stb->order.$get_odkazy;
            
         $paging = new paging_global($conn_mysql, $paging_url, 20, $list, "<div class=\"text-listing2\" style=\"width: 1000px; text-align: center; padding-top: 10px; padding-bottom: 10px;\">", "</div>\n", $stb->sql_query);
                         
         $bude_chybet = ( (($list == "")||($list == "1")) ? 0 : ((($list-1) * $paging->interval)) );
         
         $interval = $paging->interval;
          
         $stb->sql_query = $stb->sql_query . " LIMIT ".$interval." OFFSET ".$bude_chybet." "; 
        
        echo "<div id=\"objekty_stb_filter\" style=\"width: 1000px; margin: 10px; display: ".$display."; padding: 10px; border: 1px solid gray; \" >";
        
        //vlastnik - bez
        echo "<div style=\"width: 150px; float: left;\" >".
                "přiřazeno k vlastníkovi: </div>";
        
        echo "<div style=\"float: left; \">".    
                "<select size=\"1\" name=\"par_vlastnik\" style=\"width: 70px;\" >".
                "<option value=\"0\" style=\"color: gray;\" >obojí</option>".
                "<option value=\"1\" "; if($par_vlastnik == 1) echo " selected "; echo ">Ano (spárované)</option>".
                "<option value=\"2\" "; if($par_vlastnik == 2) echo " selected "; echo ">Ne (nespárované)</option>".
                "</select>".    
               "</div>";
        
        //pripojnej bod
        echo "<div style=\"width: 100px; float: left; padding-left: 10px; \" >".
                "Přípojný bod: </div>\n";
        
        echo "<div style=\"float: left; padding-left: 10px; \">\n".    
                "<select size=\"1\" name=\"id_nodu\" >\n".
                "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n";
                
            foreach ($rs_select_nod["data"] as $nod_id => $nod_name) {
                    echo "<option value=\"".$nod_id."\" ";
                    
                    if($nod_id == $id_nodu) echo " selected ";
                    
                    echo " >".$nod_name."</option>\n";
            }
                    
            echo "</select>".
              "</div>\n";
        
        //tarif 
        echo "<div style=\"width: 50px; float: left; padding-left: 10px; \" >".
                "Tarif: </div>\n";
        
        echo "<div style=\"float: left; padding-left: 10px; \">\n".    
                "<select size=\"1\" name=\"id_tarifu\" >\n".
                "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n".
                "</select>\n".
              "</div>\n";
        
        //tlacitko
        echo "<div style=\"float: left; padding-left: 100%; width: 250px; text-align: right; padding-left: 10px; \" >".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";
        
        //oddelovac
        echo "<div style=\"clear: both; height: 5px; \"></div>\n";
        
        //druha radka
        echo "<div style=\"float: left; \" >Hledání: </div>\n";
        
        echo "<div style=\"float: left; padding-left: 20px; \" >".
            "<input type=\"text\" name=\"search\" value=\"".htmlspecialchars($search)."\" ></div>\n";
        
        echo "<div style=\"float: left; padding-left: 20px; \" >Id Stb: </div>\n";
        
        echo "<div style=\"float: left; padding-left: 20px; \" >".
            "<input type=\"text\" name=\"id_stb\" size=\"3\" value=\"".htmlspecialchars($id_stb)."\" ></div>\n";
        
        //tlacitko
        echo "<div style=\"float: left; padding-left: 10px; \" >".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";
        
        //oddelovac
        echo "<div style=\"clear: both; \"></div>\n";
        
        echo "</div>\n";
        
        echo "</form>\n";
        
        //listovani
         echo $paging->listInterval();
         
        
        //zacatek tabulky ... popis
        
        echo "<table border=\"0\" width=\"1000px\" style=\"padding-left: 10px; \" >";
        
        echo "
            <tr>\n";
            
             //popis
             echo "<td width=\"200px\" style=\"border-bottom: 1px dashed gray; \" >\n";
             echo "\t<div style=\"font-weight: bold; float: left; \">popis</div>\n";
             
             echo "\t<div style=\"float: left; padding-left: 55%; \">".
                    "<a href=\"?".urlencode("order")."=1".$get_odkazy."\">";
                
                if($order == 1){
                    echo "<img src=\"img2/sorting_a-z_hot.jpg\" width=\"20px\" alt=\"sorting_a-z-hot\" >";
                    }
                    else{
                    echo "<img src=\"img2/sorting_a-z_normal.jpg\" width=\"20px\" alt=\"sorting_a-z-normal\" >";        
                    }
               echo "</a>".
                    "</div>\n";
             
             echo "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                    "<a href=\"?".urlencode("order")."=2".$get_odkazy."\">";
                    
                    if($order == 2){
                    echo "<img src=\"img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
                    }
                    else{
                    echo "<img src=\"img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";        
                    }
                
                echo "</a>".
                    "</div>\n";
             
             echo "</td>\n";
             
             //ip adresa
             echo "<td style=\"border-bottom: 1px dashed gray;\" >\n";
             echo "\t<div style=\"font-weight: bold; float: left; \">IP adresa</div>\n";
             
             echo "\t<div style=\"float: left; padding-left: 20%; \">".
                    "<a href=\"?".urlencode("order=")."3".$get_odkazy."\">";
                     
                    if($order == 3){
                     echo "<img src=\"img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >"; 	    
                     }
                     else{	    
                     echo "<img src=\"img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >"; 	    
                     }
                     
                     echo "</a>".
                    "</div>\n";
             
             echo "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=4".$get_odkazy."\">";
                    
                    if($order == 4){	    
                     echo "<img src=\"img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
                     }
                     else{
                     echo "<img src=\"img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
                     }
                     
               echo "</a>".
                    "</div>\n";
             
             echo "</td>\n";
             
             //poznamka
             echo "<td style=\"border-bottom: 1px dashed gray;\" ><b>poznámka</b></td>\n";
             
             //mac adresa
             echo "<td style=\"border-bottom: 1px dashed gray; width: 168px; \" >";
                echo "\t<div style=\"font-weight: bold; float: left; \">MAC adresa</div>\n";
        
                echo "\t<div style=\"float: left; padding-left: 20%; \">".
                    "<a href=\"?".urlencode("order")."=5".$get_odkazy."\">";
                
                if($order == 5){
                    echo "<img src=\"img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
                }
                else{
                     echo "<img src=\"img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";        
                }
                
                echo "</a>".
                    "</div>\n";
                      
                 echo "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=6".$get_odkazy."\">";
                    
                if($order == 6){ 
                     echo "<img src=\"img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
                 }
                 else{
                     echo "<img src=\"img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
                 }
                     
                echo "</a>".
                    "</div>\n";
        
             echo "</td>\n";
        
             //uprava
             echo "<td style=\"border-bottom: 1px dashed gray;\" ><b>úprava</b></td>
            
             <td style=\"border-bottom: 1px dashed gray;\" ><b>smazat</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>test</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>tarif</b></td>
            
            </tr>\n\n";
            
            //2. radka
            echo "<tr>
             <td style=\"border-bottom: 1px solid black;\" >\n";
             echo "\t<div style=\"font-weight: bold; float: left; \">přípojný nod</div>\n";
            
             echo "\t<div style=\"float: left; padding-left: 32%; \">".
                    "<a href=\"?".urlencode("order")."=9".$get_odkazy."\">";
                     
                    if($order == 9){
                     echo "<img src=\"img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >"; 	    
                     }
                     else{	    
                     echo "<img src=\"img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >"; 	    
                     }
                     
                     echo "</a>".
                    "</div>\n";
        
             echo "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                    "<a href=\"?".urlencode("order=")."10".$get_odkazy."\">";
                    
                    if($order == 10){
                    echo "<img src=\"img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
                    }
                    else{
                    echo "<img src=\"img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";        
                    }
                
                echo "</a>".
                    "</div>\n";
             
             echo "</td>\n";
            
             //PUK
             echo "<td style=\"border-bottom: 1px solid black;\" >".
                   "\t<div style=\"font-weight: bold; float: left; \">PUK</div>\n";
        
             echo "\t<div style=\"float: left; padding-left: 43%; \">".
                    "<a href=\"?".urlencode("order")."=7".$get_odkazy."\" >";
                
             if($order == 7){ 
                echo "<img src=\"img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
             }
             else{
                echo "<img src=\"img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
             }
        
             echo "</a>".
                "</div>\n";
                     
             echo "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=8".$get_odkazy."\" >";
                    
             if($order == 8){ 
                 echo "<img src=\"img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
             }
             else{
                echo "<img src=\"img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
             }
             
             echo "</a>".
                    "</div>\n";
        
             echo "</td>\n";
                  
             echo "<td style=\"border-bottom: 1px solid black;\" ><b>id stb (historie)</b></td>
            
             <td style=\"border-bottom: 1px solid black;\" ><b>id človeka</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>switch port</b></td>
        
             <td colspan=\"2\" style=\"border-bottom: 1px solid black;\" ><b>datum vytvoření</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>reg. form</b></td>
                  
            </tr>\n";
        
         echo "<tr><td colspan=\"".$stb->vypis_pocet_sloupcu."\"><br></td></tr>\n";
         
         $stb->vypis();
          
         echo "</table>\n";
        
         echo $paging->listInterval();
        
    }
}